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
            <div class="stat-value">08</div>
            <div class="stat-sub">
                <span class="dot"></span>
                2 new assigned today
            </div>
        </div>

        {{-- Completed Reviews --}}
        <div class="stat-card">
            <div class="stat-label">Completed Reviews</div>
            <div class="stat-value">142</div>
            <div class="stat-sub">
                <i class="fas fa-calendar-alt" style="color:#9ca3af; font-size:.7rem"></i>
                Current Semester
            </div>
        </div>

        {{-- Priority Action --}}
        <div class="priority-card">
            <div class="priority-badge">⚠ Priority Action</div>
            <div class="priority-title">Bio-Ethics Ref #4290</div>
            <div class="priority-sub">Deadline expires in 24 hours. Faculty of Medicine.</div>
            <button class="btn-review-now" onclick="featureInDevelopment('Review Now')">
                Review Now
            </button>
        </div>
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
                {{-- Row 1 --}}
                <tr>
                    <td>
                        <div class="proposal-title">Genomic Sequencing in Rural Pediatrics</div>
                        <div class="proposal-meta">ID: EP-2023-9904 • Dr. Sarah Jenkins</div>
                    </td>
                    <td>
                        <div class="date-main">Oct 12, 2023</div>
                    </td>
                    <td>
                        <div class="date-main">Oct 26, 2023</div>
                        <div class="date-sub days-urgent">4 DAYS LEFT</div>
                    </td>
                    <td><span class="badge badge-review">UNDER REVIEW</span></td>
                    <td>
                        <button class="action-btn" onclick="featureInDevelopment('Review proposal')">Review</button>
                    </td>
                </tr>

                {{-- Row 2 --}}
                <tr>
                    <td>
                        <div class="proposal-title">AI-Driven Diagnostic Bias Analysis</div>
                        <div class="proposal-meta">ID: EP-2023-9912 • Prof. Michael Chen</div>
                    </td>
                    <td>
                        <div class="date-main">Oct 15, 2023</div>
                    </td>
                    <td>
                        <div class="date-main">Oct 29, 2023</div>
                        <div class="date-sub days-warn">7 DAYS LEFT</div>
                    </td>
                    <td><span class="badge badge-new">NEW ASSIGNMENT</span></td>
                    <td>
                        <button class="action-btn" onclick="featureInDevelopment('Review proposal')">Review</button>
                    </td>
                </tr>

                {{-- Row 3 --}}
                <tr>
                    <td>
                        <div class="proposal-title">Social Media Mental Health Monitoring</div>
                        <div class="proposal-meta">ID: EP-2023-9920 • Dr. Diana Rodriguez</div>
                    </td>
                    <td>
                        <div class="date-main">Oct 18, 2023</div>
                    </td>
                    <td>
                        <div class="date-main">Nov 01, 2023</div>
                        <div class="date-sub days-ok">10 DAYS LEFT</div>
                    </td>
                    <td><span class="badge badge-queued">QUEUED</span></td>
                    <td>
                        <button class="action-btn" onclick="featureInDevelopment('Review proposal')">Review</button>
                    </td>
                </tr>

                {{-- Row 4 --}}
                <tr>
                    <td>
                        <div class="proposal-title">Urban Heat Island Impact Mitigation</div>
                        <div class="proposal-meta">ID: EP-2023-9931 • Dept. of Urban Studies</div>
                    </td>
                    <td>
                        <div class="date-main">Oct 20, 2023</div>
                    </td>
                    <td>
                        <div class="date-main">Nov 03, 2023</div>
                        <div class="date-sub days-ok">12 DAYS LEFT</div>
                    </td>
                    <td><span class="badge badge-queued">QUEUED</span></td>
                    <td>
                        <button class="action-btn" onclick="featureInDevelopment('Review proposal')">Review</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="pagination">
            <span class="page-info">Showing 1–4 of 9 results</span>
            <div class="page-btns">
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn"><i class="fas fa-chevron-right" style="font-size:.65rem"></i></button>
            </div>
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

            <div class="activity-item">
                <div class="act-icon green"><i class="fas fa-check"></i></div>
                <div>
                    <div class="act-title">Approved proposal "Human-Centered Robotics Ethics".</div>
                    <div class="act-time">2 hours ago • Narrative review completed with minor stipulations.</div>
                </div>
            </div>

            <div class="activity-item">
                <div class="act-icon red"><i class="fas fa-undo"></i></div>
                <div>
                    <div class="act-title">Returned proposal "Blockchain for Public Health Data".</div>
                    <div class="act-time">Yesterday • Clarification required on data and encryption protocols.</div>
                </div>
            </div>
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
                    <span class="vel-value">5.2 Days</span>
                </div>
                <div class="vel-bar-bg">
                    <div class="vel-bar-fg teal" style="width: 74%"></div>
                </div>
            </div>

            <div class="vel-row">
                <div class="vel-row-header">
                    <span class="vel-label">Institutional Target</span>
                    <span class="vel-value">7.0 Days</span>
                </div>
                <div class="vel-bar-bg">
                    <div class="vel-bar-fg gray" style="width: 100%"></div>
                </div>
            </div>

            <div class="vel-tip">
                <span class="vel-tip-icon">↑</span>
                <span class="vel-tip-text">
                    <strong>You're 25% faster</strong> than the department average this month. Keep it up!
                </span>
            </div>
        </div>

    </div>{{-- /bottom-grid --}}

</div>{{-- /rw-wrap --}}

@endsection