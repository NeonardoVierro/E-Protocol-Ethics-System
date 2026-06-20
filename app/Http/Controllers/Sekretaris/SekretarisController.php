<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\ProposalFile;
use App\Models\ProposalRevision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\ReviewFeedback;
use App\Models\EthicsDocument;
use App\Models\DocumentLog;

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
            'revisions',
        ]);

        $proposal->sekretaris_assignment = $proposal->assignments->first();

        $hasSubmittedRevision = $proposal->revisions->where('status', ProposalRevision::STATUS_SUBMITTED)->isNotEmpty();

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

        $latestReviewerAssignment = ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_REVIEWER)
            ->whereNotNull('sent_at')
            ->orderByDesc('sent_at')
            ->first();

        $latestSubmittedRevision = ProposalRevision::where('proposal_id', $proposal->id)
            ->where('status', ProposalRevision::STATUS_SUBMITTED)
            ->orderByDesc('created_at')
            ->first();

        $processingActionHidden = false;
        if ($latestReviewerAssignment) {
            if (! $latestSubmittedRevision || $latestReviewerAssignment->sent_at >= $latestSubmittedRevision->created_at) {
                $processingActionHidden = true;
            }
        }

        $logs = $proposal->documentLogs()->latest()->get();

        return view('sekretaris.manajemen-proposal.show', compact('proposal', 'reviewers', 'currentReviewer', 'reviewAssignments', 'logs', 'processingActionHidden'));
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

        // Create or refresh a review task for this reviewer
        $review = \App\Models\Review::where('proposal_id', $proposal->id)
            ->where('reviewer_id', $reviewer->id)
            ->orderByDesc('created_at')
            ->first();

        if ($review && $review->status === \App\Models\Review::STATUS_COMPLETED) {
            $review->update([
                'status' => \App\Models\Review::STATUS_ASSIGNED,
                'assigned_date' => now(),
                'due_date' => $request->due_date,
                'completed_date' => null,
            ]);
        } elseif (! $review) {
            \App\Models\Review::create([
                'proposal_id' => $proposal->id,
                'reviewer_id' => $reviewer->id,
                'status' => \App\Models\Review::STATUS_ASSIGNED,
                'assigned_date' => now(),
                'due_date' => $request->due_date,
            ]);
        } else {
            $review->update([
                'assigned_date' => now(),
                'due_date' => $request->due_date,
            ]);
        }

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

    public function hasilReview(Request $request)
    {
        $query = ReviewFeedback::with(['proposal.researcher', 'review.reviewer'])
            ->where('is_submitted', true)
            ->orderByDesc('submitted_at');

        // pencarian sederhana (judul proposal atau nama reviewer)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->whereHas('proposal', function ($p) use ($q) {
                    $p->where('title', 'like', "%{$q}%")
                      ->orWhere('code', 'like', "%{$q}%");
                })->orWhereHas('review.reviewer', function ($r) use ($q) {
                    $r->where('name', 'like', "%{$q}%");
                });
            });
        }

        $feedbacks = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => ReviewFeedback::where('is_submitted', true)->count(),
            'approved' => ReviewFeedback::where('is_submitted', true)->where('recommendation', ReviewFeedback::RECOMMENDATION_APPROVED)->count(),
            'revision' => ReviewFeedback::where('is_submitted', true)->where('recommendation', ReviewFeedback::RECOMMENDATION_REVISION)->count(),
            'rejected' => ReviewFeedback::where('is_submitted', true)->where('recommendation', ReviewFeedback::RECOMMENDATION_REJECTED)->count(),
        ];

        return view('sekretaris.hasil-review.index', compact('feedbacks', 'stats'));
    }

    public function hasilReviewShow(Proposal $proposal)
    {
        $proposal->load(['researcher', 'files' => function ($q) { $q->where('is_active', true); }]);

        $feedbacks = ReviewFeedback::with(['review.reviewer'])
            ->where('proposal_id', $proposal->id)
            ->where('is_submitted', true)
            ->orderByDesc('submitted_at')
            ->get();

        // aggregate counts for summary
        $summary = [
            'approved' => $feedbacks->where('recommendation', ReviewFeedback::RECOMMENDATION_APPROVED)->count(),
            'revision' => $feedbacks->where('recommendation', ReviewFeedback::RECOMMENDATION_REVISION)->count(),
            'rejected' => $feedbacks->where('recommendation', ReviewFeedback::RECOMMENDATION_REJECTED)->count(),
            'total_reviewers' => $feedbacks->count(),
        ];

        // Normalize feedback_text: if stored as JSON string, decode it for easier display in view
        $feedbacks->transform(function ($fb) {
            $parsed = null;
            if (is_array($fb->feedback_text)) {
                $parsed = $fb->feedback_text;
            } elseif (is_string($fb->feedback_text)) {
                $decoded = json_decode($fb->feedback_text, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $parsed = $decoded;
                } else {
                    // keep raw string under 'summary'
                    $parsed = ['summary' => $fb->feedback_text];
                }
            } else {
                $parsed = ['summary' => (string) $fb->feedback_text];
            }

            // Attach non-persistent property for the view
            $fb->parsed_feedback = $parsed;
            return $fb;
        });

        return view('sekretaris.hasil-review.show', compact('proposal', 'feedbacks', 'summary'));
    }

    public function keputusan(Request $request)
    {
        // Filter: hanya proposal yang di-assign ke sekretaris yg login
        $assignmentConstraint = function ($query) {
            $query->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                ->where('assigned_to', Auth::id())
                ->whereNotNull('sent_at');
        };
    
        $keputusan = Proposal::with(['researcher'])
            ->withCount('revisions')
            ->whereHas('assignments', $assignmentConstraint)
            ->whereHas('reviewFeedbacks', function ($query) {
                $query->where('is_submitted', true);
            })
            ->whereNull('decision_date')
            ->whereIn('status', [
                Proposal::STATUS_ON_REVIEW,
                Proposal::STATUS_REVISED,
            ])
            ->orderByRaw("FIELD(status,
                'on_review',
                'revised',
                'approved',
                'waiting_for_publish',
                'published',
                'rejected'
            )")
            ->orderByDesc('submission_date')
            ->get();
    
        // Proposal yang langsung dibuka modalnya (dari "Lanjut ke Keputusan")
        $autoOpenProposalId = $request->query('proposal_id');
        $autoOpenProposal   = null;
    
        if ($autoOpenProposalId) {
            $autoOpenProposal = $keputusan->firstWhere('id', (int) $autoOpenProposalId);
        }
    
        return view('sekretaris.keputusan.index', compact('keputusan', 'autoOpenProposalId', 'autoOpenProposal'));
    }
    
    // ── METHOD updateDecision() ───────────────────────────────────────
    public function updateDecision(Request $request)
    {
        $request->validate([
            'proposal_id'      => 'required|integer|exists:proposals,id',
            'status'           => 'required|in:approved,revised,rejected',
            'rejection_reason' => 'required_if:status,rejected,revised|nullable|string|max:1000',
        ], [
            'rejection_reason.required_if' => 'Alasan wajib diisi untuk keputusan revisi atau penolakan.',
        ]);
    
        $proposal = Proposal::findOrFail($request->proposal_id);
    
        // Cegah edit keputusan yang sudah dibuat (semua keputusan bersifat final)
        $finalStatuses = [Proposal::STATUS_APPROVED, Proposal::STATUS_REJECTED, Proposal::STATUS_REVISED, Proposal::STATUS_WAITING_FOR_PUBLISH, Proposal::STATUS_PUBLISHED];
        if (in_array($proposal->status, $finalStatuses) && $proposal->decision_date !== null) {
            return redirect()->route('sekretaris.keputusan')
                ->with('error', 'Keputusan untuk proposal ini sudah dibuat dan tidak dapat diubah. Jika peneliti mengirim revisi, akan dibuat dokumen versi baru (vol2).');
        }

        // Pastikan sekretaris yang login memang assigned ke proposal ini
        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (!$assigned) {
            abort(403, 'Anda tidak memiliki akses untuk memutuskan proposal ini.');
        }

        // Update status, decision date, dan rejection reason
        $proposal->status           = $request->status;
        $proposal->rejection_reason = in_array($request->status, ['rejected', 'revised'])
            ? $request->rejection_reason
            : null;
        $proposal->decision_date    = now();
        $proposal->save();

        // ── Simpan catatan sekretaris ke ProposalNote ──
        if ($request->rejection_reason) {
            \App\Models\ProposalNote::create([
                'proposal_id' => $proposal->id,
                'user_id'     => Auth::id(),
                'note_type'   => \App\Models\ProposalNote::TYPE_SECRETARY,
                'content'     => $request->rejection_reason,
            ]);
        }

        // ── Jika REVISED: buat record revision untuk peneliti submit revisi ──
        if ($request->status === 'revised') {
            // Hitung nomor revisi berikutnya
            $nextRevisionNumber = $proposal->revisions()->max('revision_number') + 1;

            \App\Models\ProposalRevision::create([
                'proposal_id'       => $proposal->id,
                'revision_number'   => $nextRevisionNumber,
                'revision_note'     => null,
                'requested_date'    => now(),
                'submitted_date'    => null,
                'status'            => 'requested',
                'file_id'           => null,
            ]);

            // Notifikasi ke peneliti untuk submit revisi
            \App\Models\Notification::create([
                'user_id' => $proposal->user_id,
                'title'   => 'Permintaan Revisi Proposal',
                'message' => 'Proposal "' . $proposal->title . '" memerlukan revisi. Silakan upload dokumen revisi (Vol.' . $nextRevisionNumber . ').',
                'type'    => \App\Models\Notification::TYPE_REVISION_REQUEST,
                'status'  => \App\Models\Notification::STATUS_UNREAD ?? 'unread',
                'data'    => json_encode([
                    'proposal_id'     => $proposal->id,
                    'revision_number' => $nextRevisionNumber,
                ]),
            ]);
        }
    
        // ── Jika APPROVED: buat draft Ethics Document ─────────────────
        if ($request->status === 'approved') {
            // Generate nomor dokumen sementara (bisa diubah admin nanti)
            $nomorDraft = 'DRAFT-EC-' . now()->format('Ymd') . '-' . str_pad($proposal->id, 4, '0', STR_PAD_LEFT);
    
            \App\Models\EthicsDocument::firstOrCreate(
                ['proposal_id' => $proposal->id],
                [
                    'document_number' => $nomorDraft,
                    'status'          => \App\Models\EthicsDocument::STATUS_DRAFT,
                    'file_path'       => '',
                    'original_name'   => '',
                    'notes'           => 'Draft otomatis dibuat saat proposal disetujui oleh sekretaris.',
                ]
            );
        }
    
        // ── Notifikasi ke peneliti (untuk approved dan rejected saja, revised sudah dihandle di atas) ──
        if ($request->status !== 'revised') {
            \App\Models\Notification::create([
                'user_id' => $proposal->user_id,
                'title'   => 'Keputusan Proposal: ' . match($request->status) {
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                },
                'message' => match($request->status) {
                    'approved' => 'Selamat! Proposal "' . $proposal->title . '" Anda telah disetujui oleh sekretaris.',
                    'rejected' => 'Proposal "' . $proposal->title . '" ditolak. Alasan: ' . $request->rejection_reason,
                },
                'type'   => \App\Models\Notification::TYPE_PROPOSAL_STATUS ?? 'proposal_status',
                'status' => \App\Models\Notification::STATUS_UNREAD ?? 'unread',
                'data'   => json_encode([
                    'proposal_id' => $proposal->id,
                    'status'      => $request->status,
                ]),
            ]);
        }
    
        return redirect()->route('sekretaris.keputusan')
            ->with('success', 'Keputusan untuk proposal "' . $proposal->title . '" berhasil disimpan.');
    }

    public function draftEthicalClearance()
    {
        // Ambil daftar ethics documents yang berstatus draft dari database
        $docs = EthicsDocument::with('proposal')
            ->where('status', EthicsDocument::STATUS_DRAFT)
            ->orderByDesc('created_at')
            ->get();

        // Map model collection ke array yang dipakai oleh view lama
        $drafts = $docs->map(function ($d) {
            return [
                'id' => $d->document_number ?? ('EC' . str_pad($d->id, 3, '0', STR_PAD_LEFT)),
                'proposal_id' => $d->proposal?->id ?? null,
                'judul' => $d->proposal?->title ?? ($d->original_name ?? 'Untitled'),
                'status' => $d->status,
                'tanggal' => $d->created_at?->toDateString() ?? now()->toDateString(),
            ];
        })->toArray();

        // Daftar admin aktif untuk dipilih sebagai penanggung jawab draft
        $admins = User::whereHas('roles', function ($q) {
                $q->where('name', 'admin');
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Daftar proposal yang berstatus approved dan assigned ke sekretaris yang login
                $approvedProposals = Proposal::whereHas('assignments', function ($q) {
                $q->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                  ->where('assigned_to', Auth::id())
                  ->whereNotNull('sent_at');
            })
                        ->where('status', Proposal::STATUS_APPROVED)
                        ->whereDoesntHave('ethicsDocument', function ($docQ) {
                            $docQ->where('status', EthicsDocument::STATUS_DRAFT)
                                 ->whereHas('documentLogs', function ($logQ) {
                                     $logQ->where('activity', DocumentLog::ACTIVITY_ASSIGN);
                                 });
                        })
                        ->with('researcher')
                        ->orderByDesc('submission_date')
                        ->get();

        return view('sekretaris.draf-ethical-clearance.new', compact('drafts', 'admins', 'approvedProposals'));
    }

    public function storeDraft(Request $request)
    {
        $request->validate([
            'proposal_id' => 'nullable|exists:proposals,id',
            'title' => 'required|string|max:1024',
            'principal_investigator' => 'required|string|max:255',
            'members' => 'nullable|string|max:2000',
            'institution' => 'required|string|max:255',
            'research_place' => 'required|string|max:255',
            'admin_id' => 'nullable|exists:users,id',
        ]);

        $payload = [
            'title' => $request->title,
            'principal_investigator' => $request->principal_investigator,
            'members' => $request->members,
            'institution' => $request->institution,
            'research_place' => $request->research_place,
            'admin_id' => $request->admin_id,
        ];

        // Simpan minimal ke kolom notes sebagai JSON agar data draft tetap tersedia
        $doc = EthicsDocument::updateOrCreate(
            ['proposal_id' => $request->proposal_id],
            [
                'document_number' => '',
                'ketua_id' => null,
                'status' => EthicsDocument::STATUS_DRAFT,
                'file_path' => '',
                'original_name' => '',
                'notes' => json_encode($payload),
            ]
        );

        return redirect()->route('sekretaris.draf-ethical-clearance')->with('success', 'Draft berhasil disimpan. Anda dapat mengedit atau mengirimkannya ke pemohon.');
    }

    public function sendDraft(Request $request, EthicsDocument $document)
    {
        // Menandai draft sebagai siap dikirimkan (kita simpan notifikasi ke peneliti)
        $document->update(['status' => EthicsDocument::STATUS_DRAFT]);

        if ($document->proposal && $document->proposal->user_id) {
            \App\Models\Notification::create([
                'user_id' => $document->proposal->user_id,
                'title' => 'Draft Ethical Clearance Tersedia',
                'message' => 'Draft sertifikat kelaikan etik untuk proposal "' . ($document->proposal->title ?? '—') . '" telah dibuat oleh sekretariat.',
                'type' => \App\Models\Notification::TYPE_PROPOSAL_STATUS ?? 'proposal_status',
                'status' => \App\Models\Notification::STATUS_UNREAD ?? 'unread',
                'data' => json_encode(['document_id' => $document->id, 'proposal_id' => $document->proposal->id ?? null]),
            ]);
        }

        return redirect()->route('sekretaris.draf-ethical-clearance')->with('success', 'Draft berhasil dikirimkan ke pemohon (notifikasi dibuat).');
    }

    public function sendToAdmin(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|exists:proposals,id',
            'admin_id' => 'required|exists:users,id',
        ]);

        $proposal = Proposal::findOrFail($request->proposal_id);

        // Find or create ethics document for this proposal
        $document = EthicsDocument::firstOrCreate(
            ['proposal_id' => $proposal->id],
            [
                'document_number' => '',
                'ketua_id' => null,
                'status' => EthicsDocument::STATUS_DRAFT,
                'file_path' => '',
                'original_name' => '',
                'notes' => 'Draft created by sekretariat and sent to admin.',
            ]
        );

        // Assign to admin (ketua_id used here as handler)
        $document->ketua_id = $request->admin_id;
        $document->status = EthicsDocument::STATUS_DRAFT;
        // add admin assignment into notes (merge if JSON)
        try {
            $notes = json_decode($document->notes ?: '{}', true);
            if (!is_array($notes)) $notes = ['notes' => (string)$document->notes];
        } catch (\Throwable $e) {
            $notes = ['notes' => (string)$document->notes];
        }
        $notes['assigned_admin_id'] = $request->admin_id;
        $notes['assigned_at'] = now()->toDateTimeString();
        $document->notes = json_encode($notes);
        $document->save();

        // Notify the admin
        \App\Models\Notification::create([
            'user_id' => $request->admin_id,
            'title' => 'Draft Ethical Clearance Diterima',
            'message' => 'Draft untuk proposal "' . ($proposal->title ?? '—') . '" telah dikirim untuk ditinjau.',
            'type' => \App\Models\Notification::TYPE_DOCUMENT_READY,
            'status' => \App\Models\Notification::STATUS_UNREAD,
            'data' => json_encode(['document_id' => $document->id, 'proposal_id' => $proposal->id]),
        ]);

        // Create a document log entry so it appears in archives/log
        DocumentLog::create([
            'ethics_document_id' => $document->id,
            'proposal_id' => $proposal->id,
            'user_id' => Auth::id(),
            'activity' => DocumentLog::ACTIVITY_ASSIGN,
            'ip_address' => request()->ip(),
            'description' => 'Draft dikirim ke admin ID ' . $request->admin_id,
            'metadata' => ['assigned_admin_id' => $request->admin_id],
        ]);

        return response()->json(['status' => 'ok', 'message' => 'Draft berhasil dikirim ke admin.']);
    }

    public function arsipDokumen()
    {
        // Show ethics documents that were assigned/sent to admin. Some legacy rows have
        // non-JSON `notes`, so we also include docs that have a related DocumentLog
        // of type 'assign'. Paginate results (10 per page).
        $documents = EthicsDocument::with('proposal', 'ketua')
            ->where(function ($q) {
                $q->whereRaw("JSON_VALID(notes) = 1 AND JSON_EXTRACT(notes, '$.assigned_admin_id') IS NOT NULL")
                  ->orWhereExists(function ($sub) {
                      $sub->select(\DB::raw(1))
                          ->from('document_logs')
                          ->whereColumn('document_logs.ethics_document_id', 'ethics_documents.id')
                          ->where('document_logs.activity', DocumentLog::ACTIVITY_ASSIGN);
                  });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('sekretaris.arsip-dokumen.index', compact('documents'));
    }

    public function arsip()
    {
        $documents = EthicsDocument::with('proposal', 'ketua')
            ->where(function ($q) {
                $q->whereRaw("JSON_VALID(notes) = 1 AND JSON_EXTRACT(notes, '$.assigned_admin_id') IS NOT NULL")
                  ->orWhereExists(function ($sub) {
                      $sub->select(\DB::raw(1))
                          ->from('document_logs')
                          ->whereColumn('document_logs.ethics_document_id', 'ethics_documents.id')
                          ->where('document_logs.activity', DocumentLog::ACTIVITY_ASSIGN);
                  });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('sekretaris.arsip-dokumen.index', compact('documents'));
    }

    public function persetujuanTtd()
    {
        // show documents that need persetujuan / tanda tangan
        $docs = EthicsDocument::with('proposal', 'ketua')
            ->where('status', EthicsDocument::STATUS_DRAFT)
            ->orderByDesc('created_at')
            ->get();

        return view('sekretaris.persetujuan-ttd.index', compact('docs'));
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
