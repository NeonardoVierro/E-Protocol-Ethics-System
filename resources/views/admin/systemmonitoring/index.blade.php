@extends('layouts.admin')

@section('title', 'System Monitoring - Admin')
@section('page-title', 'System Monitoring')
@section('breadcrumb', 'Kelola sistem monitoring untuk ethical clearance')

@section('content')


{{-- ═══════════════════════════════════════════
     Workflow Tracking
═══════════════════════════════════════════ --}}
<div class="mt-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div>
            <h2 class="text-[15px] font-bold text-slate-900">Workflow Tracking</h2>
            <p class="text-[12px] text-slate-500 mt-1">Lacak alur proposal mulai dari masuk hingga publikasi.</p>
        </div>
        <div class="text-right text-[13px] text-slate-600">
            <div>Total dipantau: <span class="font-semibold">{{ number_format($workflowProposals->count()) }}</span></div>
        </div>
    </div>

    <div class="px-6 py-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 text-[13px] text-slate-700">
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                <div class="text-slate-400 text-[10px] uppercase tracking-widest font-bold mb-2">Proposal Masuk</div>
                <div class="text-[24px] font-bold text-slate-900">{{ number_format($incomingProposals) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Proposal baru</div>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                <div class="text-slate-400 text-[10px] uppercase tracking-widest font-bold mb-2">Admin Receipt</div>
                <div class="text-[24px] font-bold text-slate-900">{{ number_format($adminReceivedCount) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Diterima admin</div>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                <div class="text-slate-400 text-[10px] uppercase tracking-widest font-bold mb-2">Sekretaris Assigned</div>
                <div class="text-[24px] font-bold text-slate-900">{{ number_format($sekreAssignedCount) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Terkirim ke sekre</div>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                <div class="text-slate-400 text-[10px] uppercase tracking-widest font-bold mb-2">Reviewer Assigned</div>
                <div class="text-[24px] font-bold text-slate-900">{{ number_format($reviewerAssignedCount) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Terkirim ke reviewer</div>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                <div class="text-slate-400 text-[10px] uppercase tracking-widest font-bold mb-2">Published</div>
                <div class="text-[24px] font-bold text-slate-900">{{ number_format($publishedProposals) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Proposal selesai</div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-separate border-spacing-y-2">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 text-[10px] font-bold tracking-wider uppercase text-slate-400">Proposal</th>
                    <th class="px-4 py-3 text-[10px] font-bold tracking-wider uppercase text-slate-400">Researcher</th>
                    <th class="px-4 py-3 text-[10px] font-bold tracking-wider uppercase text-slate-400">Admin</th>
                    <th class="px-4 py-3 text-[10px] font-bold tracking-wider uppercase text-slate-400">Sekretaris</th>
                    <th class="px-4 py-3 text-[10px] font-bold tracking-wider uppercase text-slate-400">Reviewer</th>
                    <th class="px-4 py-3 text-[10px] font-bold tracking-wider uppercase text-slate-400">Publish</th>
                    <th class="px-4 py-3 text-[10px] font-bold tracking-wider uppercase text-slate-400">Last Updated</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workflowProposals as $proposal)
                    @php
                        $adminReceived = $proposal->documentLogs->contains(fn($log) => $log->activity === \App\Models\DocumentLog::ACTIVITY_SENT_TO_ADMIN);
                        $sekreAssigned = $proposal->assignments->contains(fn($assignment) => $assignment->role === \App\Models\ProposalAssignment::ROLE_SEKRETARIS && $assignment->isSent());
                        $reviewerAssigned = $proposal->assignments->contains(fn($assignment) => $assignment->role === \App\Models\ProposalAssignment::ROLE_REVIEWER && $assignment->isSent());
                        $published = $proposal->status === \App\Models\Proposal::STATUS_PUBLISHED || $proposal->documentLogs->contains(fn($log) => $log->activity === \App\Models\DocumentLog::ACTIVITY_PUBLISH);
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3.5 text-[13px] font-semibold text-slate-900">{{ $proposal->nomor_ec ?: 'P-'.$proposal->id }}</td>
                        <td class="px-4 py-3.5 text-[13px] text-slate-600">{{ $proposal->researcher?->name ?? $proposal->nama_peneliti }}</td>
                        <td class="px-4 py-3.5 text-[13px]">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $adminReceived ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $adminReceived ? 'Received' : 'Pending' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-[13px]">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $sekreAssigned ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $sekreAssigned ? 'Assigned' : 'Pending' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-[13px]">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $reviewerAssigned ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $reviewerAssigned ? 'Assigned' : 'Pending' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-[13px]">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $published ? 'Published' : 'Open' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-[13px] text-slate-400">{{ optional($proposal->updated_at)->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-400">Tidak ada data workflow proposal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     Master Proposal Registry
═══════════════════════════════════════════ --}}
<div class="mt-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <h2 class="text-[15px] font-bold text-slate-900">Master Proposal Registry</h2>
        <a href="{{ route('admin.systemmonitoring.index') }}"
                class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[#1e3a5f] hover:text-[#162d4a] transition-colors">
            View All <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    <div class="px-6 py-6 border-b border-slate-100 bg-slate-50">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-[12px] font-semibold uppercase tracking-[0.18em] text-slate-400">Proposal Status</div>
                        <div class="mt-1 text-sm font-semibold text-slate-900">Breakdown</div>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ number_format(array_sum($proposalStatuses)) }}</span>
                </div>
                <div class="space-y-3">
                    @foreach($proposalStatuses as $status => $count)
                        <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <span class="text-sm font-medium text-slate-700">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                            <span class="text-sm font-semibold text-slate-900">{{ number_format($count) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-[12px] font-semibold uppercase tracking-[0.18em] text-slate-400">Ethics Document</div>
                        <div class="mt-1 text-sm font-semibold text-slate-900">Status</div>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ number_format(array_sum($ethicsStatuses)) }}</span>
                </div>
                <div class="space-y-3">
                    @foreach($ethicsStatuses as $status => $count)
                        <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <span class="text-sm font-medium text-slate-700">{{ $status === 'signed' ? 'Signed' : ucfirst($status) }}</span>
                            <span class="text-sm font-semibold text-slate-900">{{ number_format($count) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">Proposal ID</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Researcher</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Status</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Ethics Doc</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Last Updated</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($recentProposals as $proposal)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-3.5">
                            <a href="{{ route('admin.proposal.preview', $proposal) }}"
                                    class="text-[13.5px] font-bold text-[#1e3a5f] hover:underline">
                                {{ $proposal->nomor_ec ?: 'P-'.$proposal->id }}
                            </a>
                        </td>
                        <td class="px-4 py-3.5 text-[13px] text-slate-600">{{ $proposal->researcher?->name ?? $proposal->nama_peneliti }}</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase {{ $proposal->status_badge }}">
                                {{ $proposal->status_label }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($proposal->ethicsDocument)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase {{ $proposal->ethicsDocument->status_badge }}">
                                    {{ $proposal->ethicsDocument->status_label }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase bg-slate-100 text-slate-600">
                                    Missing
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-[13px] text-slate-400">{{ optional($proposal->updated_at)->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">Tidak ada proposal terbaru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
        <span class="text-[12.5px] text-slate-400">Showing {{ $recentProposals->count() }} latest entries</span>
        <div class="flex items-center gap-1">
            <button type="button"
                    class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors">
                <i class="fas fa-chevron-left text-xs"></i>
            </button>

            <button type="button"
                    class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     Real-time Activity Log
═══════════════════════════════════════════ --}}
<div class="mt-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
            <h2 class="text-[15px] font-bold text-slate-900">Real-time Activity Log</h2>
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        </div>
    </div>

    <div class="divide-y divide-slate-50 px-4 py-2">
        @forelse($recentActivities as $activity)
            @php
                $activityText = $activity->activity;
                switch ($activity->activity) {
                    case \App\Models\DocumentLog::ACTIVITY_UPLOAD:
                        $activityText = $activity->proposal ? sprintf('Proposal %s uploaded a document.', $activity->proposal->nomor_ec ?? '#'.$activity->proposal->id) : 'A document was uploaded.';
                        break;
                    case \App\Models\DocumentLog::ACTIVITY_SIGN:
                        $activityText = $activity->proposal ? sprintf('Ethics document signed for %s.', $activity->proposal->nomor_ec ?? '#'.$activity->proposal->id) : 'An ethics document was signed.';
                        break;
                    case \App\Models\DocumentLog::ACTIVITY_PUBLISH:
                        $activityText = $activity->proposal ? sprintf('Ethics clearance published for %s.', $activity->proposal->nomor_ec ?? '#'.$activity->proposal->id) : 'Ethics clearance was published.';
                        break;
                    case \App\Models\DocumentLog::ACTIVITY_ASSIGN:
                        $activityText = $activity->proposal ? sprintf('Assignment created for %s.', $activity->proposal->nomor_ec ?? '#'.$activity->proposal->id) : 'A new assignment was created.';
                        break;
                    case \App\Models\DocumentLog::ACTIVITY_SENT_TO_ADMIN:
                        $activityText = $activity->proposal ? sprintf('Proposal %s sent to admin.', $activity->proposal->nomor_ec ?? '#'.$activity->proposal->id) : 'Proposal sent to admin.';
                        break;
                }

                $activityIcon = match ($activity->activity) {
                    \App\Models\DocumentLog::ACTIVITY_UPLOAD => 'fas fa-file-arrow-up',
                    \App\Models\DocumentLog::ACTIVITY_SIGN => 'fas fa-pen-to-square',
                    \App\Models\DocumentLog::ACTIVITY_PUBLISH => 'fas fa-circle-check',
                    \App\Models\DocumentLog::ACTIVITY_ASSIGN => 'fas fa-user-plus',
                    \App\Models\DocumentLog::ACTIVITY_SENT_TO_ADMIN => 'fas fa-arrow-right-to-bracket',
                    default => 'fas fa-clock',
                };

                $iconColor = in_array($activity->activity, [\App\Models\DocumentLog::ACTIVITY_PUBLISH, \App\Models\DocumentLog::ACTIVITY_SIGN])
                    ? 'text-emerald-500'
                    : 'text-slate-500';
                $iconBg = in_array($activity->activity, [\App\Models\DocumentLog::ACTIVITY_PUBLISH, \App\Models\DocumentLog::ACTIVITY_SIGN])
                    ? 'bg-emerald-50'
                    : 'bg-slate-100';
            @endphp

            <div class="flex items-start gap-3 py-3.5">
                <div class="flex flex-col items-center flex-shrink-0">
                    <div class="w-8 h-8 {{ $iconBg }} rounded-full flex items-center justify-center border-l-2 border-slate-300 ring-0">
                        <i class="{{ $activityIcon }} {{ $iconColor }} text-xs"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-semibold text-[#1e3a5f] leading-snug">{{ $activityText }}</p>
                    <p class="text-[11.5px] text-slate-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}@if($activity->user) • {{ $activity->user->name }}@endif</p>
                </div>
            </div>
        @empty
            <div class="py-8 text-center text-slate-400">
                Tidak ada aktivitas terbaru.
            </div>
        @endforelse
    </div>
</div>

@endsection