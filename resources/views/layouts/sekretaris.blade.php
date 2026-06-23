@extends('layouts.admin')

@section('title', 'Sekretaris Panel')

@section('sidebar')
<div class="w-64 min-h-screen bg-white border-r border-[#e8ecf0] fixed left-0 top-0 flex flex-col z-[100]">
    <div class="flex items-center gap-2.5 px-5 py-5 pb-[18px] border-b border-[#f0f2f5]">
        <div class="w-[38px] h-[38px] bg-[#1a3a5c] rounded-xl flex items-center justify-center shrink-0">
            <i class="fas fa-shield-halved text-white text-[15px]"></i>
        </div>
        <div>
            <span class="text-[15px] font-bold text-[#0f1e2e] leading-tight tracking-[-0.2px]">Ethics Portal</span>
            <span class="text-[9.5px] font-semibold tracking-[0.9px] uppercase text-[#94a3b8] block">Sekretariat</span>
        </div>
    </div>

    <nav class="flex-1 p-4 px-3 flex flex-col gap-0.5">
        <a href="{{ route('sekretaris.dashboard') }}"
           class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('sekretaris.dashboard') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-tachometer-alt text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('sekretaris.dashboard') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Dashboard
        </a>

        <a href="{{ route('sekretaris.user-management') }}"
           class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('sekretaris.user-management') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-users text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('sekretaris.user-management') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Manajemen User
        </a>

        <a href="{{ route('sekretaris.manajemen-proposal') }}"
           class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('sekretaris.manajemen-proposal') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-file-alt text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('sekretaris.manajemen-proposal') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Manajemen Proposal
        </a>

        <a href="{{ route('sekretaris.hasil-review') }}"
           class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('sekretaris.hasil-review') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-clipboard-list text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('sekretaris.hasil-review') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Hasil Review
        </a>

        <a href="{{ route('sekretaris.keputusan') }}"
           class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('sekretaris.keputusan') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-gavel text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('sekretaris.keputusan') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Keputusan
        </a>

        <a href="{{ route('sekretaris.draf-ethical-clearance') }}"
           class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('sekretaris.draf-ethical-clearance') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-file-signature text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('sekretaris.draf-ethical-clearance') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Draft Ethical Clearance
        </a>

        <a href="{{ route('sekretaris.arsip') }}"
           class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('sekretaris.arsip') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-archive text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('sekretaris.arsip') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Arsip Dokumen
        </a>

       

        <div class="flex-1"></div>
    </nav>
</div>
@endsection
