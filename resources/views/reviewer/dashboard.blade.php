@extends('layouts.reviewer')

@section('title', 'Dashboard Reviewer')
@section('page-title', 'Dashboard Reviewer')
@section('breadcrumb', 'Overview & Statistik Review')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

    .rw-wrap { font-family: 'DM Sans', sans-serif; color: #1a1d23; }

    /* ── Header ── */
    .rw-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; }
    .rw-header-left h1 { font-size: 1.5rem; font-weight: 700; margin: 0 0 .2rem; }
    .rw-header-left p  { font-size: .8rem; color: #6b7280; margin: 0; }
    .rw-header-actions { display: flex; align-items: center; gap: .75rem; }
    .btn-bell { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: .45rem .6rem; cursor: pointer; color: #6b7280; font-size: .95rem; }
    .btn-quick { background: #1a1d23; color: #fff; border: none; border-radius: 8px; padding: .5rem 1rem; font-size: .82rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: .4rem; }
    .btn-quick:hover { background: #111; }

    /* ── Stat Cards row ── */
    .stat-grid { display: grid; grid-template-columns: 1fr 1fr 1.4fr; gap: 1rem; margin-bottom: 1.25rem; }


    .stat-card { background: #fff; border: 1px solid #e9ebee; border-radius: 14px; padding: 1.25rem 1.4rem; }
    .stat-label { font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; margin-bottom: .35rem; }
    .stat-value { font-size: 2.4rem; font-weight: 700; line-height: 1; color: #111; }
    .stat-sub   { font-size: .75rem; color: #6b7280; margin-top: .5rem; display: flex; align-items: center; gap: .3rem; }
    .stat-sub .dot { width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block; }

    /* Priority card */
    .priority-card { background: #1e2a4a; border-radius: 14px; padding: 1.25rem 1.4rem; color: #fff; position: relative; overflow: hidden; }
    .priority-card::before {
        content: '⚠';
        position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
        font-size: 4rem; opacity: .08; pointer-events: none;
    }
    .priority-badge { font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #93c5fd; margin-bottom: .4rem; }
    .priority-title { font-size: 1.1rem; font-weight: 700; margin-bottom: .35rem; }
    .priority-sub  { font-size: .75rem; color: #93c5fd; margin-bottom: .9rem; }
    .btn-review-now { background: #2563eb; color: #fff; border: none; border-radius: 7px; padding: .45rem .9rem; font-size: .78rem; font-weight: 600; cursor: pointer; }
    .btn-review-now:hover { background: #1d4ed8; }

    /* ── Review Queue ── */
    .section-card { background: #fff; border: 1px solid #e9ebee; border-radius: 14px; padding: 1.25rem 1.4rem; margin-bottom: 1.25rem; }
    .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
    .section-title { font-size: 1rem; font-weight: 700; }
    .queue-controls { display: flex; align-items: center; gap: .6rem; }
    .search-box { border: 1px solid #e5e7eb; border-radius: 8px; padding: .4rem .75rem; font-size: .8rem; color: #374151; outline: none; width: 160px; font-family: inherit; }
    .search-box::placeholder { color: #9ca3af; }
    .filter-btn { border: 1px solid #e5e7eb; border-radius: 8px; padding: .4rem .55rem; background: #fff; cursor: pointer; color: #6b7280; }

    /* Table */
    .queue-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
    .queue-table thead tr { border-bottom: 1px solid #f3f4f6; }
    .queue-table th { text-align: left; padding: .5rem .5rem .75rem; font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; }
    .queue-table td { padding: .85rem .5rem; vertical-align: top; border-bottom: 1px solid #f9fafb; }
    .queue-table tr:last-child td { border-bottom: none; }
    .proposal-title  { font-weight: 600; color: #111; margin-bottom: .18rem; }
    .proposal-meta   { font-size: .72rem; color: #9ca3af; font-family: 'DM Mono', monospace; }
    .date-main  { font-weight: 500; color: #374151; }
    .date-sub   { font-size: .72rem; margin-top: .15rem; }
    .days-urgent { color: #ef4444; font-weight: 600; }
    .days-warn   { color: #f59e0b; font-weight: 600; }
    .days-ok     { color: #6b7280; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; padding: .25rem .65rem; border-radius: 20px; font-size: .68rem; font-weight: 700; letter-spacing: .03em; }
    .badge-review  { background: #dbeafe; color: #1d4ed8; }
    .badge-new     { background: #fef3c7; color: #b45309; }
    .badge-queued  { background: #f3f4f6; color: #6b7280; }

    .action-btn { border: 1px solid #e5e7eb; border-radius: 7px; padding: .3rem .65rem; font-size: .75rem; background: #fff; cursor: pointer; color: #374151; font-family: inherit; }
    .action-btn:hover { background: #f9fafb; }

    /* Pagination */
    .pagination { display: flex; align-items: center; justify-content: space-between; margin-top: .75rem; }
    .page-info { font-size: .75rem; color: #9ca3af; }
    .page-btns { display: flex; gap: .35rem; }
    .page-btn { width: 28px; height: 28px; border-radius: 7px; border: 1px solid #e5e7eb; background: #fff; font-size: .78rem; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #374151; }
    .page-btn.active { background: #1a1d23; color: #fff; border-color: #1a1d23; }
    .page-btn:hover:not(.active) { background: #f9fafb; }

    /* ── Bottom row ── */
    .bottom-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    /* Recent Activity */
    .activity-header { display: flex; align-items: center; gap: .5rem; font-size: .95rem; font-weight: 700; margin-bottom: 1rem; }
    .activity-icon-wrap { color: #6366f1; font-size: 1rem; }
    .activity-item { display: flex; align-items: flex-start; gap: .75rem; padding: .75rem 0; border-bottom: 1px solid #f3f4f6; }
    .activity-item:last-child { border-bottom: none; padding-bottom: 0; }
    .act-icon { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: .8rem; margin-top: .05rem; }
    .act-icon.green { background: #dcfce7; color: #16a34a; }
    .act-icon.red   { background: #fee2e2; color: #dc2626; }
    .act-title { font-size: .82rem; font-weight: 500; color: #111; margin-bottom: .15rem; }
    .act-time  { font-size: .72rem; color: #9ca3af; }

    /* Review Velocity */
    .velocity-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
    .velocity-title { font-size: .95rem; font-weight: 700; }
    .velocity-icon  { width: 32px; height: 32px; background: #1e2a4a; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: .85rem; }
    .velocity-sub   { font-size: .75rem; color: #9ca3af; margin-bottom: 1.1rem; }
    .vel-row { margin-bottom: .9rem; }
    .vel-row-header { display: flex; justify-content: space-between; font-size: .78rem; margin-bottom: .35rem; }
    .vel-label { color: #374151; font-weight: 500; }
    .vel-value { font-weight: 600; color: #111; }
    .vel-bar-bg { height: 6px; background: #f3f4f6; border-radius: 10px; overflow: hidden; }
    .vel-bar-fg { height: 6px; border-radius: 10px; }
    .vel-bar-fg.teal  { background: #14b8a6; }
    .vel-bar-fg.gray  { background: #d1d5db; }
    .vel-tip { background: #f0fdf4; border-radius: 10px; padding: .65rem .85rem; display: flex; align-items: center; gap: .6rem; margin-top: 1rem; }
    .vel-tip-icon { color: #10b981; font-size: .85rem; }
    .vel-tip-text { font-size: .75rem; color: #374151; }
    .vel-tip-text strong { color: #065f46; }
</style>

<div class="rw-wrap">

    {{-- ── Page Header ── --}}
    <div class="rw-header">
        <div class="rw-header-left">
            <h1>Reviewer Workspace</h1>
            <p>Manage your ethical review queue and evaluate pending research proposals.</p>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="stat-grid">
        {{-- Active Reviews --}}
        <div class="stat-card">
            <div class="stat-label">Active Reviews</div>
            <div class="stat-value">{{ str_pad($activeReviewsCount, 2, '0', STR_PAD_LEFT) }}</div>
            <div class="stat-sub">
                <span class="dot"></span>
                {{ $allReviews->where('status', App\Models\Review::STATUS_ASSIGNED)->count() }} newly assigned
            </div>
        </div>

        {{-- Completed Reviews --}}
        <div class="stat-card">
            <div class="stat-label">Completed Reviews</div>
            <div class="stat-value">{{ str_pad($completedReviewsCount, 3, '0', STR_PAD_LEFT) }}</div>
            <div class="stat-sub">
                <i class="fas fa-calendar-alt" style="color:#9ca3af; font-size:.7rem"></i>
                All time
            </div>
        </div>

        {{-- Priority Action --}}
        @if($priorityReview)
        <div class="priority-card">
            <div class="priority-badge">⚠ Priority Action</div>
            <div class="priority-title">{{ $priorityReview->proposal->title }}</div>
            <div class="priority-sub">Deadline: {{ $priorityReview->due_date->format('M d, Y') }}</div>
            <a href="{{ route('reviewer.review-proposal.show', $priorityReview->proposal->id) }}" class="btn-review-now">
                Review Now
            </a>
        </div>
        @else
        <div class="priority-card">
            <div class="priority-badge">✓ All Clear</div>
            <div class="priority-title">No Urgent Reviews</div>
            <div class="priority-sub">All reviews are on schedule.</div>
        </div>
        @endif
    </div>

    {{-- ── Review Queue ── --}}
    <div class="section-card">
        <div class="section-header">
            <span class="section-title">Review Queue</span>
            <div class="queue-controls">
                <input class="search-box" type="text" placeholder="Filter proposals…">
                <button class="filter-btn" title="Advanced filter">
                    <i class="fas fa-sliders-h"></i>
                </button>
            </div>
        </div>

        <table class="queue-table">
            <thead>
                <tr>
                    <th>Proposal Title</th>
                    <th>Submitted</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allReviews as $review)
                <tr>
                    <td>
                        <div class="proposal-title">{{ $review->proposal->title }}</div>
                        <div class="proposal-meta">ID: {{ $review->proposal->id }} • {{ $review->proposal->researcher->name ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <div class="date-main">{{ $review->assigned_date?->format('M d, Y') ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="date-main">{{ $review->due_date?->format('M d, Y') ?? '-' }}</div>
                        @php
                            $daysLeft = $review->due_date ? $review->due_date->diffInDays(now(), false) : null;
                            $daysClass = 'days-ok';
                            if ($daysLeft !== null) {
                                if ($daysLeft < 0) {
                                    $daysClass = 'days-urgent';
                                    $daysLabel = 'EXPIRED';
                                } elseif ($daysLeft <= 3) {
                                    $daysClass = 'days-urgent';
                                    $daysLabel = $daysLeft . ' DAYS LEFT';
                                } elseif ($daysLeft <= 7) {
                                    $daysClass = 'days-warn';
                                    $daysLabel = $daysLeft . ' DAYS LEFT';
                                } else {
                                    $daysClass = 'days-ok';
                                    $daysLabel = $daysLeft . ' DAYS LEFT';
                                }
                            }
                        @endphp
                        <div class="date-sub {{ $daysClass }}">{{ $daysLabel ?? '-' }}</div>
                    </td>
                    <td>
                        @php
                            $statusBadgeClass = 'badge-queued';
                            $statusLabel = 'QUEUED';
                            if ($review->status === App\Models\Review::STATUS_IN_PROGRESS) {
                                $statusBadgeClass = 'badge-review';
                                $statusLabel = 'UNDER REVIEW';
                            } elseif ($review->status === App\Models\Review::STATUS_ASSIGNED) {
                                $statusBadgeClass = 'badge-new';
                                $statusLabel = 'NEW ASSIGNMENT';
                            } elseif ($review->status === App\Models\Review::STATUS_COMPLETED) {
                                $statusBadgeClass = 'badge-queued';
                                $statusLabel = 'COMPLETED';
                            }
                        @endphp
                        <span class="badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td>
                        @if($review->status === App\Models\Review::STATUS_COMPLETED)
                            <a href="{{ route('reviewer.riwayat-review.show', $review->id) }}" class="action-btn">
                                View
                            </a>
                        @else
                            <a href="{{ route('reviewer.review-proposal.show', $review->proposal->id) }}" class="action-btn">
                                Review
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: #9ca3af;">
                        No reviews assigned yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            <span class="page-info">Showing 1–{{ count($allReviews) }} of {{ count($allReviews) }} results</span>
        </div>
    </div>

    {{-- ── Bottom Row ── --}}
    <div class="bottom-grid">

        {{-- Recent Activity --}}
        <div class="section-card" style="margin-bottom:0">
            <div class="activity-header">
                <span class="activity-icon-wrap">⟳</span>
                Recent Activity
            </div>

            @forelse($recentActivity as $activity)
            <div class="activity-item">
                @php
                    $isCompleted = $activity->status === App\Models\Review::STATUS_COMPLETED;
                    $iconClass = $isCompleted ? 'green' : 'red';
                    $icon = $isCompleted ? 'fa-check' : 'fa-undo';
                @endphp
                <div class="act-icon {{ $iconClass }}"><i class="fas {{ $icon }}"></i></div>
                <div>
                    <div class="act-title">{{ $isCompleted ? 'Completed' : 'Returned' }} proposal "{{ $activity->proposal->title }}".</div>
                    <div class="act-time">{{ $activity->completed_date?->diffForHumans() ?? 'N/A' }} • {{ $activity->feedback?->feedback_text ? 'Feedback provided' : 'No feedback' }}.</div>
                </div>
            </div>
            @empty
            <div class="activity-item">
                <div style="color: #9ca3af; font-size: .82rem;">No recent activity yet.</div>
            </div>
            @endforelse
        </div>

        {{-- Review Velocity --}}
        <div class="section-card" style="margin-bottom:0">
            <div class="velocity-header">
                <div>
                    <div class="velocity-title">Review Velocity</div>
                    <div class="velocity-sub">Your review turnaround time compared to the institutional average.</div>
                </div>
                <div class="velocity-icon"><i class="fas fa-chart-line"></i></div>
            </div>

            <div class="vel-row">
                <div class="vel-row-header">
                    <span class="vel-label">Current Speed</span>
                    <span class="vel-value">{{ number_format($avgReviewDays, 1) }} Days</span>
                </div>
                <div class="vel-bar-bg">
                    <div class="vel-bar-fg teal" style="width: {{ min(($avgReviewDays / $institutionalAvg) * 100, 100) }}%"></div>
                </div>
            </div>

            <div class="vel-row">
                <div class="vel-row-header">
                    <span class="vel-label">Institutional Target</span>
                    <span class="vel-value">{{ number_format($institutionalAvg, 1) }} Days</span>
                </div>
                <div class="vel-bar-bg">
                    <div class="vel-bar-fg gray" style="width: 100%"></div>
                </div>
            </div>

            <div class="vel-tip">
                @if($velocityPercentage > 0)
                <span class="vel-tip-icon">↑</span>
                <span class="vel-tip-text">
                    <strong>You're {{ abs($velocityPercentage) }}% faster</strong> than the department average this month. Keep it up!
                </span>
                @elseif($velocityPercentage < 0)
                <span class="vel-tip-icon">↓</span>
                <span class="vel-tip-text">
                    <strong>You're {{ abs($velocityPercentage) }}% slower</strong> than the department average. Try to speed up reviews to maintain quality standards.
                </span>
                @else
                <span class="vel-tip-icon">→</span>
                <span class="vel-tip-text">
                    <strong>You're at pace</strong> with the department average. Maintain this consistent performance!
                </span>
                @endif
            </div>
        </div>

    </div>{{-- /bottom-grid --}}

</div>{{-- /rw-wrap --}}

@endsection