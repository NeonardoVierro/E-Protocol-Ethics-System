<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\ProposalFile;
use App\Models\Review;
use App\Models\ReviewFeedback;
use App\Models\Notification;
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

        return view('reviewer.review-proposal.show', compact('proposal', 'draftReview', 'draftFeedback'));
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

        $review = Review::where('proposal_id', $proposal->id)
            ->where('reviewer_id', Auth::id())
            ->whereIn('status', [Review::STATUS_IN_PROGRESS, Review::STATUS_ASSIGNED])
            ->first();

        if (! $review) {
            $review = Review::create([
                'proposal_id' => $proposal->id,
                'reviewer_id' => Auth::id(),
                'status' => $isSubmit ? Review::STATUS_COMPLETED : Review::STATUS_IN_PROGRESS,
                'assigned_date' => now(),
                'due_date' => now()->addDays(7),
                'completed_date' => $isSubmit ? now() : null,
            ]);
        } else {
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
            $statusMap = [
                'approved' => Proposal::STATUS_APPROVED,
                'revision' => Proposal::STATUS_REVISED,
                'rejected' => Proposal::STATUS_REJECTED,
            ];

            if (isset($statusMap[$reviewFeedback->recommendation])) {
                $newStatus = $statusMap[$reviewFeedback->recommendation];
                \Log::info('Updating proposal status', [
                    'proposal_id' => $proposal->id,
                    'recommendation' => $reviewFeedback->recommendation,
                    'new_status' => $newStatus,
                    'current_status' => $proposal->status,
                ]);
                $proposal->updateStatus($newStatus);
                // Refresh to verify
                $proposal->refresh();
                \Log::info('Proposal status updated', ['proposal_id' => $proposal->id, 'new_status' => $proposal->status]);

                // Create notification for researcher (proposal owner)
                $statusMessages = [
                    Proposal::STATUS_APPROVED => [
                        'title' => 'Proposal Disetujui! 🎉',
                        'message' => 'Proposal Anda "' . $proposal->title . '" telah disetujui oleh reviewer.',
                    ],
                    Proposal::STATUS_REVISED => [
                        'title' => 'Revisi Diperlukan',
                        'message' => 'Proposal Anda "' . $proposal->title . '" memerlukan revisi. Silakan lihat feedback reviewer.',
                    ],
                    Proposal::STATUS_REJECTED => [
                        'title' => 'Proposal Ditolak',
                        'message' => 'Proposal Anda "' . $proposal->title . '" telah ditolak. Silakan hubungi sekretariat untuk informasi lebih lanjut.',
                    ],
                ];

                if (isset($statusMessages[$newStatus])) {
                    Notification::create([
                        'user_id' => $proposal->user_id,
                        'title' => $statusMessages[$newStatus]['title'],
                        'message' => $statusMessages[$newStatus]['message'],
                        'type' => Notification::TYPE_PROPOSAL_STATUS,
                        'status' => Notification::STATUS_UNREAD,
                        'data' => [
                            'proposal_id' => $proposal->id,
                            'old_status' => 'on_review',
                            'new_status' => $newStatus,
                            'recommendation' => $reviewFeedback->recommendation,
                        ],
                    ]);
                }
            } else {
                \Log::warning('Recommendation not found in statusMap', [
                    'recommendation' => $reviewFeedback->recommendation,
                    'statusMap_keys' => array_keys($statusMap),
                ]);
            }

            return redirect()->route('reviewer.riwayat-review')
                ->with('success', 'Review berhasil disubmit dan status proposal diupdate.');
        }

        return redirect()->route('reviewer.review-proposal.show', $proposal->id)
            ->with('success', 'Draft review berhasil disimpan.');
    }
}
