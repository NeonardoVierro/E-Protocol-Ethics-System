@extends('layouts.admin')

@section('title', 'Dashboard - Admin')
@section('page-title', 'Admin Overview')

@section('header-action')
    <button class="inline-flex items-center gap-1.5 text-[12.5px] font-medium text-[#6b7280] cursor-pointer border-none bg-transparent p-0 hover:text-[#374151] transition-colors" 
            onclick="showToast('Date Filter sedang dalam pengembangan', 'info')">
        <i class="fas fa-calendar-days text-[12px] text-[#9ca3af]"></i>
        <span>Filter Tanggal</span>
    </button>
@endsection

@section('content')

{{-- ═══════════════════════════════════════════
     ROW 1: Chart (kiri lebar) + Stat Cards (kanan)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-12 gap-5 mb-5">

    {{-- Proposal Volume Trends --}}
    <div class="col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col">
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
            @foreach(['MON','TUE','WED','THU','FRI'] as $d)
                <span class="text-[10.5px] font-semibold text-slate-400 tracking-wider">{{ $d }}</span>
            @endforeach
        </div>
    </div>

    {{-- Stat Cards kanan --}}
    <div class="col-span-5 flex flex-col gap-5">

        {{-- Total Proposals --}}
        <div class="flex-1 rounded-2xl p-6 flex flex-col justify-between" style="background:#0f2744;">
            <p class="text-[10px] font-bold tracking-widest uppercase" style="color:rgba(255,255,255,0.5);">Total Proposals</p>
            <div>
                <p class="text-[40px] font-bold text-white tracking-tight leading-none mb-3">1,248</p>
                <span class="inline-flex items-center gap-1.5 text-emerald-400 text-[13px] font-medium">
                    <i class="fas fa-arrow-trend-up text-xs"></i>
                    +12.5% from last month
                </span>
            </div>
        </div>

        {{-- Active Reviewers --}}
        <div class="flex-1 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Active Reviewers</p>
            <div>
                <p class="text-[40px] font-bold text-slate-900 tracking-tight leading-none mb-3">84</p>
                <div class="flex items-center">
                    @foreach([['AM','bg-blue-600'],['SL','bg-emerald-600'],['JM','bg-amber-500']] as $i => $av)
                        <div class="w-7 h-7 rounded-full {{ $av[1] }} {{ $i>0?'-ml-2':'' }} border-2 border-white flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">{{ $av[0] }}</div>
                    @endforeach
                    <div class="-ml-2 w-7 h-7 rounded-full border-2 border-white flex items-center justify-center text-[10px] font-bold bg-slate-100 text-slate-600">+80</div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════
     ROW 2: User Management (kiri) + System Monitoring (kanan)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-12 gap-5 mb-5">

    {{-- User Management --}}
    <div class="col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <span class="text-[15px] font-bold text-slate-900">User Management</span>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="fas fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px] pointer-events-none"></i>
                    <input type="text" placeholder="Search users..."
                           id="user-search"
                           class="pl-7 pr-3 py-1.5 text-[12.5px] bg-slate-50 border border-slate-200 rounded-lg outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all w-44 text-slate-700 placeholder-slate-400">
                </div>
                <button onclick="featureInDevelopment('Filter')"
                        class="w-8 h-8 border border-slate-200 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer">
                    <i class="fas fa-sliders text-xs"></i>
                </button>
            </div>
        </div>

        {{-- Table --}}
        <table class="w-full" id="user-table">
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
                @php
                $users = [
                    ['initials'=>'AM','color'=>'bg-blue-600',  'name'=>'Arthur Morgan', 'email'=>'a.morgan@university.edu','role'=>'RESEARCHER',  'roleClass'=>'bg-emerald-50 text-emerald-700','status'=>'active',  'last'=>'2 mins ago'],
                    ['initials'=>'SL','color'=>'bg-slate-500', 'name'=>'Sadie Linton',  'email'=>'s.linton@ethics.gov',     'role'=>'ETHICS CHAIR','roleClass'=>'bg-blue-50 text-blue-700',    'status'=>'active',  'last'=>'1 hour ago'],
                    ['initials'=>'JM','color'=>'bg-amber-500', 'name'=>'John Marston',  'email'=>'j.marston@labs.io',        'role'=>'REVIEWER',    'roleClass'=>'bg-slate-100 text-slate-600', 'status'=>'inactive','last'=>'3 days ago'],
                ];
                @endphp
                @foreach($users as $u)
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full {{ $u['color'] }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">{{ $u['initials'] }}</div>
                            <div>
                                <div class="text-[13.5px] font-semibold text-slate-800">{{ $u['name'] }}</div>
                                <div class="text-[11px] text-slate-400">{{ $u['email'] }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase {{ $u['roleClass'] }}">{{ $u['role'] }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        @if($u['status'] === 'active')
                            <span class="inline-flex items-center gap-1.5 text-[13px] font-medium text-slate-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_0_3px_#d1fae5]"></span> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-[13px] font-medium text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-[13px] text-slate-500">{{ $u['last'] }}</td>
                    <td class="px-4 py-3.5">
                        <button onclick="featureInDevelopment('User Actions')"
                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer">
                            <i class="fas fa-ellipsis-vertical text-sm"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- System Monitoring --}}
    <div class="col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col overflow-hidden">
        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-100">
            <span class="text-[15px] font-bold text-slate-900">System Monitoring</span>
            <span class="inline-flex items-center gap-1.5 text-[9.5px] font-bold tracking-wider uppercase text-emerald-600 bg-emerald-50 rounded-full px-2 py-0.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live Logs
            </span>
        </div>

        <div class="flex-1 divide-y divide-slate-50 px-6">
            @php
            $logs = [
                ['icon'=>'fa-circle-check', 'iconBg'=>'bg-emerald-50','iconColor'=>'text-emerald-500',
                 'text'=>'Proposal <strong>#EC-9942</strong> published',
                 'meta'=>'14 minutes ago', 'by'=>'System Admin'],
                ['icon'=>'fa-user-plus',    'iconBg'=>'bg-blue-50',   'iconColor'=>'text-blue-500',
                 'text'=>'New Reviewer assigned to <strong>#EC-1022</strong>',
                 'meta'=>'42 minutes ago', 'by'=>'Ethics Chair'],
                ['icon'=>'fa-triangle-exclamation','iconBg'=>'bg-amber-50','iconColor'=>'text-amber-500',
                 'text'=>'Template <strong>"Bio-Security v2"</strong> updated',
                 'meta'=>'2 hours ago', 'by'=>'Super Admin'],
            ];
            @endphp
            @foreach($logs as $log)
            <div class="flex items-start gap-3 py-4">
                <div class="w-8 h-8 rounded-full {{ $log['iconBg'] }} flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas {{ $log['icon'] }} {{ $log['iconColor'] }} text-sm"></i>
                </div>
                <div>
                    <p class="text-[13.5px] text-slate-700 leading-snug">{!! $log['text'] !!}</p>
                    <p class="text-[11.5px] text-slate-400 mt-0.5">{{ $log['meta'] }} &bull; <span class="text-slate-500">{{ $log['by'] }}</span></p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-6 pb-5 pt-2">
            <button onclick="featureInDevelopment('Audit Logs')"
                    class="w-full py-2 border border-slate-200 rounded-xl text-[13px] font-semibold text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-colors cursor-pointer bg-white">
                View All Audit Logs
            </button>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════
     ROW 3: Quick Access Cards (4 kolom)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-4 gap-4">
    @php
    $quickCards = [
        ['icon'=>'fas fa-key',           'title'=>'Role Matrix',      'sub'=>'Edit permissions',   'feature'=>'Role Matrix'],
        ['icon'=>'fas fa-clipboard-list','title'=>'EC Assignments',   'sub'=>'Bulk assign chairs', 'feature'=>'EC Assignments'],
        ['icon'=>'fas fa-pen-to-square', 'title'=>'Template Editor',  'sub'=>'Manage forms',       'feature'=>'Template Editor'],
        ['icon'=>'fas fa-rotate',        'title'=>'Publication Sync', 'sub'=>'Global repo sync',   'feature'=>'Publication Sync'],
    ];
    @endphp
    @foreach($quickCards as $card)
    <button onclick="featureInDevelopment('{{ $card['feature'] }}')"
            class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 hover:shadow-md hover:border-slate-300 hover:-translate-y-0.5 transition-all text-left group cursor-pointer">
        <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-slate-100 transition-colors">
            <i class="{{ $card['icon'] }} text-slate-500 text-base"></i>
        </div>
        <div>
            <div class="text-[13.5px] font-semibold text-slate-800">{{ $card['title'] }}</div>
            <div class="text-[11.5px] text-slate-400 mt-0.5">{{ $card['sub'] }}</div>
        </div>
    </button>
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
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon','Tue','Wed','Thu','Fri'],
                datasets: [
                    {
                        label: 'Approved',
                        data: [62, 78, 55, 91, 84],
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
                        data: [40, 55, 47, 66, 52],
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

    // ── Live search tabel ──────────────────────────
    const searchInput = document.getElementById('user-search');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#user-table tbody tr').forEach(row => {
                row.style.display = !q || row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

});
</script>
@endpush