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
            <button class="btn-filter" onclick="openFilterModal()">
                <i class="fas fa-sliders-h"></i> Filter Queue
            </button>
            <a href="{{ route('reviewer.proposal-masuk.export') }}">
                <button class="btn-export">
                    <i class="fas fa-download"></i> Export List
                </button>
            </a>
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
                            @php
                                $deadline = null;
                                if(isset($proposal->assignments)) {
                                    $deadline = $proposal->assignments->where('role', \App\Models\ProposalAssignment::ROLE_REVIEWER)->pluck('due_date')->filter()->min();
                                }
                                if(!$deadline) {
                                    $deadline = optional($proposal->review_deadline) ? optional($proposal->review_deadline)->toDateString() : null;
                                }
                            @endphp
                            <td><span class="date-deadline">{{ $deadline ? \Carbon\Carbon::createFromFormat('Y-m-d', \Carbon\Carbon::parse($deadline)->toDateString())->format('M d, Y') : 'N/A' }}</span></td>
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
                                    $needsRevisedReview = $proposal->hasRevisionSubmitted && (
                                        !$proposal->lastReviewCompletedDate ||
                                        $proposal->lastReviewCompletedDate < $proposal->lastSubmittedRevisionDate
                                    );
                                @endphp
                                @if($isReviewed && !$needsRevisedReview)
                                    <div class="inline-flex items-center gap-2 text-sm text-slate-600">
                                        <span class="material-symbols-outlined" style="color: green;">check_circle</span>
                                        <span class="font-semibold text-sm" style="color: green;">Submitted</span>
                                    </div>
                                @else
                                    {{-- Show special action if there's a submitted revision --}}
                                    @if(!empty($proposal->hasRevisionSubmitted) && $proposal->hasRevisionSubmitted)
                                        <a href="{{ route('reviewer.review-proposal.show', $proposal->id) }}">
                                            <button class="btn-review-now">Review Revised</button>
                                        </a>
                                    @elseif(!empty($proposal->hasRevisionRequested) && $proposal->hasRevisionRequested)
                                        <a href="{{ route('reviewer.review-proposal.show', $proposal->id) }}">
                                            <button class="btn-view-detail">View Revision Request</button>
                                        </a>
                                    @else
                                        <a href="{{ route('reviewer.review-proposal.show', $proposal->id) }}">
                                            <button class="btn-review-now">Review Now</button>
                                        </a>
                                    @endif
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
            <span class="page-info">Showing {{ $paginationData->from }}–{{ $paginationData->to }} of {{ $paginationData->total }} proposals</span>
            <div class="page-btns">
                {{-- Previous Button --}}
                @if($paginationData->current_page > 1)
                    <a href="{{ route('reviewer.proposal-masuk', ['page' => $paginationData->current_page - 1]) }}">
                        <button class="page-btn">
                            <i class="fas fa-chevron-left" style="font-size:.65rem"></i>
                        </button>
                    </a>
                @else
                    <button class="page-btn" disabled style="opacity: 0.5; cursor: not-allowed;">
                        <i class="fas fa-chevron-left" style="font-size:.65rem"></i>
                    </button>
                @endif

                {{-- Page Numbers --}}
                @php
                    $visiblePages = 5;
                    $halfVisible = floor($visiblePages / 2);
                    $startPage = max(1, $paginationData->current_page - $halfVisible);
                    $endPage = min($paginationData->last_page, $startPage + $visiblePages - 1);
                    
                    if ($endPage - $startPage + 1 < $visiblePages) {
                        $startPage = max(1, $endPage - $visiblePages + 1);
                    }
                @endphp

                @if($startPage > 1)
                    <a href="{{ route('reviewer.proposal-masuk', ['page' => 1]) }}">
                        <button class="page-btn">1</button>
                    </a>
                    @if($startPage > 2)
                        <span class="page-dots">…</span>
                    @endif
                @endif

                @for($i = $startPage; $i <= $endPage; $i++)
                    @if($i === $paginationData->current_page)
                        <button class="page-btn active">{{ $i }}</button>
                    @else
                        <a href="{{ route('reviewer.proposal-masuk', ['page' => $i]) }}">
                            <button class="page-btn">{{ $i }}</button>
                        </a>
                    @endif
                @endfor

                @if($endPage < $paginationData->last_page)
                    @if($endPage < $paginationData->last_page - 1)
                        <span class="page-dots">…</span>
                    @endif
                    <a href="{{ route('reviewer.proposal-masuk', ['page' => $paginationData->last_page]) }}">
                        <button class="page-btn">{{ $paginationData->last_page }}</button>
                    </a>
                @endif

                {{-- Next Button --}}
                @if($paginationData->current_page < $paginationData->last_page)
                    <a href="{{ route('reviewer.proposal-masuk', ['page' => $paginationData->current_page + 1]) }}">
                        <button class="page-btn">
                            <i class="fas fa-chevron-right" style="font-size:.65rem"></i>
                        </button>
                    </a>
                @else
                    <button class="page-btn" disabled style="opacity: 0.5; cursor: not-allowed;">
                        <i class="fas fa-chevron-right" style="font-size:.65rem"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>

</div>{{-- /pm-wrap --}}

{{-- Filter Modal --}}
<div id="filterModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeFilterModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-slate-200">
        <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-6 py-5">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Filter Proposals</h3>
                <p class="text-sm text-slate-500">Search and filter your review queue</p>
            </div>
            <button type="button" onclick="closeFilterModal()" class="rounded-full p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="filterForm" method="GET" action="{{ route('reviewer.proposal-masuk') }}" class="space-y-4 px-6 py-5">
            {{-- Search --}}
            <div>
                <label class="text-sm font-medium text-slate-700">Search Proposal</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, researcher..." class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-900 placeholder-slate-500 focus:border-slate-500 focus:outline-none" />
            </div>

            {{-- Status Filter --}}
            <div>
                <label class="text-sm font-medium text-slate-700">Status</label>
                <select name="status" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-900 focus:border-slate-500 focus:outline-none">
                    <option value="">All Status</option>
                    <option value="new_proposal" {{ request('status') === 'new_proposal' ? 'selected' : '' }}>New Proposal</option>
                    <option value="in_process" {{ request('status') === 'in_process' ? 'selected' : '' }}>In Process</option>
                    <option value="on_review" {{ request('status') === 'on_review' ? 'selected' : '' }}>On Review</option>
                    <option value="revision_requested" {{ request('status') === 'revision_requested' ? 'selected' : '' }}>Revision Requested</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>

            {{-- Urgency Filter --}}
            <div>
                <label class="text-sm font-medium text-slate-700">Urgency</label>
                <select name="urgency" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-900 focus:border-slate-500 focus:outline-none">
                    <option value="">All Proposals</option>
                    <option value="urgent" {{ request('urgency') === 'urgent' ? 'selected' : '' }}>Urgent (Last 24h)</option>
                    <option value="not_urgent" {{ request('urgency') === 'not_urgent' ? 'selected' : '' }}>Not Urgent</option>
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3 pt-3">
                <button type="button" onclick="resetFilters()" class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Reset
                </button>
                <button type="submit" class="flex-1 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openFilterModal() {
    document.getElementById('filterModal').style.display = 'flex';
}

function closeFilterModal() {
    document.getElementById('filterModal').style.display = 'none';
}

function resetFilters() {
    document.getElementById('filterForm').reset();
    window.location.href = '{{ route("reviewer.proposal-masuk") }}';
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('filterModal');
    const filterBtn = event.target.closest('.btn-filter');
    if (modal && !modal.contains(event.target) && !filterBtn) {
        closeFilterModal();
    }
});
</script>

@endsection