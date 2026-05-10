@extends('layouts.admin')

@section('title', 'System Monitoring - Admin')
@section('page-title', 'System Monitoring')
@section('breadcrumb', 'Kelola sistem monitoring untuk ethical clearance')

@section('content')

{{-- ═══════════════════════════════════════════
     Stat Cards (4 kolom)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-4 gap-5 mb-5">

    {{-- Active Sessions --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-2">Active Sessions</p>
            <p class="text-[32px] font-bold text-slate-900 leading-none tracking-tight">156</p>
            <p class="text-[11.5px] text-slate-400 mt-1">Users Online</p>
        </div>
        <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-users text-blue-500 text-base"></i>
        </div>
    </div>

    {{-- System Uptime --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-2">System Uptime</p>
            <p class="text-[32px] font-bold text-slate-900 leading-none tracking-tight">99.98%</p>
            <p class="text-[11.5px] text-slate-400 mt-1">Last 30 Days</p>
        </div>
        <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-circle-check text-emerald-500 text-base"></i>
        </div>
    </div>

    {{-- Daily Requests --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-2">Daily Requests</p>
            <p class="text-[32px] font-bold text-slate-900 leading-none tracking-tight">2,450</p>
            <p class="text-[11.5px] text-slate-400 mt-1">Interactions Today</p>
        </div>
        <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-arrow-right-arrow-left text-orange-400 text-base"></i>
        </div>
    </div>

    {{-- Storage Usage --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-2">Storage Usage</p>
            <p class="text-[32px] font-bold text-slate-900 leading-none tracking-tight">45.2 <span class="text-[18px]">GB</span></p>
            <p class="text-[11.5px] text-slate-400 mt-1 mb-2">of 100 GB</p>
            {{-- Progress bar --}}
            <div class="w-full bg-slate-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full bg-emerald-500" style="width: 45.2%"></div>
            </div>
        </div>
        <div class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="fas fa-database text-slate-500 text-base"></i>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════
     ROW 2: Activity Log (kiri) + Proposal Registry (kanan)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-12 gap-5">

    {{-- Real-time Activity Log --}}
    <div class="col-span-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <h2 class="text-[15px] font-bold text-slate-900">Real-time Activity Log</h2>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
        </div>

        <div class="flex-1 divide-y divide-slate-50 px-4 py-2">
            @php
            $logs = [
                [
                    'icon'      => 'fas fa-arrow-right-to-bracket',
                    'iconColor' => 'text-slate-500',
                    'iconBg'    => 'bg-slate-100',
                    'border'    => 'border-slate-300',
                    'text'      => 'Dr. Sarah Jenkins logged in',
                    'time'      => '2 mins ago',
                    'link'      => true,
                ],
                [
                    'icon'      => 'fas fa-circle-check',
                    'iconColor' => 'text-emerald-500',
                    'iconBg'    => 'bg-emerald-50',
                    'border'    => 'border-emerald-400',
                    'text'      => 'Proposal EC-2023-0942 status changed to APPROVED',
                    'time'      => '15 mins ago',
                    'link'      => true,
                ],
                [
                    'icon'      => 'fas fa-file-arrow-up',
                    'iconColor' => 'text-orange-400',
                    'iconBg'    => 'bg-orange-50',
                    'border'    => 'border-orange-400',
                    'text'      => 'New Template "Biomedis v3.0" uploaded by Admin',
                    'time'      => '1 hour ago',
                    'link'      => true,
                ],
                [
                    'icon'      => 'fas fa-rotate-right',
                    'iconColor' => 'text-slate-500',
                    'iconBg'    => 'bg-slate-100',
                    'border'    => 'border-slate-300',
                    'text'      => 'User "Prof. Aris" reset password',
                    'time'      => '3 hours ago',
                    'link'      => false,
                ],
                [
                    'icon'      => 'fas fa-file-lines',
                    'iconColor' => 'text-slate-500',
                    'iconBg'    => 'bg-slate-100',
                    'border'    => 'border-slate-300',
                    'text'      => 'System auto-backup initiated',
                    'time'      => '4 hours ago',
                    'link'      => false,
                ],
            ];
            @endphp

            @foreach($logs as $log)
            <div class="flex items-start gap-3 py-3.5">
                {{-- Left border accent + icon --}}
                <div class="flex flex-col items-center flex-shrink-0">
                    <div class="w-8 h-8 {{ $log['iconBg'] }} rounded-full flex items-center justify-center border-l-2 {{ $log['border'] }} ring-0">
                        <i class="{{ $log['icon'] }} {{ $log['iconColor'] }} text-xs"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    @if($log['link'])
                        <button onclick="featureInDevelopment('Activity Detail')"
                                class="text-[13px] font-semibold text-[#1e3a5f] hover:underline text-left leading-snug cursor-pointer">
                            {{ $log['text'] }}
                        </button>
                    @else
                        <p class="text-[13px] text-slate-600 leading-snug">{{ $log['text'] }}</p>
                    @endif
                    <p class="text-[11.5px] text-slate-400 mt-0.5">{{ $log['time'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Master Proposal Registry --}}
    <div class="col-span-7 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="text-[15px] font-bold text-slate-900">Master Proposal Registry</h2>
            <button onclick="featureInDevelopment('View All')"
                    class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[#1e3a5f] hover:text-[#162d4a] transition-colors cursor-pointer">
                View All <i class="fas fa-arrow-right text-xs"></i>
            </button>
        </div>

        <div class="flex-1 overflow-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">Proposal ID</th>
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Researcher</th>
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Category</th>
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Status</th>
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Last Activity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                    $proposals = [
                        ['id'=>'EC-2023-0891','researcher'=>'Dr. Helena Vane',  'category'=>'Clinical Research', 'status'=>'APPROVED',  'statusClass'=>'bg-emerald-50 text-emerald-700','activity'=>'20 Oct 2023, 14:20'],
                        ['id'=>'EC-2023-0902','researcher'=>'Prof. Simon K.',   'category'=>'Behavioral Study',  'status'=>'IN REVIEW', 'statusClass'=>'bg-blue-50 text-blue-700',    'activity'=>'21 Oct 2023, 09:15'],
                        ['id'=>'EC-2023-0915','researcher'=>'Dr. Aris Sudarmo','category'=>'Genomics',          'status'=>'REJECTED',  'statusClass'=>'bg-red-50 text-red-600',      'activity'=>'21 Oct 2023, 16:45'],
                        ['id'=>'EC-2023-0942','researcher'=>'Dr. Sarah Jenkins','category'=>'Biomedical',       'status'=>'PENDING',   'statusClass'=>'bg-slate-100 text-slate-600', 'activity'=>'Just Now'],
                        ['id'=>'EC-2023-0850','researcher'=>'Prof. Liam Neeson','category'=>'Sociology',        'status'=>'APPROVED',  'statusClass'=>'bg-emerald-50 text-emerald-700','activity'=>'18 Oct 2023, 11:30'],
                    ];
                    @endphp

                    @foreach($proposals as $p)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-3.5">
                            <button onclick="featureInDevelopment('Detail Proposal {{ $p['id'] }}')"
                                    class="text-[13.5px] font-bold text-[#1e3a5f] hover:underline cursor-pointer">
                                {{ $p['id'] }}
                            </button>
                        </td>
                        <td class="px-4 py-3.5 text-[13px] text-slate-600">{{ $p['researcher'] }}</td>
                        <td class="px-4 py-3.5 text-[13px] text-slate-500">{{ $p['category'] }}</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase {{ $p['statusClass'] }}">
                                {{ $p['status'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-[13px] text-slate-400">{{ $p['activity'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
            <span class="text-[12.5px] text-slate-400">Showing 5 of <span class="font-semibold text-slate-600">1,280</span> entries</span>
            <div class="flex items-center gap-1">
                <button onclick="featureInDevelopment('Previous')"
                        class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors cursor-pointer">
                    <i class="fas fa-chevron-left text-xs"></i>
                </button>
                <button onclick="featureInDevelopment('Next')"
                        class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors cursor-pointer">
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>

</div>

@endsection