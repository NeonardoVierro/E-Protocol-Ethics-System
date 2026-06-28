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
    public function index(Request $request)
    {
        // Fetch all proposals assigned to current reviewer via ProposalAssignment
        $query = Proposal::whereHas('assignments', function ($q) {
            $q->where('role', ProposalAssignment::ROLE_REVIEWER)
              ->where('assigned_to', Auth::id())
              ->whereNotNull('sent_at');
        })
            ->with([
                'researcher',
                'files',
                'revisions' => function ($q) {
                    $q->with('file')->orderByDesc('revision_number');
                },
            ]);

        // Apply filters from request
        $filterStatus = $request->get('status');
        $filterUrgency = $request->get('urgency');
        $filterSearch = $request->get('search');

        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        if ($filterSearch) {
            $query->where(function ($q) use ($filterSearch) {
                $q->where('title', 'like', "%$filterSearch%")
                  ->orWhere('description', 'like', "%$filterSearch%")
                  ->orWhereHas('researcher', function ($subQ) use ($filterSearch) {
                      $subQ->where('name', 'like', "%$filterSearch%");
                  });
            });
        }

        $allProposals = $query->orderByDesc('submission_date')->get();

        // Apply urgency filter on collection (after fetching)
        if ($filterUrgency === 'urgent') {
            $allProposals = $allProposals->filter(function ($p) {
                return $p->submission_date && $p->submission_date->greaterThanOrEqualTo(Carbon::now()->subDay());
            })->values();
        } elseif ($filterUrgency === 'not_urgent') {
            $allProposals = $allProposals->filter(function ($p) {
                return !($p->submission_date && $p->submission_date->greaterThanOrEqualTo(Carbon::now()->subDay()));
            })->values();
        }

        // Real-time stats
        $total = $allProposals->count();
        $urgent = $allProposals->filter(function ($p) {
            return $p->submission_date && $p->submission_date->greaterThanOrEqualTo(Carbon::now()->subDay());
        })->count();

        // average wait in days from submission to now
        $avgWait = $total > 0 ? round($allProposals->avg(function ($p) {
            return $p->submission_date ? Carbon::now()->diffInDays($p->submission_date) : 0;
        }), 1) : 0;

        // processed = not 'new_proposal'
        $processed = $allProposals->filter(function ($p) {
            return $p->status !== Proposal::STATUS_NEW;
        })->count();

        $teamCapacityPercent = $total > 0 ? round(($processed / $total) * 100) : 0;

        // Load current user's review statuses for these proposals
        $reviewStatuses = Review::where('reviewer_id', Auth::id())
            ->whereIn('proposal_id', $allProposals->pluck('id'))
            ->orderByDesc('created_at')
            ->pluck('status', 'proposal_id')
            ->toArray();

        // Load latest completed review dates for current user, to compare against revision submission dates
        $completedReviewDates = Review::where('reviewer_id', Auth::id())
            ->whereIn('proposal_id', $allProposals->pluck('id'))
            ->where('status', Review::STATUS_COMPLETED)
            ->orderByDesc('completed_date')
            ->get()
            ->groupBy('proposal_id')
            ->mapWithKeys(function ($reviews, $proposalId) {
                return [$proposalId => $reviews->first()->completed_date];
            })->toArray();

        // Attach revision flags for UI
        $allProposals->each(function ($p) use ($reviewStatuses, $completedReviewDates) {
            $p->hasRevisionRequested = $p->revisions->where('status', ProposalRevision::STATUS_REQUESTED)->isNotEmpty();
            $p->hasRevisionSubmitted = $p->revisions->where('status', ProposalRevision::STATUS_SUBMITTED)->isNotEmpty();
            $p->lastSubmittedRevisionDate = optional($p->revisions->where('status', ProposalRevision::STATUS_SUBMITTED)
                ->sortByDesc('submitted_date')
                ->first())->submitted_date;
            $p->lastReviewCompletedDate = isset($completedReviewDates[$p->id]) ? $completedReviewDates[$p->id] : null;
            $p->isReviewed = isset($reviewStatuses[$p->id]) && $reviewStatuses[$p->id] === \App\Models\Review::STATUS_COMPLETED;
        });

        // Paginate with 15 items per page
        $perPage = 15;
        $page = $request->get('page', 1);
        $proposals = $allProposals->forPage($page, $perPage);
        $total_count = $allProposals->count();

        // Create manual pagination object
        $paginationData = new \stdClass();
        $paginationData->current_page = $page;
        $paginationData->per_page = $perPage;
        $paginationData->total = $total_count;
        $paginationData->last_page = ceil($total_count / $perPage);
        $paginationData->from = (($page - 1) * $perPage) + 1;
        $paginationData->to = min($page * $perPage, $total_count);

        return view('reviewer.proposal-masuk.index', compact(
            'proposals', 
            'total', 
            'urgent', 
            'avgWait', 
            'teamCapacityPercent', 
            'reviewStatuses',
            'paginationData'
        ));
    }

    /**
     * Export proposals list to CSV
     */
    public function export()
    {
        // Fetch all proposals
        $proposals = Proposal::whereHas('assignments', function ($query) {
            $query->where('role', ProposalAssignment::ROLE_REVIEWER)
                  ->where('assigned_to', Auth::id())
                  ->whereNotNull('sent_at');
        })
            ->with('researcher')
            ->orderByDesc('submission_date')
            ->get();

        $filename = 'proposals_' . Auth::id() . '_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=$filename"
        ];

        $callback = function() use ($proposals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Proposal', 'Judul', 'Peneliti', 'Tanggal Masuk', 'Deadline', 'Status']);

            foreach ($proposals as $proposal) {
                fputcsv($file, [
                    'EP-' . str_pad($proposal->id, 4, '0', STR_PAD_LEFT),
                    $proposal->title,
                    $proposal->researcher->name ?? 'Unknown',
                    $proposal->submission_date?->format('Y-m-d') ?? '-',
                    $proposal->submission_date?->addDays(14)->format('Y-m-d') ?? '-',
                    ucfirst(str_replace('_', ' ', $proposal->status))
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}