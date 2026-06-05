<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\ProposalFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class SekretarisController extends Controller
{
    public function dashboard()
    {
        $data = [
            'total_proposal' => 24,
            'pending' => 7,
            'approved' => 12,
            'rejected' => 5,
        ];
        return view('sekretaris.dashboard', $data);
    }

    public function manajemenProposal(Request $request)
    {
        $status = $request->query('status', 'in_process');
        $search = $request->query('search');

        $statusMap = [
            'in_process' => [Proposal::STATUS_IN_PROCESS],
            'on_review' => [Proposal::STATUS_ON_REVIEW],
            'revision_required' => [],
            'revised' => [Proposal::STATUS_REVISED],
            'approved' => [Proposal::STATUS_APPROVED],
            'disapproved' => [Proposal::STATUS_REJECTED],
        ];

        $assignmentConstraint = function ($query) {
            $query->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                  ->where('assigned_to', Auth::id())
                  ->whereNotNull('sent_at');
        };

        $baseQuery = Proposal::with([
            'researcher',
            'files' => function ($query) {
                $query->where('is_active', true);
            },
            'assignments' => $assignmentConstraint,
            'assignments.assignedBy',
        ])
            ->whereHas('assignments', $assignmentConstraint);

        $statusCounts = [
            'in_process' => (clone $baseQuery)->whereIn('status', $statusMap['in_process'])->count(),
            'on_review' => (clone $baseQuery)->whereIn('status', $statusMap['on_review'])->count(),
            'revision_required' => 0,
            'revised' => (clone $baseQuery)->whereIn('status', $statusMap['revised'])->count(),
            'approved' => (clone $baseQuery)->whereIn('status', $statusMap['approved'])->count(),
            'disapproved' => (clone $baseQuery)->whereIn('status', $statusMap['disapproved'])->count(),
        ];

        if ($search) {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('nama_peneliti', 'like', "%{$search}%")
                      ->orWhereHas('researcher', function ($query) use ($search) {
                          $query->where('name', 'like', "%{$search}%");
                      });
            });
        }

        if (array_key_exists($status, $statusMap) && ! empty($statusMap[$status])) {
            $baseQuery->whereIn('status', $statusMap[$status]);
        } elseif ($status === 'revision_required') {
            $baseQuery->whereRaw('0 = 1');
        }

        $proposals = $baseQuery
            ->withCount('revisions')
            ->orderByDesc('submission_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($proposal) {
                $activeFilesCount = $proposal->files->count();
                $proposal->files_count = $activeFilesCount;
                $proposal->has_documents = $activeFilesCount > 0;
                $proposal->sekretaris_assignment = $proposal->assignments->first();

                return $proposal;
            });

        return view('sekretaris.manajemen-proposal.index', compact('proposals', 'statusCounts', 'status', 'search'));
    }

    public function showProposal(Proposal $proposal)
    {
        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        $proposal->load([
            'researcher',
            'files' => function ($query) {
                $query->where('is_active', true);
            },
            'assignments' => function ($q) {
                $q->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                  ->where('assigned_to', Auth::id())
                  ->whereNotNull('sent_at');
            },
            'assignments.assignedBy',
            'reviews' => function ($q) {
                $q->with('feedback', 'reviewer');
            },
        ]);

        $proposal->sekretaris_assignment = $proposal->assignments->first();

        $reviewers = User::whereHas('roles', function ($q) {
                $q->where('name', 'reviewer');
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $currentReviewer = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_REVIEWER)
            ->with('assignedTo')
            ->latest()
            ->first();

        $reviewAssignments = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_REVIEWER)
            ->with(['assignedTo', 'assignedBy'])
            ->get();

        $logs = $proposal->documentLogs()->latest()->get();

        return view('sekretaris.manajemen-proposal.show', compact('proposal', 'reviewers', 'currentReviewer', 'reviewAssignments', 'logs'));
    }

    public function updateReviewType(Request $request, Proposal $proposal)
    {
        $request->validate([
            'review_type' => 'required|in:' . implode(',', [
                Proposal::REVIEW_EXEMPTED,
                Proposal::REVIEW_EXPEDITED,
                Proposal::REVIEW_FULL_BOARD,
            ]),
        ]);

        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        $proposal->update(['review_type' => $request->review_type]);

        return redirect()->route('sekretaris.proposal.show', $proposal)
            ->with('success', 'Jenis review proposal berhasil diperbarui.');
    }

    public function assignReviewerToProposal(Request $request, Proposal $proposal)
    {
        $request->validate([
            'reviewer_id' => 'required|exists:users,id',
        ]);

        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        $reviewer = User::whereHas('roles', function ($q) {
                    $q->where('name', 'reviewer');
                })
                ->where('status', 'active')
                ->findOrFail($request->reviewer_id);

        $assignment = ProposalAssignment::create([
            'proposal_id' => $proposal->id,
            'assigned_by' => Auth::id(),
            'assigned_to' => $reviewer->id,
            'role'        => ProposalAssignment::ROLE_REVIEWER,
            'sent_at'     => now(),
        ]);

        $proposal->update(['status' => Proposal::STATUS_ON_REVIEW]);

        // Record activity log for assignment to reviewer
        \App\Models\DocumentLog::create([
            'proposal_id' => $proposal->id,
            'user_id'     => Auth::id(),
            'activity'    => \App\Models\DocumentLog::ACTIVITY_ASSIGN,
            'description' => 'Reviewer assigned: ' . $reviewer->name,
            'metadata'    => [
                'reviewer_id' => $reviewer->id,
            ],
        ]);

        return redirect()->route('sekretaris.proposal.show', $proposal)
            ->with('success', 'Reviewer berhasil dipilih dan ditandatangani.');
    }

    public function sendProposalToReviewer(Request $request, Proposal $proposal)
    {
        $request->validate([
            'review_type' => 'required|in:' . implode(',', [
                Proposal::REVIEW_EXEMPTED,
                Proposal::REVIEW_EXPEDITED,
                Proposal::REVIEW_FULL_BOARD,
            ]),
            'reviewer_id' => 'required|exists:users,id',
            'due_date'    => 'required|date|after_or_equal:today',
            'notes'       => 'nullable|string|max:1000',
            'comment_to_review' => 'nullable|string|max:1000',
        ]);

        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        $reviewer = User::whereHas('roles', function ($q) {
                $q->where('name', 'reviewer');
            })
            ->where('status', 'active')
            ->findOrFail($request->reviewer_id);

        $proposal->update(['review_type' => $request->review_type]);

        $assignment = ProposalAssignment::create([
            'proposal_id' => $proposal->id,
            'assigned_by' => Auth::id(),
            'assigned_to' => $reviewer->id,
            'role'        => ProposalAssignment::ROLE_REVIEWER,
            'notes'       => $request->notes,
            'due_date'    => $request->due_date,
            'comment_to_review' => $request->comment_to_review,
            'sent_at'     => now(),
        ]);

        $proposal->update(['status' => Proposal::STATUS_ON_REVIEW]);

        // Create reviewer notification
        \App\Models\Notification::create([
            'user_id' => $reviewer->id,
            'title' => 'Penugasan Review Baru',
            'message' => 'Anda telah ditugaskan untuk mereview proposal: ' . $proposal->title,
            'type' => \App\Models\Notification::TYPE_REVIEW_ASSIGNMENT,
            'status' => \App\Models\Notification::STATUS_UNREAD,
            'data' => [
                'proposal_id' => $proposal->id,
                'review_type' => $request->review_type,
                'due_date' => $request->due_date,
            ],
        ]);

        // Record activity log for assignment to reviewer
        \App\Models\DocumentLog::create([
            'proposal_id' => $proposal->id,
            'user_id'     => Auth::id(),
            'activity'    => \App\Models\DocumentLog::ACTIVITY_ASSIGN,
            'description' => 'Submission assigned to reviewer ' . $reviewer->name,
            'metadata'    => [
                'reviewer_id' => $reviewer->id,
                'review_type' => $request->review_type,
                'notes'       => $request->notes ?? null,
                'due_date'    => $request->due_date ?? null,
                'comment_to_review' => $request->comment_to_review ?? null,
            ],
        ]);

        return redirect()->route('sekretaris.proposal.show', $proposal)
            ->with('success', 'Proposal berhasil dikirim ke reviewer. Pastikan informasi review dan reviewer sudah benar.');
    }

    public function activityLogs(Proposal $proposal)
    {
        $logs = $proposal->documentLogs()->latest()->get();
        return view('sekretaris.manajemen-proposal._activity_logs', compact('logs', 'proposal'));
    }

    public function viewProposalFile(ProposalFile $file)
    {
        $proposal = $file->proposal;
        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($file->file_path));
    }

    public function downloadProposalFile(ProposalFile $file)
    {
        $proposal = $file->proposal;
        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404);
        }

        return response()->download(Storage::disk('public')->path($file->file_path), $file->original_name);
    }

    public function assignReviewer()
    {
        $proposals = [
            ['id' => 'P001', 'judul' => 'Studi Etika AI', 'reviewer_terpilih' => null],
            ['id' => 'P002', 'judul' => 'Penelitian Klinis', 'reviewer_terpilih' => null],
        ];
        $reviewers = ['Dr. Andi', 'Prof. Siti', 'Dr. Budi', 'Prof. Joko'];
        return view('sekretaris.assign-reviewer.index', compact('proposals', 'reviewers'));
    }

    public function hasilReview()
    {
        $reviews = [
            ['proposal_id' => 'P001', 'judul' => 'Studi Etika AI', 'reviewer' => 'Dr. Andi', 'feedback' => 'Revisi minor, perbaiki metodologi', 'tanggal' => '2025-05-03'],
            ['proposal_id' => 'P002', 'judul' => 'Penelitian Klinis', 'reviewer' => 'Prof. Siti', 'feedback' => 'Diterima dengan catatan', 'tanggal' => '2025-05-04'],
        ];
        return view('sekretaris.hasil-review.index', compact('reviews'));
    }

    public function keputusan()
    {
        $keputusan = Proposal::with('researcher')
            ->orderByDesc('submission_date')
            ->orderByDesc('created_at')
            ->get();

        return view('sekretaris.keputusan.index', compact('keputusan'));
    }

    public function updateDecision(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|integer|exists:proposals,id',
            'status' => 'required|in:approved,revised,rejected',
        ]);

        $proposal = Proposal::findOrFail($request->proposal_id);
        $proposal->updateStatus($request->status);

        return redirect()->route('sekretaris.keputusan')
            ->with('success', 'Status keputusan proposal berhasil diperbarui.');
    }

    public function draftEthicalClearance()
    {
        $drafts = [
            ['id' => 'EC001', 'proposal_id' => 'P002', 'judul' => 'Penelitian Klinis', 'status' => 'draft', 'tanggal' => '2025-05-10'],
        ];
        return view('sekretaris.draf-ethical-clearance.index', compact('drafts'));
    }

    public function arsipDokumen()
    {
        $arsip = [
            ['nama' => 'Proposal P001 - Studi Etika AI', 'tipe' => 'PDF', 'tanggal' => '2025-04-01', 'ukuran' => '2.3 MB'],
            ['nama' => 'Ethical Clearance EC001', 'tipe' => 'PDF', 'tanggal' => '2025-05-12', 'ukuran' => '1.1 MB'],
            ['nama' => 'Surat Tugas Reviewer P002', 'tipe' => 'DOCX', 'tanggal' => '2025-05-02', 'ukuran' => '0.5 MB'],
        ];
        return view('sekretaris.arsip-dokumen.index', compact('arsip'));
    }

    public function arsip()
    {
        $arsip = [
            ['nama' => 'Proposal P001 - Studi Etika AI', 'tipe' => 'PDF', 'tanggal' => '2025-04-01', 'ukuran' => '2.3 MB'],
            ['nama' => 'Ethical Clearance EC001', 'tipe' => 'PDF', 'tanggal' => '2025-05-12', 'ukuran' => '1.1 MB'],
            ['nama' => 'Surat Tugas Reviewer P002', 'tipe' => 'DOCX', 'tanggal' => '2025-05-02', 'ukuran' => '0.5 MB'],
        ];
        return view('sekretaris.arsip-dokumen.index', compact('arsip'));
    }

    public function userManagement()
    {
        $pendingUsers = User::where('status', 'pending')->get();
        return view('sekretaris.user-management.index', compact('pendingUsers'));
    }

    public function activateUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        // Buat notifikasi untuk user
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Akun Anda Telah Diaktivasi',
            'message' => 'Selamat! Akun Anda telah diaktivasi oleh sekretariat. Anda sekarang dapat login dan mengajukan ethical clearance.',
            'status' => 'unread',
            'type' => Notification::TYPE_ACCOUNT_ACTIVATION,
            'data' => json_encode(['activated_at' => now()->toDateTimeString()]),
        ]);

        return redirect()->route('sekretaris.user-management')->with('success', 'Akun berhasil diaktifkan dan notifikasi telah dikirim ke peneliti.');
    }
}
