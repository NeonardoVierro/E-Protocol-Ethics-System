<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\Review;
use App\Models\ReviewFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('reviewer.review-proposal.show', compact('proposal'));
    }

    /**
     * Menyimpan hasil review (submit review).
     */
    public function store(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|integer|exists:proposals,id',
            'feedback' => 'nullable|string|min:10',
            'status' => 'required|in:diterima,revisi,ditolak',
        ]);

        $proposal = Proposal::findOrFail($request->proposal_id);

        $mapping = [
            'diterima' => Proposal::STATUS_ON_REVIEW,
            'revisi' => Proposal::STATUS_REVISED,
            'ditolak' => Proposal::STATUS_REJECTED,
        ];

        $recommendationMap = [
            'diterima' => ReviewFeedback::RECOMMENDATION_APPROVED,
            'revisi' => ReviewFeedback::RECOMMENDATION_REVISION,
            'ditolak' => ReviewFeedback::RECOMMENDATION_REJECTED,
        ];

        $review = Review::create([
            'proposal_id' => $proposal->id,
            'reviewer_id' => Auth::id(),
            'status' => Review::STATUS_COMPLETED,
            'assigned_date' => now(),
            'due_date' => now()->addDays(7),
            'completed_date' => now(),
        ]);

        ReviewFeedback::create([
            'review_id' => $review->id,
            'proposal_id' => $proposal->id,
            'feedback_text' => $request->feedback,
            'recommendation' => $recommendationMap[$request->status],
            'is_submitted' => true,
            'submitted_at' => now(),
        ]);

        $proposal->updateStatus($mapping[$request->status]);

        return redirect()->route('reviewer.riwayat-review')
            ->with('success', 'Review berhasil disubmit dan status proposal diupdate.');
    }
}
