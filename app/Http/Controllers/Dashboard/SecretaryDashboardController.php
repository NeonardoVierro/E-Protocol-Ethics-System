<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecretaryDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:sekretaris|ketua']);
    }
    
    public function index()
    {
        $assignmentConstraint = function ($query) {
            $query->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                  ->where('assigned_to', Auth::id())
                  ->whereNotNull('sent_at');
        };

        $baseQuery = Proposal::with(['researcher', 'assignments' => $assignmentConstraint])
            ->whereHas('assignments', $assignmentConstraint);

        $data = [
            'title' => 'Dashboard Sekretaris',
            'total_proposal' => $baseQuery->count(),
            'new_proposal' => (clone $baseQuery)->whereIn('status', [Proposal::STATUS_NEW, Proposal::STATUS_IN_PROCESS])->count(),
            'on_review' => (clone $baseQuery)->where('status', Proposal::STATUS_ON_REVIEW)->count(),
            'approved' => (clone $baseQuery)->where('status', Proposal::STATUS_APPROVED)->count(),
            'rejected' => (clone $baseQuery)->where('status', Proposal::STATUS_REJECTED)->count(),
            'recentProposals' => $baseQuery->orderByDesc('submission_date')->orderByDesc('created_at')->take(4)->get(),
        ];
        
        return view('sekretaris.dashboard', $data);
    }
}