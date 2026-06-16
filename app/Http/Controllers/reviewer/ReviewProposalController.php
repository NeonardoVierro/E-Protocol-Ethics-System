<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\ProposalFile;
use App\Models\Review;
use App\Models\ReviewFeedback;
use App\Models\Notification;
use App\Models\User;
use App\Models\DocumentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewProposalController extends Controller
{
    /**
     * Menampilkan halaman review untuk proposal tertentu.
     * Jika tidak ada parameter, tampilkan form kosong atau pilih proposal terlebih dahulu.
     */
    public function index(Request $request)
    {
        $proposalId = $request->query('id');

        $proposal = null;
        if ($proposalId) {
            $proposal = Proposal::with(['files' => function ($query) {
                $query->where('is_active', true);
            }])->find($proposalId);
        }

        if (! $proposal) {
            $proposal = Proposal::with(['files' => function ($query) {
                $query->where('is_active', true);
            }])
                ->whereIn('status', [Proposal::STATUS_NEW, Proposal::STATUS_IN_PROCESS, Proposal::STATUS_ON_REVIEW, Proposal::STATUS_REVISED])
                ->orderByDesc('submission_date')
                ->orderByDesc('created_at')
                ->first();
        }

        return view('reviewer.review-proposal.index', compact('proposal'));
    }

    /**
     * Menampilkan detail proposal untuk ditinjau dengan layout rinci.
     */
    public function show($id)
    {
        $proposal = Proposal::with([
            'researcher',
            'files' => function ($query) {
                $query->where('is_active', true);
            },
            'reviews' => function ($query) {
                $query->with('feedback', 'reviewer');
            },
            'assignments' => function ($query) {
                $query->where('role', ProposalAssignment::ROLE_REVIEWER)
                      ->with('assignedBy', 'assignedTo');
            },
            'revisions' => function ($q) {
                $q->with('file')->orderByDesc('revision_number');
            },
        ])->findOrFail($id);

        // Load existing review feedback for current user
        $draftReview = Review::where('proposal_id', $id)
            ->where('reviewer_id', Auth::id())
            ->with('feedback')
            ->first();

        $draftFeedback = null;
        if ($draftReview) {
            $draftFeedback = $draftReview->feedback;
        }

        // Indicate if there are any revisions (requested or submitted) for the view
        $hasRevisions = $proposal->revisions && $proposal->revisions->isNotEmpty();

        return view('reviewer.review-proposal.show', compact('proposal', 'draftReview', 'draftFeedback', 'hasRevisions'));
    }

    public function downloadProposalFile(ProposalFile $file)
    {
        $proposal = $file->proposal;
        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_REVIEWER)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        if (! Storage::disk('public')->exists($file->file_path)) {
            abort(404);
        }

        return response()->download(Storage::disk('public')->path($file->file_path), $file->original_name);
    }

    public function previewProposalFile(ProposalFile $file)
    {
        $proposal = $file->proposal;
        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_REVIEWER)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        if (! Storage::disk('public')->exists($file->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($file->file_path));
    }

    /**
     * Menyimpan hasil review (submit review atau simpan sebagai draft).
     */
    public function store(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|integer|exists:proposals,id',
            'save_mode' => 'required|in:draft,submit',
            'autonomy' => 'nullable|string|max:2000',
            'beneficence' => 'nullable|string|max:2000',
            'justice' => 'nullable|string|max:2000',
            'general_comments' => $request->input('save_mode') === 'submit' ? 'required|string|min:10|max:4000' : 'nullable|string|max:4000',
            'recommendation' => $request->input('save_mode') === 'submit' ? 'required|in:approved,revision,rejected' : 'nullable|in:approved,revision,rejected',
        ]);

        $proposal = Proposal::findOrFail($request->proposal_id);
        $isSubmit = $request->input('save_mode') === 'submit';

        // Use firstOrCreate to avoid race conditions creating duplicate reviews
        $review = Review::firstOrCreate(
            ['proposal_id' => $proposal->id, 'reviewer_id' => Auth::id()],
            [
                'status' => $isSubmit ? Review::STATUS_COMPLETED : Review::STATUS_IN_PROGRESS,
                'assigned_date' => now(),
                'due_date' => now()->addDays(7),
                'completed_date' => $isSubmit ? now() : null,
            ]
        );

        // Ensure status is updated for existing review
        if ($review->wasRecentlyCreated === false) {
            $review->update([
                'status' => $isSubmit ? Review::STATUS_COMPLETED : Review::STATUS_IN_PROGRESS,
                'completed_date' => $isSubmit ? now() : null,
            ]);
        }

        $feedbackData = json_encode([
            'autonomy' => $request->input('autonomy'),
            'beneficence' => $request->input('beneficence'),
            'justice' => $request->input('justice'),
            'general_comments' => $request->input('general_comments'),
        ], JSON_UNESCAPED_UNICODE);

        $reviewFeedback = ReviewFeedback::firstOrNew([
            'review_id' => $review->id,
            'proposal_id' => $proposal->id,
        ]);

        $reviewFeedback->feedback_text = $feedbackData;
        $reviewFeedback->recommendation = $request->input('recommendation');
        $reviewFeedback->is_submitted = $isSubmit;
        $reviewFeedback->submitted_at = $isSubmit ? now() : null;
        $reviewFeedback->save();

        if ($isSubmit) {
            // Pastikan sekretaris mendapatkan proposal ini sebagai bahan keputusan,
            // tetapi rekomendasi reviewer tidak langsung menjadi keputusan final sekretaris.
            $this->assignProposalToSecretary($proposal, $reviewFeedback->recommendation);

            // Status proposal hanya berubah saat sekretaris memberikan keputusan, bukan saat reviewer submit review.

            return redirect()->route('reviewer.riwayat-review')
                ->with('success', 'Review berhasil disubmit dan proposal dikirim untuk keputusan sekretaris.');
        }

        return redirect()->route('reviewer.review-proposal.show', $proposal->id)
            ->with('success', 'Draft review berhasil disimpan.');
    }

    /**
     * Pastikan proposal approved memiliki sekretaris yang dapat melihatnya.
     */
    protected function assignProposalToSecretary(Proposal $proposal, string $recommendation)
    {
        $sekretarisId = $proposal->sekretaris_id
            ?? $proposal->assignments()
                ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                ->value('assigned_to');

        if (! $sekretarisId) {
            $candidate = User::whereHas('roles', function ($q) {
                    $q->where('name', 'sekretaris');
                })
                ->where('status', 'active')
                ->orderBy('name')
                ->first();

            if ($candidate) {
                $sekretarisId = $candidate->id;
            }
        }

        if (! $sekretarisId) {
            return;
        }

        if ($proposal->sekretaris_id !== $sekretarisId) {
            $proposal->update(['sekretaris_id' => $sekretarisId]);
        }

        $assignment = ProposalAssignment::firstOrNew([
            'proposal_id' => $proposal->id,
            'role' => ProposalAssignment::ROLE_SEKRETARIS,
            'assigned_to' => $sekretarisId,
        ]);

        if (! $assignment->exists) {
            $assignment->assigned_by = Auth::id();
            $assignment->sent_at = now();
            $assignment->save();
        } elseif (! $assignment->sent_at) {
            $assignment->sent_at = now();
            $assignment->save();
        }

        $notificationStatus = $recommendation === 'revision' ? 'revised' : 'approved';

        Notification::create([
            'user_id' => $sekretarisId,
            'title' => $recommendation === 'revision'
                ? 'Proposal Revisi Reviewer'
                : 'Proposal Disetujui Reviewer',
            'message' => $recommendation === 'revision'
                ? 'Proposal "' . $proposal->title . '" menerima rekomendasi revisi dari reviewer dan masuk ke tab Revised.'
                : 'Proposal "' . $proposal->title . '" telah disetujui oleh reviewer dan masuk ke tab Approved.',
            'type' => Notification::TYPE_REVIEW_ASSIGNMENT,
            'status' => Notification::STATUS_UNREAD,
            'data' => json_encode([
                'proposal_id' => $proposal->id,
                'status' => $notificationStatus,
            ]),
        ]);

        DocumentLog::create([
            'proposal_id' => $proposal->id,
            'user_id' => Auth::id(),
            'activity' => DocumentLog::ACTIVITY_ASSIGN,
            'description' => $recommendation === 'revision'
                ? 'Proposal marked for revision by reviewer and assigned to secretary.'
                : 'Proposal approved by reviewer and assigned to secretary.',
            'metadata' => [
                'sekretaris_id' => $sekretarisId,
                'status' => $notificationStatus,
            ],
        ]);
    }
}
