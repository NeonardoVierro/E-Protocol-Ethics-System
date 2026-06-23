@extends('layouts.admin')

@section('title', 'Ketua Panel')

@section('sidebar')
<div class="w-64 min-h-screen bg-white border-r border-[#e8ecf0] fixed left-0 top-0 flex flex-col z-[100]">
    <div class="flex items-center gap-2.5 px-5 py-5 pb-[18px] border-b border-[#f0f2f5]">
        <div class="w-[38px] h-[38px] bg-[#1a3a5c] rounded-xl flex items-center justify-center shrink-0">
            <i class="fas fa-shield-halved text-white text-[15px]"></i>
        </div>
        <div>
            <span class="text-[15px] font-bold text-[#0f1e2e] leading-tight tracking-[-0.2px]">Ethics Portal</span>
            <span class="text-[9.5px] font-semibold tracking-[0.9px] uppercase text-[#94a3b8] block">Ketua</span>
        </div>
    </div>

    <nav class="flex-1 p-4 px-3 flex flex-col gap-0.5">
        <a href="{{ route('ketua.dashboard') }}"
           class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('ketua.dashboard') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-tachometer-alt text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('ketua.dashboard') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Dashboard
        </a>

        <a href="{{ route('ketua.persetujuan-ttd') }}"
           class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('ketua.persetujuan-ttd') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-pen-fancy text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('ketua.persetujuan-ttd') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Persetujuan & TTD
        </a>

        <div class="flex-1"></div>
    </nav>
</div>
@endsection
