@extends('layouts.reviewer')

@section('title', 'Proposal Masuk')
@section('page-title', 'Proposal Masuk')
@section('breadcrumb', 'Daftar proposal dari sekretaris yang perlu direview')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

    .pm-wrap { font-family: 'DM Sans', sans-serif; color: #1a1d23; }

    /* ── Page Header ── */
    .pm-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; }
    .pm-header-left h1 { font-size: 1.45rem; font-weight: 700; margin: 0 0 .2rem; }
    .pm-header-left p  { font-size: .8rem; color: #6b7280; margin: 0; }
    .pm-header-actions { display: flex; gap: .6rem; }
    .btn-filter { border: 1px solid #d1d5db; border-radius: 8px; padding: .45rem .9rem; font-size: .8rem; font-weight: 500; background: #fff; cursor: pointer; display: flex; align-items: center; gap: .4rem; color: #374151; font-family: inherit; }
    .btn-filter:hover { background: #f9fafb; }
    .btn-export { background: #1a1d23; color: #fff; border: none; border-radius: 8px; padding: .45rem .9rem; font-size: .8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: .4rem; font-family: inherit; }
    .btn-export:hover { background: #111; }

    /* ── Stat Cards ── */
    .stat-row { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem; }
    .stat-card { background: #fff; border: 1px solid #e9ebee; border-radius: 14px; padding: 1.1rem 1.3rem; }
    .stat-card.urgent { border-left: 4px solid #ef4444; }
    .stat-label { font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; margin-bottom: .3rem; }
    .stat-value { font-size: 2rem; font-weight: 700; line-height: 1.1; color: #111; }
    .stat-value.urgent { color: #ef4444; }
    .stat-sub { font-size: .72rem; color: #6b7280; margin-top: .4rem; display: flex; align-items: center; gap: .3rem; }
    .stat-sub .arrow-up { color: #10b981; font-size: .7rem; }
    .urgent-note { font-size: .72rem; color: #ef4444; margin-top: .3rem; font-weight: 500; }
    .capacity-bar-bg { height: 5px; background: #f3f4f6; border-radius: 10px; margin-top: .55rem; overflow: hidden; }
    .capacity-bar-fg { height: 5px; background: #10b981; border-radius: 10px; width: 82%; }

    /* ── Active Proposals Table ── */
    .table-card { background: #fff; border: 1px solid #e9ebee; border-radius: 14px; overflow: hidden; }
    .table-header { display: flex; align-items: center; justify-content: space-between; padding: 1.1rem 1.3rem; border-bottom: 1px solid #f3f4f6; }
    .table-title { font-size: 1rem; font-weight: 700; }
    .tab-group { display: flex; gap: .35rem; }
    .tab-btn { border: 1px solid #e5e7eb; border-radius: 7px; padding: .3rem .7rem; font-size: .75rem; font-weight: 500; background: #fff; cursor: pointer; color: #6b7280; font-family: inherit; white-space: nowrap; }
    .tab-btn.active { background: #1a1d23; color: #fff; border-color: #1a1d23; }
    .tab-btn:hover:not(.active) { background: #f9fafb; }

    /* Table */
    .prop-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
    .prop-table thead tr { background: #fafafa; border-bottom: 1px solid #f0f0f0; }
    .prop-table th { padding: .65rem 1rem; text-align: left; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; }
    .prop-table td { padding: .9rem 1rem; vertical-align: middle; border-bottom: 1px solid #f9fafb; }
    .prop-table tr:last-child td { border-bottom: none; }
    .prop-table tr:hover td { background: #fafafa; }

    .id-code  { font-family: 'DM Mono', monospace; font-size: .78rem; color: #374151; font-weight: 500; line-height: 1.4; }
    .judul-title { font-weight: 600; color: #111; margin-bottom: .1rem; font-size: .83rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px; }
    .judul-sub   { font-size: .71rem; color: #9ca3af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px; }

    .peneliti-wrap { display: flex; align-items: center; gap: .55rem; }
    .avatar { width: 28px; height: 28px; border-radius: 50%; background: #e0e7ff; display: flex; align-items: center; justify-content: center; font-size: .7rem; font-weight: 700; color: #4338ca; flex-shrink: 0; }
    .avatar.gray { background: #f3f4f6; color: #6b7280; }
    .peneliti-name { font-size: .8rem; font-weight: 500; color: #374151; }

    .date-main { font-weight: 500; color: #374151; font-size: .8rem; }
    .date-deadline { font-weight: 600; color: #ef4444; font-size: .8rem; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; padding: .22rem .6rem; border-radius: 20px; font-size: .68rem; font-weight: 700; letter-spacing: .03em; }
    .badge-new    { background: #dbeafe; color: #1d4ed8; }
    .badge-queued { background: #f3f4f6; color: #6b7280; }
    .badge-urgent { background: #fee2e2; color: #dc2626; }

    /* Action buttons */
    .btn-review-now  { background: #1a1d23; color: #fff; border: none; border-radius: 7px; padding: .35rem .75rem; font-size: .75rem; font-weight: 600; cursor: pointer; font-family: inherit; white-space: nowrap; }
    .btn-review-now:hover { background: #111; }
    .btn-view-detail { border: 1px solid #e5e7eb; background: #fff; color: #374151; border-radius: 7px; padding: .35rem .75rem; font-size: .75rem; font-weight: 500; cursor: pointer; font-family: inherit; white-space: nowrap; }
    .btn-view-detail:hover { background: #f9fafb; }

    /* Pagination */
    .table-footer { display: flex; align-items: center; justify-content: space-between; padding: .85rem 1.3rem; border-top: 1px solid #f3f4f6; }
    .page-info { font-size: .75rem; color: #9ca3af; }
    .page-btns { display: flex; align-items: center; gap: .3rem; }
    .page-btn { width: 30px; height: 30px; border-radius: 7px; border: 1px solid #e5e7eb; background: #fff; font-size: .78rem; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #374151; font-family: inherit; }
    .page-btn.active { background: #1a1d23; color: #fff; border-color: #1a1d23; }
    .page-btn:hover:not(.active) { background: #f9fafb; }
    .page-btn.add-btn { background: #1a1d23; color: #fff; border-color: #1a1d23; font-size: 1rem; width: 34px; height: 34px; border-radius: 9px; }
    .page-dots { color: #9ca3af; font-size: .8rem; padding: 0 .2rem; }
</style>

<div class="pm-wrap">

    {{-- ── Page Header ── --}}
    <div class="pm-header">
        <div class="pm-header-left">
            <h1>Proposal Masuk</h1>
            <p>Review the latest ethics applications submitted for committee evaluation.</p>
        </div>
        <div class="pm-header-actions">
            <button class="btn-filter" onclick="featureInDevelopment('Filter Queue')">
                <i class="fas fa-sliders-h"></i> Filter Queue
            </button>
            <button class="btn-export" onclick="featureInDevelopment('Export List')">
                <i class="fas fa-download"></i> Export List
            </button>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="stat-row">
        {{-- Total Queue --}}
        <div class="stat-card">
            <div class="stat-label">Total Queue</div>
            <div class="stat-value">{{ $total ?? $proposals->count() }}</div>
            <div class="stat-sub">
                <span class="arrow-up">↑</span> Real-time
            </div>
        </div>

        {{-- Urgent (24h) --}}
        <div class="stat-card urgent">
            <div class="stat-label">Urgent (24h)</div>
            <div class="stat-value urgent">{{ $urgent ?? 0 }}</div>
            <div class="urgent-note">⚑ Requires immediate action</div>
        </div>

        {{-- Average Wait --}}
        <div class="stat-card">
            <div class="stat-label">Average Wait</div>
            <div class="stat-value">{{ $avgWait ?? 0 }}d</div>
            <div class="stat-sub">
                <i class="fas fa-clock" style="color:#9ca3af;font-size:.65rem"></i> Avg days since submission
            </div>
        </div>

        {{-- Team Capacity --}}
        <div class="stat-card">
            <div class="stat-label">Team Capacity</div>
            <div class="stat-value">{{ $teamCapacityPercent ?? 0 }}%</div>
            <div class="capacity-bar-bg">
                <div class="capacity-bar-fg" style="width: {{ $teamCapacityPercent ?? 0 }}%;"></div>
            </div>
        </div>
    </div>

    {{-- ── Active Proposals Table ── --}}
    <div class="table-card">
        <div class="table-header">
            <span class="table-title">Active Proposals</span>
            <div class="tab-group">
                <button class="tab-btn active">ALL (24)</button>
                <button class="tab-btn">NEW (8)</button>
                <button class="tab-btn">QUEUED (16)</button>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="prop-table">
                <thead>
                    <tr>
                        <th>ID Proposal</th>
                        <th>Judul Proposal</th>
                        <th>Peneliti</th>
                        <th>Tanggal Masuk</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposals as $proposal)
                        <tr>
                            <td>
                                <div class="id-code">EP-</div>
                                <div class="id-code">{{ str_pad($proposal->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td>
                                <div class="judul-title">{{ Str::limit($proposal->title, 20, '…') }}</div>
                                <div class="judul-sub">{{ $proposal->description ? Str::limit($proposal->description, 25, '…') : 'Research Proposal' }}</div>
                            </td>
                            <td>
                                <div class="peneliti-wrap">
                                    <div class="avatar">{{ substr($proposal->researcher->name ?? 'U', 0, 1) }}</div>
                                    <span class="peneliti-name">{{ Str::limit($proposal->researcher->name ?? 'Unknown', 20) }}</span>
                                </div>
                            </td>
                            <td><span class="date-main">{{ optional($proposal->submission_date)->format('M d, Y') ?? 'N/A' }}</span></td>
                            <td><span class="date-deadline">{{ optional($proposal->submission_date)->addDays(14)->format('M d, Y') ?? 'N/A' }}</span></td>
                            <td>
                                @if($proposal->status === 'new_proposal')
                                    <span class="badge badge-new">NEW</span>
                                @elseif($proposal->status === 'in_process')
                                    <span class="badge badge-queued">IN PROCESS</span>
                                @elseif($proposal->status === 'on_review')
                                    <span class="badge badge-queued">IN REVIEW</span>
                                @else
                                    <span class="badge badge-queued">{{ strtoupper(str_replace('_', ' ', $proposal->status)) }}</span>
                                @endif
                            </td>
                                <td>
                                @php
                                    $isReviewed = isset($reviewStatuses[$proposal->id]) && $reviewStatuses[$proposal->id] === \App\Models\Review::STATUS_COMPLETED;
                                @endphp
                                @if($isReviewed)
                                    <div class="inline-flex items-center gap-2 text-sm text-slate-600">
                                        <span class="material-symbols-outlined" style="color: green;">check_circle</span>
                                        <span class="font-semibold text-sm" style="color: green;">Submitted</span>
                                    </div>
                                @else
                                    <a href="{{ route('reviewer.review-proposal.show', $proposal->id) }}">
                                        <button class="btn-review-now">Review Now</button>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <p class="text-gray-500">Tidak ada proposal untuk ditinjau saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="table-footer">
            <span class="page-info">Showing 1–{{ $proposals->count() }} of {{ $proposals->count() }} proposals</span>
            <div class="page-btns">
                <button class="page-btn active">1</button>
                @if($proposals->count() > 10)
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                @endif
            </div>
        </div>
    </div>

</div>{{-- /pm-wrap --}}

@endsection