@php
$logs = $logs ?? ($proposal?->documentLogs()->latest()->get() ?? collect());
@endphp

<div class="space-y-3 text-sm text-slate-700">
    @forelse($logs as $log)
        <div class="border border-slate-100 rounded-lg p-3 bg-white">
            <div class="text-slate-900 font-semibold">{{ $log->activityLabel ?? ucfirst($log->activity) }}</div>
            <div class="text-xs text-slate-500">{{ $log->description ?? '' }}</div>
            <div class="text-xs text-slate-400 mt-1">{{ optional($log->created_at)->format('d M Y H:i') }}</div>
        </div>
    @empty
        <div class="text-sm text-slate-500">No activity yet.</div>
    @endforelse
</div>