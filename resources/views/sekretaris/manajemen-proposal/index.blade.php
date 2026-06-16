@extends('layouts.sekretaris')

@section('title', 'Manajemen Proposal')
@section('page-title', 'Manajemen Proposal')
@section('breadcrumb', 'Cek Kelengkapan Dokumen')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-xl font-semibold text-slate-900">Manajemen Proposal</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola Proposal dan Tentukan Reviewer.</p>
        </div>

        <div class="px-6 py-4 space-y-4">
            <div class="flex flex-wrap gap-2">
                @php
                    $tabs = [
                        'in_process' => 'In Process',
                        'on_review' => 'On Review',
                        'revision_required' => 'Revision Required',
                        'revised' => 'Revised',
                        'approved' => 'Approved',
                        'disapproved' => 'Disapproved',
                    ];
                @endphp

                @foreach($tabs as $key => $label)
                    <a href="{{ route('sekretaris.manajemen-proposal', ['status' => $key, 'search' => $search]) }}"
                       class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition-all duration-150 {{ $status === $key ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <span>{{ $label }}</span>
                        <span class="inline-flex h-6 min-w-[28px] items-center justify-center rounded-full bg-white px-2 text-xs font-semibold text-slate-900 shadow-sm">{{ $statusCounts[$key] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('sekretaris.manajemen-proposal') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input
                        name="search"
                        value="{{ $search }}"
                        type="text"
                        class="w-full rounded-2xl border border-slate-300 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-900 outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                        placeholder="Search by title or on behalf..."
                    />
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700 transition-colors duration-150">
                    Search
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.02em]">Title</th>
                    <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.02em]">On Behalf</th>
                    <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.02em]">Status</th>
                    <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.02em]">Round</th>
                    <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.02em]">Submitted</th>
                    <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.02em]">Review Progress</th>
                    <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.02em]">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($proposals as $proposal)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 max-w-[300px] text-slate-900">
                            <div class="flex items-center gap-2">
                                <div class="font-semibold">{{ Str::limit($proposal->title, 50) }}</div>
                                @if($proposal->revisions_count > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-semibold">
                                    Vol.{{ $proposal->revisions_count + 1 }}
                                </span>
                                @endif
                            </div>
                            <div class="mt-1 text-xs text-slate-500">{{ Str::limit($proposal->description ?? '-', 60) }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-700">{{ $proposal->researcher->name ?? $proposal->nama_peneliti ?? 'Unknown' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $proposal->status_badge ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $proposal->status_label ?? ucfirst(str_replace('_', ' ', $proposal->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-700">{{ $proposal->revisions_count ?? 0 }}</td>
                        <td class="px-6 py-4 text-slate-700">{{ $proposal->submission_date ? $proposal->submission_date->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-4 text-slate-700">
                            @if($proposal->status === App\Models\Proposal::STATUS_ON_REVIEW)
                                On Review
                            @elseif($proposal->status === App\Models\Proposal::STATUS_REVISED)
                                Revision Submitted
                            @elseif($proposal->status === App\Models\Proposal::STATUS_APPROVED)
                                Approved
                            @elseif($proposal->status === App\Models\Proposal::STATUS_REJECTED)
                                Disapproved
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $showProcessing = $proposal->status === App\Models\Proposal::STATUS_IN_PROCESS
                                    || $proposal->status === App\Models\Proposal::STATUS_ON_REVIEW
                                    || ($proposal->status === App\Models\Proposal::STATUS_REVISED && $proposal->revisions()->where('status', App\Models\ProposalRevision::STATUS_SUBMITTED)->exists());
                            @endphp

                            @if($showProcessing)
                                <a href="{{ route('sekretaris.proposal.show', $proposal) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-500 text-white hover:bg-amber-600 transition-colors duration-150">
                                    <i class="fas fa-cog"></i>
                                </a>
                            @else
                                <a href="{{ route('sekretaris.proposal.show', $proposal) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-200 text-slate-600 cursor-default">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">Tidak ada proposal sesuai filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection