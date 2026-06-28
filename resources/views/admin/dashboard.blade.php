@extends('layouts.admin')

@section('title', 'Dashboard - Admin')
@section('page-title', 'Admin Overview')

@section('header-action')
    <button class="inline-flex items-center gap-1.5 text-[12.5px] font-medium text-[#6b7280] cursor-default border-none bg-transparent p-0 hover:text-[#374151] transition-colors" type="button">
        <i class="fas fa-calendar-days text-[12px] text-[#9ca3af]"></i>
        <span>Filter Tanggal</span>
    </button>
@endsection

@section('content')

{{-- ═══════════════════════════════════════════
     ROW 1: Chart (kiri lebar) + Stat Cards (kanan)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-5">

    {{-- Proposal Volume Trends --}}
    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col">
        <div class="flex items-center justify-between px-6 pt-5 pb-2">
            <span class="text-[15px] font-bold text-slate-900 tracking-tight">Proposal Volume Trends</span>
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Approved
                </span>
                <span class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-slate-800"></span> Pending
                </span>
            </div>
        </div>
        <div class="flex-1 px-5 pb-2 pt-1" style="min-height:200px; position:relative;">
            <canvas id="proposalChart" style="width:100%; height:210px;"></canvas>
        </div>
        <div class="flex justify-between px-7 pb-4">
            @foreach($proposalChartLabels as $label)
                <span class="text-[10.5px] font-semibold text-slate-400 tracking-wider">{{ $label }}</span>
            @endforeach
        </div>
    </div>

    {{-- Stat Cards kanan --}}
    <div class="lg:col-span-5 flex flex-col gap-5">

        {{-- Total Proposals --}}
        <div class="flex-1 rounded-2xl p-6 flex flex-col justify-between" style="background:#0f2744;">
            <p class="text-[10px] font-bold tracking-widest uppercase" style="color:rgba(255,255,255,0.5);">Total Proposals</p>
            <div>
                <p class="text-[40px] font-bold text-white tracking-tight leading-none mb-3">{{ number_format($totalProposals) }}</p>
                <span class="inline-flex items-center gap-1.5 text-emerald-400 text-[13px] font-medium">
                    <i class="fas fa-arrow-trend-up text-xs"></i>
                    {{ number_format($approvedProposals) }} approved / {{ number_format($pendingProposals) }} pending
                </span>
            </div>
        </div>

        {{-- Active Reviewers --}}
        <div class="flex-1 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Active Reviewers</p>
            <div>
                <p class="text-[40px] font-bold text-slate-900 tracking-tight leading-none mb-3">{{ number_format($activeReviewers) }}</p>
                <div class="text-[13px] text-slate-500">Reviewer aktif saat ini</div>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════
     ROW 2: User Management (kiri) + System Monitoring (kanan)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-5">

    {{-- User Management --}}
    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <span class="text-[15px] font-bold text-slate-900">User Management</span>
            <a href="{{ route('admin.usermanagement.index') }}"
               class="inline-flex items-center py-2 px-4 border border-slate-200 rounded-xl text-[13px] font-semibold text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-colors bg-white">
                View All
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]" id="user-table">
                <thead class="bg-slate-50">
                <tr>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">User</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Role</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Status</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Last Active</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($recentUsers as $u)
                    @php
                        $nameWords = preg_split('/\s+/', trim($u->name));
                        $initials = '';
                        foreach ($nameWords as $word) {
                            if ($word === '') {
                                continue;
                            }
                            $initials .= strtoupper(substr($word, 0, 1));
                            if (strlen($initials) >= 2) {
                                break;
                            }
                        }

                        $roleLabel = strtoupper($u->primary_role);
                        $roleClass = match($u->primary_role) {
                            'admin' => 'bg-[#1e3a5f] text-white',
                            'peneliti' => 'bg-slate-100 text-slate-600',
                            'sekretaris' => 'bg-amber-50 text-amber-700',
                            'reviewer' => 'bg-indigo-50 text-indigo-700',
                            'ketua' => 'bg-indigo-50 text-indigo-700',
                            default => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white text-xs font-bold flex-shrink-0">{{ $initials ?: 'US' }}</div>
                                <div>
                                    <div class="text-[13.5px] font-semibold text-slate-800">{{ $u->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase {{ $roleClass }}">{{ $roleLabel }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($u->status === 'active')
                                <span class="inline-flex items-center gap-1.5 text-[13px] font-medium text-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_0_3px_#d1fae5]"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-[13px] font-medium text-slate-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-[13px] text-slate-500">{{ $u->updated_at ? $u->updated_at->diffForHumans() : '-' }}</td>
                        <td class="px-4 py-3.5">
                            <a href="{{ route('admin.usermanagement.index') }}"
                                    class="w-7 h-7 rounded-md inline-flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                                <i class="fas fa-ellipsis-vertical text-sm"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-500">Tidak ada user terbaru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- System Monitoring --}}
    <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col overflow-hidden">
        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-100">
            <span class="text-[15px] font-bold text-slate-900">System Monitoring</span>
            <span class="inline-flex items-center gap-1.5 text-[9.5px] font-bold tracking-wider uppercase text-emerald-600 bg-emerald-50 rounded-full px-2 py-0.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live Logs
            </span>
        </div>

        <div class="flex-1 divide-y divide-slate-50 px-6">
            @forelse($systemLogs as $log)
                @php
                    $subject = $log->description ?: ($log->proposal ? 'Proposal ' . ($log->proposal->nomor_ec ? '#'.$log->proposal->nomor_ec : '#'.$log->proposal->id) : ucfirst($log->activity_label));
                    $actor = $log->user?->name ?? 'System';
                    $iconColor = 'text-slate-500';
                    $iconBg = 'bg-slate-100';
                    $icon = 'fa-circle-info';

                    if (in_array($log->activity, [\App\Models\DocumentLog::ACTIVITY_PUBLISH, \App\Models\DocumentLog::ACTIVITY_VERIFY, \App\Models\DocumentLog::ACTIVITY_SIGN])) {
                        $icon = 'fa-circle-check';
                        $iconBg = 'bg-emerald-50';
                        $iconColor = 'text-emerald-500';
                    } elseif (in_array($log->activity, [\App\Models\DocumentLog::ACTIVITY_ASSIGN, \App\Models\DocumentLog::ACTIVITY_UPLOAD])) {
                        $icon = 'fa-user-plus';
                        $iconBg = 'bg-blue-50';
                        $iconColor = 'text-blue-500';
                    } elseif ($log->activity === \App\Models\DocumentLog::ACTIVITY_UPDATE) {
                        $icon = 'fa-pen';
                        $iconBg = 'bg-amber-50';
                        $iconColor = 'text-amber-500';
                    }
                @endphp
                <div class="flex items-start gap-3 py-4">
                    <div class="w-8 h-8 rounded-full {{ $iconBg }} flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas {{ $icon }} {{ $iconColor }} text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[13.5px] text-slate-700 leading-snug">{{ $subject }}</p>
                        <p class="text-[11.5px] text-slate-400 mt-0.5">{{ $log->created_at->diffForHumans() }} &bull; <span class="text-slate-500">{{ $actor }}</span></p>
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-slate-500">Tidak ada aktivitas sistem terbaru.</div>
            @endforelse
        </div>

        <div class="px-6 pb-5 pt-2">
            <a href="{{ route('admin.systemmonitoring.index') }}"
                    class="w-full inline-flex items-center justify-center py-2 border border-slate-200 rounded-xl text-[13px] font-semibold text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-colors bg-white">
                View All Audit Logs
            </a>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════
     ROW 3: Quick Access Cards (4 kolom)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    @php
    $quickCards = [
        ['icon'=>'fas fa-clipboard-list','title'=>'EC Assignments',   'sub'=>'Bulk assign chairs', 'route'=>route('admin.proposal-assignment.index')],
        ['icon'=>'fas fa-clipboard-list','title'=>'EC Assignments',   'sub'=>'Bulk assign chairs', 'route'=>route('admin.proposal-assignment.index')],
        ['icon'=>'fas fa-pen-to-square', 'title'=>'Template Editor',  'sub'=>'Manage forms',       'route'=>route('admin.templateproposal.index')],
        ['icon'=>'fas fa-rotate',        'title'=>'Publication Sync', 'sub'=>'Global repo sync',   'route'=>route('admin.publishing.index')],
    ];
    @endphp
    @foreach($quickCards as $card)
    <a href="{{ $card['route'] }}"
            class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 hover:shadow-md hover:border-slate-300 hover:-translate-y-0.5 transition-all text-left group">
        <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-slate-100 transition-colors">
            <i class="{{ $card['icon'] }} text-slate-500 text-base"></i>
        </div>
        <div>
            <div class="text-[13.5px] font-semibold text-slate-800">{{ $card['title'] }}</div>
            <div class="text-[11.5px] text-slate-400 mt-0.5">{{ $card['sub'] }}</div>
        </div>
    </a>
    @endforeach
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Chart ─────────────────────────────────────
    const ctx = document.getElementById('proposalChart');
    if (ctx) {
        const chartLabels = @json($proposalChartLabels);
        const approvedTrend = @json($proposalTrendApproved);
        const pendingTrend = @json($proposalTrendPending);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Approved',
                        data: approvedTrend,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.07)',
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'Pending',
                        data: pendingTrend,
                        borderColor: '#0f2744',
                        backgroundColor: 'rgba(15,39,68,0.04)',
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#0f2744',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        tension: 0.4,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f1e2e',
                        titleColor: 'rgba(255,255,255,0.55)',
                        bodyColor: '#fff',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: true,
                        boxWidth: 8, boxHeight: 8, boxPadding: 4,
                    },
                },
                scales: {
                    x: { display: false },
                    y: {
                        display: true,
                        grid: { color: '#f1f5f9' },
                        border: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 11 }, maxTicksLimit: 5 },
                    },
                },
            },
        });
    }

});
</script>
@endpush