<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Ethical Clearance System' }} | {{ $roleName ?? 'Home' }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-tertiary": "#ffffff",
                        "surface-tint": "#2b5bb5",
                        "on-primary-fixed": "#001945",
                        "surface-bright": "#f8f9ff",
                        "primary-container": "#0d47a1",
                        "on-secondary-container": "#006f66",
                        "tertiary-fixed-dim": "#ffb596",
                        "secondary-fixed": "#84f5e8",
                        "surface-container-high": "#dce9ff",
                        "tertiary": "#602100",
                        "inverse-primary": "#b0c6ff",
                        "secondary-fixed-dim": "#66d9cc",
                        "surface-variant": "#d3e4fe",
                        "error-container": "#ffdad6",
                        "on-tertiary-container": "#ffa781",
                        "surface-container-highest": "#d3e4fe",
                        "on-error-container": "#93000a",
                        "on-tertiary-fixed-variant": "#7d2d00",
                        "on-error": "#ffffff",
                        "primary": "#003178",
                        "on-background": "#0b1c30",
                        "on-primary-fixed-variant": "#00429c",
                        "secondary-container": "#81f3e5",
                        "background": "url('/images/bg-research.jpg')",
                        "inverse-surface": "#213145",
                        "outline-variant": "#c3c6d4",
                        "on-secondary-fixed": "#00201d",
                        "inverse-on-surface": "#eaf1ff",
                        "tertiary-fixed": "#ffdbcd",
                        "on-tertiary-fixed": "#360f00",
                        "surface-container": "#e5eeff",
                        "surface-dim": "#cbdbf5",
                        "error": "#ba1a1a",
                        "primary-fixed": "#d9e2ff",
                        "tertiary-container": "#853100",
                        "surface-container-lowest": "#ffffff",
                        "on-primary-container": "#a1bbff",
                        "on-surface": "#0b1c30",
                        "surface-container-low": "#eff4ff",
                        "surface": "#f8f9ff",
                        "outline": "#737783",
                        "on-surface-variant": "#434652",
                        "on-primary": "#ffffff",
                        "on-secondary-fixed-variant": "#005049",
                        "secondary": "#006a62",
                        "on-secondary": "#ffffff",
                        "primary-fixed-dim": "#b0c6ff"
                    },
                    borderRadius: {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem",
                        "2xl": "1rem",
                    },
                    spacing: {
                        "md": "16px",
                        "container-max": "1440px",
                        "base": "4px",
                        "xs": "8px",
                        "xl": "32px",
                        "lg": "24px",
                        "sm": "12px",
                        "gutter": "24px",
                    },
                    fontFamily: {
                        "button": ["Inter"],
                        "label-caps": ["Inter"],
                        "body-sm": ["Inter"],
                        "body-lg": ["Inter"],
                        "h2": ["Inter"],
                        "body-md": ["Inter"],
                        "h1": ["Inter"],
                        "h3": ["Inter"],
                    },
                    fontSize: {
                        "button": ["14px", {"lineHeight": "20px", "fontWeight": "500"}],
                        "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "body-sm": ["12px", {"lineHeight": "16px", "fontWeight": "400"}],
                        "body-lg": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "h2": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "body-md": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "h1": ["30px", {"lineHeight": "38px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "h3": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                    },
                    maxWidth: {
                        "container-max": "1440px",
                    },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .active-dot {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background-color: #ba1a1a;
            border-radius: 50%;
            border: 2px solid white;
        }
        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
        }
        .group:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #eff4ff;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: #c3c6d4;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #737783;
        }
        /* Sidebar transition */
        .sidebar-overlay {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .sidebar-panel {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .sidebar-panel.active {
            transform: translateX(0);
        }
            /* ========== NOTIFICATION STYLES ========== */
        .notification-btn {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .notification-btn:hover {
            background-color: #eff4ff;
            transform: scale(1.05);
        }
        
        .notification-btn:hover .notification-icon {
            animation: bellShake 0.5s ease-in-out;
        }
        
        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            min-width: 18px;
            height: 18px;
            background: linear-gradient(135deg, #ba1a1a, #e53935);
            color: white;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            animation: badgePop 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        @keyframes bellShake {
            0% { transform: rotate(0deg); }
            20% { transform: rotate(15deg); }
            40% { transform: rotate(-12deg); }
            60% { transform: rotate(8deg); }
            80% { transform: rotate(-4deg); }
            100% { transform: rotate(0deg); }
        }
        
        @keyframes badgePop {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        @keyframes bellRing {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(20deg); }
            20% { transform: rotate(-15deg); }
            30% { transform: rotate(12deg); }
            40% { transform: rotate(-8deg); }
            50% { transform: rotate(6deg); }
            60% { transform: rotate(-3deg); }
            70% { transform: rotate(2deg); }
            100% { transform: rotate(0deg); }
        }
        
        .notification-badge.has-new {
            animation: badgePop 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), pulseGlow 1.5s infinite;
        }
        
        @keyframes pulseGlow {
            0% { box-shadow: 0 0 0 0 rgba(186, 26, 26, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(186, 26, 26, 0); }
            100% { box-shadow: 0 0 0 0 rgba(186, 26, 26, 0); }
        }
        
        /* Animasi untuk dropdown muncul */
        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        #notificationDropdown[style*="display: block"] {
            animation: dropdownFadeIn 0.2s ease-out;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-background text-on-surface font-body-md antialiased relative min-h-screen flex flex-col" style="background-image: url('{{ asset('https://sso.uns.ac.id/module.php/uns/img/symphony.png') }}')">

<!-- Sidebar Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 sidebar-overlay lg:hidden" onclick="closeSidebar()"></div>

<!-- Sidebar Panel (Mobile Drawer) -->
<aside id="sidebarPanel" class="fixed top-0 left-0 h-full w-72 bg-white shadow-2xl z-50 sidebar-panel lg:hidden flex flex-col overflow-y-auto border-r border-outline-variant">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between p-4 border-b border-outline-variant h-16">
        <span class="text-xl font-bold tracking-tighter text-primary">EthicsClear</span>
        <button onclick="closeSidebar()" class="p-2 rounded-full hover:bg-surface-container-low transition-colors">
            <span class="material-symbols-outlined text-on-surface-variant">close</span>
        </button>
    </div>

    <!-- Sidebar Navigation (Same content as desktop but vertical) -->
    <div class="p-4 flex flex-col gap-2">
        @auth
            @if(auth()->user()->hasRole('peneliti') && Route::has('peneliti.dashboard'))
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors {{ request()->routeIs('home') ? 'bg-primary-container/20 text-primary font-semibold' : '' }}">
                    <span class="material-symbols-outlined">home</span>
                    <span>Home</span>
                </a>
                <div class="mt-1">
                    <p class="px-4 py-2 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Panduan</p>
                    <a href="{{ route('panduan.syarat-pendaftaran') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-primary text-lg">description</span>
                        <span>Syarat Pendaftaran</span>
                    </a>
                    <a href="{{ route('panduan.alur-pengajuan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-primary text-lg">timeline</span>
                        <span>Alur Pengajuan</span>
                    </a>
                    <a href="{{ route('panduan.panduan-reviewer') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-primary text-lg">rate_review</span>
                        <span>Panduan Reviewer</span>
                    </a>
                </div>
                <div class="mt-1">
                    <p class="px-4 py-2 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Pengajuan</p>
                    <a href="{{ route('pengajuan.upload-proposal') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-primary text-lg">upload_file</span>
                        <span>Upload Proposal</span>
                    </a>
                    <a href="{{ route('pengajuan.download-template') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-primary text-lg">download</span>
                        <span>Download Template</span>
                    </a>
                    <a href="{{ route('pengajuan.riwayat-pengajuan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-primary text-lg">history</span>
                        <span>Riwayat Pengajuan</span>
                    </a>
                </div>
            @elseif(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Dashboard</a>
                <a href="{{ route('admin.usermanagement.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">User Management</a>
                <a href="{{ route('admin.templateproposal.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Template</a>
                <a href="{{ route('admin.systemmonitoring.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Monitoring</a>
            @elseif(auth()->user()->hasRole('sekretaris') || auth()->user()->hasRole('ketua'))
                <a href="{{ route('sekretaris.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Dashboard</a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Manajemen Proposal</a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Assign Reviewer</a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Draft Ethical Clearance</a>
            @elseif(auth()->user()->hasRole('reviewer'))
                <a href="{{ route('reviewer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Dashboard</a>
                <a href="{{ route('reviewer.proposal-masuk') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Proposal Masuk</a>
                <a href="{{ route('reviewer.review-proposal') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Review Proposal</a>
                <a href="{{ route('reviewer.riwayat-review') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Riwayat Review</a>
            @endif
        @else
            <!-- Guest Navigation in Sidebar -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors {{ request()->routeIs('home') ? 'bg-primary-container/20 text-primary font-semibold' : '' }}">
                <span class="material-symbols-outlined">home</span>
                <span>Home</span>
            </a>
            <div class="mt-1">
                <p class="px-4 py-2 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Panduan</p>
                <a href="{{ route('panduan.syarat-pendaftaran') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-primary text-lg">description</span>
                    <span>Syarat Pendaftaran</span>
                </a>
                <a href="{{ route('panduan.alur-pengajuan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-primary text-lg">timeline</span>
                    <span>Alur Pengajuan</span>
                </a>
                <a href="{{ route('panduan.panduan-reviewer') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-primary text-lg">rate_review</span>
                    <span>Panduan Reviewer</span>
                </a>
            </div>
            <div class="mt-1">
                <p class="px-4 py-2 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Pengajuan</p>
                <a href="{{ route('pengajuan.upload-proposal') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-primary text-lg">upload_file</span>
                    <span>Upload Proposal</span>
                </a>
                <a href="{{ route('pengajuan.download-template') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-primary text-lg">download</span>
                    <span>Download Template</span>
                </a>
                <a href="{{ route('pengajuan.riwayat-pengajuan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-primary text-lg">history</span>
                    <span>Riwayat Pengajuan</span>
                </a>
            </div>
        @endauth
    </div>
</aside>

<!-- Top Navigation Bar -->
<nav class="bg-white border-b border-outline-variant shadow-sm sticky top-0 z-30">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Left side: Hamburger + Logo -->
        <div class="flex items-center gap-4 lg:gap-8">
            <!-- Hamburger Button (Visible on small screens) -->
            <button onclick="openSidebar()" class="p-2 rounded-full hover:bg-surface-container-low transition-colors lg:hidden" aria-label="Buka menu">
                <span class="material-symbols-outlined text-on-surface-variant">menu</span>
            </button>

            <a href="{{ route('home') }}" class="text-xl font-bold tracking-tighter text-primary hover:opacity-80 transition-opacity">
                EthicsClear
            </a>

            <!-- Desktop Navigation - Conditional berdasarkan role dan auth -->
            <div class="hidden lg:flex items-center gap-6">
                @auth
                    @if(auth()->user()->hasRole('peneliti') && Route::has('peneliti.dashboard'))
                        <a href="{{ route('home') }}" class="text-on-surface-variant hover:text-primary transition-colors duration-200 {{ request()->routeIs('home') ? 'text-primary border-b-2 border-primary pb-1 font-semibold' : '' }}">
                            Home
                        </a>
                        <div class="relative group">
                            <div class="flex items-center gap-1 text-on-surface-variant hover:text-primary transition-colors cursor-pointer">
                                <span>Panduan</span>
                                <span class="material-symbols-outlined text-sm">expand_more</span>
                            </div>
                            <div class="dropdown-menu absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-outline-variant z-50">
                                <a href="{{ route('panduan.syarat-pendaftaran') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low rounded-t-xl transition-colors">
                                    <span class="material-symbols-outlined text-primary text-lg">description</span>
                                    <span>Syarat Pendaftaran Ethical Clearance</span>
                                </a>
                                <a href="{{ route('panduan.alur-pengajuan') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                                    <span class="material-symbols-outlined text-primary text-lg">timeline</span>
                                    <span>Alur Pengajuan Ethical Clearance</span>
                                </a>
                                <a href="{{ route('panduan.panduan-reviewer') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low rounded-b-xl transition-colors">
                                    <span class="material-symbols-outlined text-primary text-lg">rate_review</span>
                                    <span>Panduan Reviewer</span>
                                </a>
                            </div>
                        </div>
                        <div class="relative group">
                            <div class="flex items-center gap-1 text-on-surface-variant hover:text-primary transition-colors cursor-pointer">
                                <span>Pengajuan</span>
                                <span class="material-symbols-outlined text-sm">expand_more</span>
                            </div>
                            <div class="dropdown-menu absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-outline-variant z-50">
                                <a href="{{ route('pengajuan.upload-proposal') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low rounded-t-xl transition-colors">
                                    <span class="material-symbols-outlined text-primary text-lg">upload_file</span>
                                    <span>Upload Proposal</span>
                                </a>
                                <a href="{{ route('pengajuan.download-template') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                                    <span class="material-symbols-outlined text-primary text-lg">download</span>
                                    <span>Download Template</span>
                                </a>
                                <a href="{{ route('pengajuan.riwayat-pengajuan') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low rounded-b-xl transition-colors">
                                    <span class="material-symbols-outlined text-primary text-lg">history</span>
                                    <span>Riwayat Pengajuan</span>
                                </a>
                            </div>
                        </div>
                    @elseif(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.dashboard') }}" class="text-on-surface-variant hover:text-primary transition-colors">Dashboard</a>
                        <a href="{{ route('admin.usermanagement.index') }}" class="text-on-surface-variant hover:text-primary transition-colors">User Management</a>
                        <a href="{{ route('admin.templateproposal.index') }}" class="text-on-surface-variant hover:text-primary transition-colors">Template</a>
                        <a href="{{ route('admin.systemmonitoring.index') }}" class="text-on-surface-variant hover:text-primary transition-colors">Monitoring</a>
                    @elseif(auth()->user()->hasRole('sekretaris') || auth()->user()->hasRole('ketua'))
                        <a href="{{ route('sekretaris.dashboard') }}" class="text-on-surface-variant hover:text-primary transition-colors">Dashboard</a>
                        <a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Manajemen Proposal</a>
                        <a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Assign Reviewer</a>
                        <a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Draft Ethical Clearance</a>
                    @elseif(auth()->user()->hasRole('reviewer'))
                        <a href="{{ route('reviewer.dashboard') }}" class="text-on-surface-variant hover:text-primary transition-colors">Dashboard</a>
                        <a href="{{ route('reviewer.proposal-masuk') }}" class="text-on-surface-variant hover:text-primary transition-colors">Proposal Masuk</a>
                        <a href="{{ route('reviewer.review-proposal') }}" class="text-on-surface-variant hover:text-primary transition-colors">Review Proposal</a>
                        <a href="{{ route('reviewer.riwayat-review') }}" class="text-on-surface-variant hover:text-primary transition-colors">Riwayat Review</a>
                    @endif
                @else
                    <!-- Navigation for Guest -->
                    <a href="{{ route('home') }}" class="text-on-surface-variant hover:text-primary transition-colors duration-200 {{ request()->routeIs('home') ? 'text-primary border-b-2 border-primary pb-1 font-semibold' : '' }}">
                        Home
                    </a>
                    <div class="relative group">
                        <div class="flex items-center gap-1 text-on-surface-variant hover:text-primary transition-colors cursor-pointer">
                            <span>Panduan</span>
                            <span class="material-symbols-outlined text-sm">expand_more</span>
                        </div>
                        <div class="dropdown-menu absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-outline-variant z-50">
                            <a href="{{ route('panduan.syarat-pendaftaran') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low rounded-t-xl transition-colors">
                                <span class="material-symbols-outlined text-primary text-lg">description</span>
                                <span>Syarat Pendaftaran Ethical Clearance</span>
                            </a>
                            <a href="{{ route('panduan.alur-pengajuan') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                                <span class="material-symbols-outlined text-primary text-lg">timeline</span>
                                <span>Alur Pengajuan Ethical Clearance</span>
                            </a>
                            <a href="{{ route('panduan.panduan-reviewer') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low rounded-b-xl transition-colors">
                                <span class="material-symbols-outlined text-primary text-lg">rate_review</span>
                                <span>Panduan Reviewer</span>
                            </a>
                        </div>
                    </div>
                    <div class="relative group">
                        <div class="flex items-center gap-1 text-on-surface-variant hover:text-primary transition-colors cursor-pointer">
                            <span>Pengajuan</span>
                            <span class="material-symbols-outlined text-sm">expand_more</span>
                        </div>
                        <div class="dropdown-menu absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-outline-variant z-50">
                            <a href="{{ route('pengajuan.upload-proposal') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low rounded-t-xl transition-colors">
                                <span class="material-symbols-outlined text-primary text-lg">upload_file</span>
                                <span>Upload Proposal</span>
                            </a>
                            <a href="{{ route('pengajuan.download-template') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                                <span class="material-symbols-outlined text-primary text-lg">download</span>
                                <span>Download Template</span>
                            </a>
                            <a href="{{ route('pengajuan.riwayat-pengajuan') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low rounded-b-xl transition-colors">
                                <span class="material-symbols-outlined text-primary text-lg">history</span>
                                <span>Riwayat Pengajuan</span>
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

<!-- Right Side Navigation -->
<div class="flex items-center gap-2 sm:gap-4">
    @auth
        <!-- Notifications (only when logged in) -->
        <div class="relative">
            <button onclick="toggleNotificationDropdown()" 
                    class="p-2 rounded-full hover:bg-surface-container-low transition-all duration-300 relative notification-btn"
                    id="notificationBtn">
                <span class="material-symbols-outlined text-on-surface-variant text-2xl notification-icon">notifications</span>
                <span id="notificationBadge" class="notification-badge hidden"></span>
            </button>
            
            <!-- Notification Dropdown -->
            <div id="notificationDropdown" style="display: none;" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-outline-variant z-50 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-outline-variant bg-surface-container-low">
                    <h3 class="font-semibold text-primary">Notifikasi</h3>
                    <a href="{{ route('peneliti.notifikasi.index') }}" class="text-xs text-primary hover:underline">Lihat Semua</a>
                </div>
                <div id="notificationList" class="max-h-96 overflow-y-auto">
                    <div class="p-4 text-center text-on-surface-variant">Memuat...</div>
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <div class="relative group">
            <div class="flex items-center gap-2 sm:gap-3 pl-2 sm:pl-4 border-l border-outline-variant cursor-pointer group">
                <div class="text-right hidden sm:block">
                    <p class="font-semibold text-primary text-sm">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-on-surface-variant uppercase tracking-wider">{{ $roleName ?? '' }}</p>
                </div>
                <div class="w-9 h-9 rounded-full overflow-hidden bg-surface-container border border-outline-variant flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-surface-variant">account_circle</span>
                </div>
                <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors hidden sm:block">expand_more</span>
            </div>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-outline-variant z-50">
                <div class="p-3 border-b border-outline-variant sm:hidden">
                    <p class="font-semibold text-primary">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-on-surface-variant">{{ Auth::user()->email }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low transition-colors rounded-t-xl">
                    <span class="material-symbols-outlined text-primary text-lg">person</span>
                    <span>Data Diri</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-primary text-lg">edit</span>
                    <span>Edit Profil</span>
                </a>
                <div class="border-t border-outline-variant my-1"></div>
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-error hover:bg-error-container transition-colors rounded-b-xl">
                        <span class="material-symbols-outlined text-lg">logout</span>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    @else
        <!-- Login/Register for Guest -->
        <a href="{{ route('login') }}" class="px-4 py-2 text-primary hover:bg-primary-container/10 rounded-lg transition-colors font-button">
            Login
        </a>
        <a href="{{ route('register') }}" class="px-4 py-2 bg-primary text-on-primary rounded-lg hover:bg-primary-container transition-colors font-button shadow-sm">
            Register
        </a>
    @endauth
</div>
    </div>
</nav>

<!-- Main Content -->
<main class="py-6 lg:py-8 flex-1">
    @if(session('success'))
        <div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-secondary-container text-on-secondary-container px-4 py-3 rounded-lg flex items-center gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-error-container text-on-error-container px-4 py-3 rounded-lg flex items-center gap-3">
                <span class="material-symbols-outlined">error</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @yield('content')
</main>

<!-- Footer -->
<footer class="mt-auto py-6 bg-white border-t border-outline-variant">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-4 flex-wrap justify-center">
            <span class="text-primary font-bold text-sm">EthicsClear</span>
            <p class="text-xs text-on-surface-variant">© 2026 institutional Review Board. All rights reserved.</p>
        </div>
        <div class="flex gap-4 flex-wrap justify-center">
            <a href="#" class="text-xs text-on-surface-variant hover:text-primary transition-colors">Privacy Policy</a>
            <a href="#" class="text-xs text-on-surface-variant hover:text-primary transition-colors">Terms of Service</a>
            <a href="#" class="text-xs text-on-surface-variant hover:text-primary transition-colors">Compliance Standards</a>
        </div>
    </div>
</footer>

<script>
    // Sidebar functions
    function openSidebar() {
        document.getElementById('sidebarPanel').classList.add('active');
        document.getElementById('sidebarOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeSidebar() {
        document.getElementById('sidebarPanel').classList.remove('active');
        document.getElementById('sidebarOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close sidebar when clicking overlay
    document.getElementById('sidebarOverlay')?.addEventListener('click', closeSidebar);

    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSidebar();
        }
    });

    // ========== NOTIFICATION FUNCTIONS ==========
    let notificationDropdownOpen = false;
    
    function toggleNotificationDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        notificationDropdownOpen = !notificationDropdownOpen;
        dropdown.style.display = notificationDropdownOpen ? 'block' : 'none';
        if (notificationDropdownOpen) {
            loadLatestNotifications();
        }
    }
    
    function loadLatestNotifications() {
        fetch('{{ route("peneliti.notifikasi.latest") }}', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('notificationList');
            const badge = document.getElementById('notificationBadge');
            
            if (data.unread_count > 0) {
                badge.style.display = 'block';
                badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
            } else {
                badge.style.display = 'none';
            }
            
            if (data.notifications.length === 0) {
                container.innerHTML = '<div class="p-4 text-center text-on-surface-variant">Tidak ada notifikasi</div>';
                return;
            }
            
            container.innerHTML = data.notifications.map(notif => `
                <a href="/peneliti/notifikasi/redirect/${notif.id}" class="flex items-start gap-3 p-3 hover:bg-surface-container-low transition-colors border-b border-outline-variant last:border-0 ${notif.status === 'unread' ? 'bg-surface-container-low' : ''}">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-primary text-sm">${getIconByType(notif.type)}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium ${notif.status === 'unread' ? 'text-primary' : 'text-on-surface'}">${notif.title}</p>
                        <p class="text-xs text-on-surface-variant truncate">${notif.message}</p>
                        <p class="text-xs text-on-surface-variant mt-1">${new Date(notif.created_at).toLocaleDateString('id-ID')}</p>
                    </div>
                </a>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            const container = document.getElementById('notificationList');
            if (container) {
                container.innerHTML = '<div class="p-4 text-center text-error">Gagal memuat notifikasi</div>';
            }
        });
    }
    
    function getIconByType(type) {
        const icons = {
            'account_activation': 'check_circle',
            'review_assignment': 'assignment',
            'proposal_status': 'article',
            'document_ready': 'description',
            'revision_request': 'edit_note'
        };
        return icons[type] || 'notifications';
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const notifContainer = document.getElementById('notificationBtn');
        const dropdown = document.getElementById('notificationDropdown');
        if (notifContainer && dropdown && notifContainer.parentElement && !notifContainer.parentElement.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
            notificationDropdownOpen = false;
        }
    });
    
    // Load notifications on page load if user is logged in and has role peneliti
    @auth
        @if(auth()->user()->hasRole('peneliti'))
            document.addEventListener('DOMContentLoaded', function() {
                loadLatestNotifications();
            });
        @endif
    @endauth
</script>
@stack('scripts')
</body>
</html>