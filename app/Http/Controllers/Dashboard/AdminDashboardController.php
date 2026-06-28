<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DocumentLog;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }
    
    public function index()
    {
        $today = Carbon::today();
        $periodStart = $today->copy()->subDays(4)->startOfDay();

        $trendResults = Proposal::selectRaw('DATE(created_at) as day, status, COUNT(*) as total')
            ->whereBetween('created_at', [$periodStart, $today->copy()->endOfDay()])
            ->whereIn('status', [
                Proposal::STATUS_NEW,
                Proposal::STATUS_IN_PROCESS,
                Proposal::STATUS_ON_REVIEW,
                Proposal::STATUS_REVISED,
                Proposal::STATUS_APPROVED,
                Proposal::STATUS_REJECTED,
            ])
            ->groupBy('day', 'status')
            ->get();

        $proposalChartLabels = [];
        $proposalTrendApproved = [];
        $proposalTrendPending = [];

        for ($daysBack = 4; $daysBack >= 0; $daysBack--) {
            $date = $today->copy()->subDays($daysBack);
            $dayKey = $date->toDateString();

            $proposalChartLabels[] = strtoupper($date->format('D'));
            $proposalTrendApproved[] = $trendResults->where('day', $dayKey)
                ->where('status', Proposal::STATUS_APPROVED)
                ->sum('total');
            $proposalTrendPending[] = $trendResults->where('day', $dayKey)
                ->whereNotIn('status', [Proposal::STATUS_APPROVED, Proposal::STATUS_REJECTED])
                ->sum('total');
        }

        $totalProposals = Proposal::count();
        $approvedProposals = Proposal::where('status', Proposal::STATUS_APPROVED)->count();
        $pendingProposals = Proposal::whereNotIn('status', [Proposal::STATUS_APPROVED, Proposal::STATUS_REJECTED])->count();
        $activeReviewers = User::whereHas('roles', function ($query) {
                $query->where('name', 'reviewer');
            })
            ->where('status', 'active')
            ->count();

        $recentUsers = User::latest('created_at')->limit(5)->get();
        $systemLogs = DocumentLog::with(['user', 'proposal'])
            ->latest('created_at')
            ->limit(3)
            ->get();

        return view('admin.dashboard', compact(
            'totalProposals',
            'approvedProposals',
            'pendingProposals',
            'activeReviewers',
            'recentUsers',
            'systemLogs',
            'proposalChartLabels',
            'proposalTrendApproved',
            'proposalTrendPending'
        ));
    }

    public function profile()
    {
        return view('admin.profile', [
            'title' => 'Profil Admin',
        ]);
    }
}