<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ethical Clearance System')</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: 'class',
          theme: {
            extend: {
              colors: {
                'primary-fixed-dim': '#b0c6ff',
                'inverse-on-surface': '#eaf1ff',
                'background': '#f8f9ff',
                'surface-container': '#e5eeff',
                'secondary-container': '#81f3e5',
                'on-error-container': '#93000a',
                'on-surface': '#0b1c30',
                'on-secondary-container': '#006f66',
                'on-error': '#ffffff',
                'secondary-fixed': '#84f5e8',
                'outline': '#737783',
                'secondary-fixed-dim': '#66d9cc',
                'outline-variant': '#c3c6d4',
                'surface-tint': '#2b5bb5',
                'primary-container': '#0d47a1',
                'on-background': '#0b1c30',
                'inverse-surface': '#213145',
                'on-primary-fixed': '#001945',
                'tertiary': '#602100',
                'on-tertiary-fixed': '#360f00',
                'surface-container-high': '#dce9ff',
                'surface-container-highest': '#d3e4fe',
                'error': '#ba1a1a',
                'surface': '#f8f9ff',
                'on-secondary-fixed-variant': '#005049',
                'tertiary-fixed': '#ffdbcd',
                'surface-bright': '#f8f9ff',
                'inverse-primary': '#b0c6ff',
                'surface-dim': '#cbdbf5',
                'on-secondary-fixed': '#00201d',
                'primary-fixed': '#d9e2ff',
                'error-container': '#ffdad6',
                'on-secondary': '#ffffff',
                'surface-variant': '#d3e4fe',
                'secondary': '#006a62',
                'surface-container-low': '#eff4ff',
                'on-surface-variant': '#434652',
                'on-primary': '#ffffff',
                'on-tertiary-fixed-variant': '#7d2d00'
              },
              borderRadius: {
                DEFAULT: '0.125rem',
                lg: '0.25rem',
                xl: '0.5rem',
                full: '0.75rem'
              },
              spacing: {
                base: '4px',
                lg: '24px',
                md: '16px',
                xs: '8px',
                xl: '32px',
                gutter: '24px'
              },
              maxWidth: {
                'container-max': '1440px'
              },
              fontFamily: {
                'body-lg': ['Inter', 'sans-serif'],
                h1: ['Inter', 'sans-serif'],
                'label-caps': ['Inter', 'sans-serif'],
                button: ['Inter', 'sans-serif'],
                'body-sm': ['Inter', 'sans-serif'],
                h3: ['Inter', 'sans-serif'],
                h2: ['Inter', 'sans-serif'],
                'body-md': ['Inter', 'sans-serif']
              },
              fontSize: {
                'body-lg': ['16px', {'lineHeight': '24px', 'fontWeight': '400'}],
                h1: ['30px', {'lineHeight': '38px', 'letterSpacing': '-0.02em', 'fontWeight': '700'}],
                'label-caps': ['12px', {'lineHeight': '16px', 'letterSpacing': '0.05em', 'fontWeight': '600'}],
                button: ['14px', {'lineHeight': '20px', 'fontWeight': '500'}],
                'body-sm': ['12px', {'lineHeight': '16px', 'fontWeight': '400'}],
                h3: ['20px', {'lineHeight': '28px', 'fontWeight': '600'}],
                h2: ['24px', {'lineHeight': '32px', 'letterSpacing': '-0.01em', 'fontWeight': '600'}],
                'body-md': ['14px', {'lineHeight': '20px', 'fontWeight': '400'}]
              }
            }
          }
        }
    </script>
    
    <!-- Custom Tailwind overrides -->
    <style>
        .font-h2 { font-family: 'Inter', sans-serif; font-weight: 700; }
        .font-h3 { font-family: 'Inter', sans-serif; font-weight: 600; }
        .font-body-md { font-family: 'Inter', sans-serif; }
        .font-button { font-family: 'Inter', sans-serif; }
        .font-body-sm { font-family: 'Inter', sans-serif; }
        .text-label-caps { text-transform: uppercase; letter-spacing: .05em; }
        @keyframes toast-in {
            from { opacity: 0; transform: translateY(12px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes toast-out {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(6px) scale(0.97); }
        }
        .toast-in {
            animation: toast-in 0.26s cubic-bezier(.34, 1.56, .64, 1) forwards;
        }
        .toast-out {
            animation: toast-out 0.2s ease forwards;
        }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>

    @stack('styles')
</head>
<body class="font-['Inter'] m-0 bg-[#f4f6f9]">

<!-- ═══════════════════════════════════
     SIDEBAR
═══════════════════════════════════ -->
@section('sidebar')
<div class="hidden lg:flex w-64 min-h-screen bg-white border-r border-[#e8ecf0] fixed left-0 top-0 flex-col z-[100]">

    <!-- Brand -->
    <div class="flex items-center gap-2.5 px-5 py-5 pb-[18px] border-b border-[#f0f2f5]">
        <div class="w-[38px] h-[38px] bg-[#1a3a5c] rounded-xl flex items-center justify-center shrink-0">
            <i class="fas fa-shield-halved text-white text-[15px]"></i>
        </div>
        <div>
            <span class="text-[15px] font-bold text-[#0f1e2e] leading-tight tracking-[-0.2px]">Ethics Portal</span>
            <span class="text-[9.5px] font-semibold tracking-[0.9px] uppercase text-[#94a3b8] block">Compliance Management</span>
        </div>
    </div>

    <!-- Nav Links -->
    <nav class="flex-1 p-4 px-3 flex flex-col gap-0.5">
        <a href="{{ route('admin.dashboard') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.dashboard') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-table-columns text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.dashboard') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Dashboard
        </a>

        <a href="{{ route('admin.usermanagement.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.usermanagement.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-user-group text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.usermanagement.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            User Management
        </a>

        <a href="{{ route('admin.role&permission.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.role&permission.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-user-shield text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.role&permission.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Role & Permission
        </a>

        <a href="{{ route('admin.templateproposal.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.templateproposal.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-file-lines text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.templateproposal.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Template Proposal
        </a>

        <a href="{{ route('admin.proposal-assignment.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.proposal-assignment.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-clipboard-list text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.proposal-assignment.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Proposal Assignment
        </a>

        <a href="{{ route('admin.ethicalclearance.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.ethicalclearance.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-file-signature text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.ethicalclearance.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Ethical Clearance
        </a>

        <a href="{{ route('admin.publishing.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.publishing.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-globe text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.publishing.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Publishing
        </a>

        <a href="{{ route('admin.systemmonitoring.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.systemmonitoring.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-chart-line text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.systemmonitoring.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            System Monitoring
        </a>

        <div class="flex-1"></div>
    </nav>

</div>

<!-- Sidebar Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden lg:hidden" onclick="closeSidebar()"></div>

<aside id="sidebarPanel" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-[#e8ecf0] transform -translate-x-full transition-transform duration-300 overflow-y-auto lg:hidden">
    <div class="flex items-center justify-between gap-2.5 px-5 py-5 pb-[18px] border-b border-[#f0f2f5]">
        <div class="flex items-center gap-2.5">
            <div class="w-[38px] h-[38px] bg-[#1a3a5c] rounded-xl flex items-center justify-center shrink-0">
                <i class="fas fa-shield-halved text-white text-[15px]"></i>
            </div>
            <div>
                <span class="text-[15px] font-bold text-[#0f1e2e] leading-tight tracking-[-0.2px]">Ethics Portal</span>
                <span class="text-[9.5px] font-semibold tracking-[0.9px] uppercase text-[#94a3b8] block">Compliance Management</span>
            </div>
        </div>
        <button type="button" onclick="closeSidebar()" class="p-2 rounded-lg text-[#4b5563] hover:bg-[#f5f7fa]">
            <i class="fas fa-xmark"></i>
        </button>
    </div>

    <nav class="flex-1 p-4 px-3 flex flex-col gap-0.5">
        <a href="{{ route('admin.dashboard') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.dashboard') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-table-columns text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.dashboard') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Dashboard
        </a>

        <a href="{{ route('admin.usermanagement.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.usermanagement.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-user-group text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.usermanagement.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            User Management
        </a>

        <a href="{{ route('admin.role&permission.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.role&permission.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-user-shield text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.role&permission.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Role & Permission
        </a>

        <a href="{{ route('admin.templateproposal.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.templateproposal.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-file-lines text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.templateproposal.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Template Proposal
        </a>

        <a href="{{ route('admin.proposal-assignment.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.proposal-assignment.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-clipboard-list text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.proposal-assignment.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Proposal Assignment
        </a>

        <a href="{{ route('admin.ethicalclearance.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.ethicalclearance.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-file-signature text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.ethicalclearance.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Ethical Clearance
        </a>

        <a href="{{ route('admin.publishing.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.publishing.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-globe text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.publishing.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            Publishing
        </a>

        <a href="{{ route('admin.systemmonitoring.index') }}"
        class="group flex items-center gap-[11px] px-3 py-[9px] rounded-lg text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] {{ request()->routeIs('admin.systemmonitoring.*') ? 'bg-[#eef3fb] text-[#1e4d8c] font-semibold border-l-[#2563eb]' : 'text-[#4b5563] border-l-transparent' }} border-l-[3px] hover:text-[#1e3a5f]">
            <i class="fas fa-chart-line text-[15px] w-[18px] text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('admin.systemmonitoring.*') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            System Monitoring
        </a>

        <div class="flex-1"></div>
    </nav>
</aside>

@show

<!-- ═══════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════ -->
<div class="lg:ml-64 min-h-screen bg-[#f4f6f9]">

    <!-- Top Navbar -->
    <div class="bg-white border-b border-[#e8ecf0] px-7 h-[58px] flex items-center justify-between sticky top-0 z-[50]">
        <!-- Kiri: Title + Date Range -->
    <div class="flex items-center gap-3.5">
        <button type="button" onclick="openSidebar()" class="lg:hidden p-2 rounded-full hover:bg-[#f5f7fa] text-[#4b5563] transition-colors">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="text-[20px] font-bold text-[#0f1e2e] tracking-[-0.4px] m-0">@yield('page-title', 'Dashboard')</h1>
        <div class="w-px h-[18px] bg-[#d1d5db]"></div>
        
        {{-- Yield untuk area date/action, bisa diisi atau tidak --}}
        @hasSection('header-action')
            @yield('header-action')
        @else
            <div class="inline-flex items-center gap-1.5 text-[12.5px] font-medium text-[#6b7280]">
                <i class="text-[12px] text-[#9ca3af]"></i>
                @yield('breadcrumb', 'Overview & Statistics')
            </div>
        @endif
    </div>

        <!-- Kanan: Bell + Admin -->
        <div class="flex items-center gap-3.5">
            <button class="w-9 h-9 rounded-full bg-transparent border-none flex items-center justify-center cursor-pointer text-[#6b7280] text-base transition-colors duration-150 hover:text-[#374151] relative" onclick="featureInDevelopment('Notifikasi')">
                <i class="fas fa-bell"></i>
            </button>

            <div class="relative" id="profile-dropdown-container">
                <button id="profile-dropdown-toggle" type="button" class="flex items-center gap-2.5 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-left transition-all duration-150 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <div class="text-right">
                        <span class="text-[13.5px] font-semibold text-[#0f1e2e] block leading-tight">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] font-bold tracking-[0.6px] uppercase text-[#94a3b8] block">{{ Auth::user()->role ?? 'Admin' }}</span>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-[#1a3a5c] text-white text-[12px] font-bold flex items-center justify-center border-2 border-[#e8ecf0] shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <i class="fas fa-chevron-down text-slate-400"></i>
                </button>

                <div id="profile-dropdown-menu" class="hidden absolute right-0 mt-2 w-48 rounded-2xl border border-slate-200 bg-white shadow-lg py-2 z-50">
                    @if(auth()->user() && auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.profile') }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-slate-100">Edit Profil</a>
                    @else
                        <a href="{{ route('profile.show') }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-slate-100">Edit Profil</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-slate-100">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="p-6 px-7">
        @yield('content')
    </div>

</div>

<!-- Toast Container -->
<div id="toast-container"
     class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-2.5 pointer-events-none"></div>

<!-- Scripts -->
<script>
function featureInDevelopment(feature) {
    showToast(feature + ' sedang dalam pengembangan', 'info');
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    
    const toast = document.createElement('div');
    toast.className = 'bg-[#0f1e2e] text-white px-4 py-[11px] rounded-xl text-[13px] font-medium shadow-xl flex items-center gap-2.5 pointer-events-auto max-w-[300px] min-w-[200px] toast-in';
    toast.innerHTML = `
        <i class="fas ${type === 'info' ? 'fa-circle-info' : 'fa-circle-check'} text-sm"></i>
        <span>${message}</span>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('toast-in');
        toast.classList.add('toast-out');
        setTimeout(() => toast.remove(), 200);
    }, 3000);
}

(function () {
    const toggle = document.getElementById('profile-dropdown-toggle');
    const menu = document.getElementById('profile-dropdown-menu');

    if (toggle && menu) {
        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            menu.classList.toggle('hidden');
        });

        document.addEventListener('click', function (event) {
            if (!menu.classList.contains('hidden') && !toggle.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    }
})();

function openSidebar() {
    const sidebar = document.getElementById('sidebarPanel');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar || !overlay) return;

    sidebar.classList.remove('-translate-x-full');
    sidebar.classList.add('translate-x-0');
    overlay.classList.remove('hidden');
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebarPanel');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar || !overlay) return;

    sidebar.classList.add('-translate-x-full');
    sidebar.classList.remove('translate-x-0');
    overlay.classList.add('hidden');
}

document.querySelectorAll('#sidebarPanel a').forEach(link => {
    link.addEventListener('click', closeSidebar);
});

window.addEventListener('resize', function () {
    if (window.innerWidth >= 1024) {
        closeSidebar();
    }
});
</script>
@stack('scripts')

</body>
</html>