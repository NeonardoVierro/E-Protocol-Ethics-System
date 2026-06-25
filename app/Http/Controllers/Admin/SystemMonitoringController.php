<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentLog;
use App\Models\EthicsDocument;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use Illuminate\Support\Facades\DB;

class SystemMonitoringController extends Controller
{
    public function index()
    {
        $totalProposals = Proposal::count();

        $proposalStatuses = Proposal::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $ethicsStatuses = EthicsDocument::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $proposalsInReview = Proposal::whereIn('status', [
            Proposal::STATUS_IN_PROCESS,
            Proposal::STATUS_ON_REVIEW,
            Proposal::STATUS_REVISED,
        ])->count();

        $proposalsAwaitingEthics = Proposal::whereIn('status', [
            Proposal::STATUS_APPROVED,
            Proposal::STATUS_WAITING_FOR_CONFIRMATION,
            Proposal::STATUS_READY_FOR_CHAIR,
            Proposal::STATUS_WITH_CHAIR,
            Proposal::STATUS_WAITING_FOR_PUBLISH,
        ])->count();

        $publishedProposals = Proposal::where('status', Proposal::STATUS_PUBLISHED)->count();

        $incomingProposals = Proposal::where('status', Proposal::STATUS_NEW)->count();

        $adminReceivedCount = Proposal::whereHas('documentLogs', function ($query) {
            $query->where('activity', DocumentLog::ACTIVITY_SENT_TO_ADMIN);
        })->count();

        $sekreAssignedCount = Proposal::whereHas('assignments', function ($query) {
            $query->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                ->whereNotNull('sent_at');
        })->count();

        $reviewerAssignedCount = Proposal::whereHas('assignments', function ($query) {
            $query->where('role', ProposalAssignment::ROLE_REVIEWER)
                ->whereNotNull('sent_at');
        })->count();

        $recentProposals = Proposal::with(['researcher', 'ethicsDocument'])
            ->latest('updated_at')
            ->take(8)
            ->get();

        $workflowProposals = Proposal::with([
                'researcher',
                'assignments',
                'documentLogs',
                'ethicsDocument'
            ])
            ->latest('updated_at')
            ->take(12)
            ->get();

        $recentActivities = DocumentLog::with(['user', 'proposal', 'ethicsDocument'])
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('admin.systemmonitoring.index', compact(
            'totalProposals',
            'proposalStatuses',
            'ethicsStatuses',
            'proposalsInReview',
            'proposalsAwaitingEthics',
            'publishedProposals',
            'incomingProposals',
            'adminReceivedCount',
            'sekreAssignedCount',
            'reviewerAssignedCount',
            'recentProposals',
            'workflowProposals',
            'recentActivities'
        ));
    }
}