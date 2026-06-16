<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalRevision;
use App\Models\ProposalAssignment;
use App\Models\Review;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalMasukController extends Controller
{
    /**
     * Menampilkan daftar proposal yang perlu direview oleh reviewer.
     */
    public function index()
    {
        // Fetch proposals assigned to current reviewer via ProposalAssignment
        $proposals = Proposal::whereHas('assignments', function ($query) {
            $query->where('role', ProposalAssignment::ROLE_REVIEWER)
                  ->where('assigned_to', Auth::id())
                  ->whereNotNull('sent_at');
        })
            ->with([
                'researcher',
                'files',
                'revisions' => function ($q) {
                    $q->with('file')->orderByDesc('revision_number');
                },
            ])
            ->orderByDesc('submission_date')
            ->get();

        // Real-time stats
        $total = $proposals->count();
        $urgent = $proposals->filter(function ($p) {
            return $p->submission_date && $p->submission_date->greaterThanOrEqualTo(Carbon::now()->subDay());
        })->count();

        // average wait in days from submission to now
        $avgWait = $total > 0 ? round($proposals->avg(function ($p) {
            return $p->submission_date ? Carbon::now()->diffInDays($p->submission_date) : 0;
        }), 1) : 0;

        // processed = not 'new_proposal'
        $processed = $proposals->filter(function ($p) {
            return $p->status !== Proposal::STATUS_NEW;
        })->count();

        $teamCapacityPercent = $total > 0 ? round(($processed / $total) * 100) : 0;

        // Load current user's review statuses for these proposals
        $reviewStatuses = Review::where('reviewer_id', Auth::id())
            ->whereIn('proposal_id', $proposals->pluck('id'))
            ->orderByDesc('created_at')
            ->pluck('status', 'proposal_id')
            ->toArray();

        // Load latest completed review dates for current user, to compare against revision submission dates
        $completedReviewDates = Review::where('reviewer_id', Auth::id())
            ->whereIn('proposal_id', $proposals->pluck('id'))
            ->where('status', Review::STATUS_COMPLETED)
            ->orderByDesc('completed_date')
            ->get()
            ->groupBy('proposal_id')
            ->mapWithKeys(function ($reviews, $proposalId) {
                return [$proposalId => $reviews->first()->completed_date];
            })->toArray();

        // Attach revision flags for UI
        $proposals->each(function ($p) use ($reviewStatuses, $completedReviewDates) {
            $p->hasRevisionRequested = $p->revisions->where('status', ProposalRevision::STATUS_REQUESTED)->isNotEmpty();
            $p->hasRevisionSubmitted = $p->revisions->where('status', ProposalRevision::STATUS_SUBMITTED)->isNotEmpty();
            $p->lastSubmittedRevisionDate = optional($p->revisions->where('status', ProposalRevision::STATUS_SUBMITTED)
                ->sortByDesc('submitted_date')
                ->first())->submitted_date;
            $p->lastReviewCompletedDate = isset($completedReviewDates[$p->id]) ? $completedReviewDates[$p->id] : null;
            $p->isReviewed = isset($reviewStatuses[$p->id]) && $reviewStatuses[$p->id] === \App\Models\Review::STATUS_COMPLETED;
        });

        return view('reviewer.proposal-masuk.index', compact('proposals', 'total', 'urgent', 'avgWait', 'teamCapacityPercent', 'reviewStatuses'));
    }
}