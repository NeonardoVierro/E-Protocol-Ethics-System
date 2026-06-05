<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Reviewer Panel') | Ethical Clearance</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Tailwind overrides -->
    <style>
        @keyframes toast-in {
            from { opacity: 0; transform: translateY(12px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes toast-out {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(6px) scale(0.97); }
        }
        .toast-in { animation: toast-in 0.26s cubic-bezier(.34, 1.56, .64, 1) forwards; }
        .toast-out { animation: toast-out 0.2s ease forwards; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        .sidebar-link.active {
            background: #eef3fb;
            color: #1e4d8c;
            font-weight: 600;
            border-left-color: #2563eb;
        }

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
        
        .notification-dropdown-link {
            transition: background-color 0.2s ease;
        }
        
        .notification-dropdown-link:hover {
            background-color: #eff4ff;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .notification-badge {
                font-size: 9px;
                min-width: 16px;
                height: 16px;
                padding: 0 3px;
                top: -4px;
                right: -4px;
            }
        }

        /* Smooth sidebar transition */
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }

        /* Ensure tables are responsive */
        table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            white-space: nowrap;
        }

        @media (min-width: 1024px) {
            table {
                display: table;
                width: 100%;
                white-space: normal;
            }
        }
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
/*         
        .notification-badge.has-new {
            animation: badgePop 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), pulseGlow 1.5s infinite;
        } */
        
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
<body class="font-['DM_Sans'] m-0 bg-[#f4f6f9]">

<!-- ═══════════════════════════════════
     SIDEBAR (Reviewer)
═══════════════════════════════════ -->
<!-- Mobile Backdrop -->
<div id="sidebarBackdrop" class="hidden fixed inset-0 bg-black/50 z-40 lg:hidden" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<div id="sidebar" class="fixed left-0 top-0 bottom-0 w-64 bg-white border-r border-[#e8ecf0] flex flex-col z-50 lg:z-[100] transform -translate-x-full lg:translate-x-0 transition-transform duration-300 overflow-y-auto">

    <!-- Brand -->
    <div class="flex items-center gap-2.5 px-4 sm:px-5 py-4 sm:py-5 pb-3 sm:pb-[18px] border-b border-[#f0f2f5] bg-white">
        <div class="w-[38px] h-[38px] bg-[#1a3a5c] rounded-xl flex items-center justify-center shrink-0">
            <i class="fas fa-shield-halved text-white text-[15px]"></i>
        </div>
        <div class="flex-1 min-w-0">
            <span class="text-sm sm:text-[15px] font-bold text-[#0f1e2e] leading-tight tracking-[-0.2px] block truncate">Ethics Portal</span>
            <span class="text-[8px] sm:text-[9.5px] font-semibold tracking-[0.9px] uppercase text-[#4b5563] block">Reviewer Access</span>
        </div>
        <!-- Close button for mobile -->
        <button onclick="closeSidebar()" class="sm:hidden ml-auto p-2 rounded-lg hover:bg-[#f5f7fa] text-[#4b5563] flex-shrink-0">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <!-- Nav Links (sesuai DASHBOARD 3) -->
    <nav class="flex-1 p-3 sm:p-4 px-2 sm:px-3 flex flex-col gap-0.5">
        {{-- Dashboard --}}
        <a href="{{ route('reviewer.dashboard') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-[9px] rounded-lg text-[12px] sm:text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] border-l-[3px] border-l-transparent hover:border-l-[#2563eb] text-[#4b5563] {{ request()->routeIs('reviewer.dashboard') ? 'active bg-[#eef3fb] text-[#1e4d8c] border-l-[#2563eb]' : '' }}">
            <i class="fas fa-table-columns text-[14px] sm:text-[15px] w-5 text-center shrink-0 {{ request()->routeIs('reviewer.dashboard') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            <span class="truncate">Dashboard</span>
        </a>

        {{-- Proposal Masuk --}}
        <a href="{{ route('reviewer.proposal-masuk') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-[9px] rounded-lg text-[12px] sm:text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] border-l-[3px] border-l-transparent hover:border-l-[#2563eb] text-[#4b5563] {{ request()->routeIs('reviewer.proposal-masuk') ? 'active bg-[#eef3fb] text-[#1e4d8c] border-l-[#2563eb]' : '' }}">
            <i class="fas fa-inbox text-[14px] sm:text-[15px] w-5 text-center shrink-0 {{ request()->routeIs('reviewer.proposal-masuk') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            <span class="truncate">Proposal Masuk</span>
        </a>

        {{-- Review Proposal --}}
        <a href="{{ route('reviewer.review-proposal') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-[9px] rounded-lg text-[12px] sm:text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] border-l-[3px] border-l-transparent hover:border-l-[#2563eb] text-[#4b5563] {{ request()->routeIs('reviewer.review-proposal') ? 'active bg-[#eef3fb] text-[#1e4d8c] border-l-[#2563eb]' : '' }}">
            <i class="fas fa-edit text-[14px] sm:text-[15px] w-5 text-center shrink-0 {{ request()->routeIs('reviewer.review-proposal') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            <span class="truncate">Review Proposal</span>
        </a>

        {{-- Riwayat Review --}}
        <a href="{{ route('reviewer.riwayat-review') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-[9px] rounded-lg text-[12px] sm:text-[13.5px] font-medium no-underline transition-all duration-150 hover:bg-[#f5f7fa] border-l-[3px] border-l-transparent hover:border-l-[#2563eb] text-[#4b5563] {{ request()->routeIs('reviewer.riwayat-review') ? 'active bg-[#eef3fb] text-[#1e4d8c] border-l-[#2563eb]' : '' }}">
            <i class="fas fa-history text-[14px] sm:text-[15px] w-5 text-center shrink-0 {{ request()->routeIs('reviewer.riwayat-review') ? 'text-[#2563eb]' : 'text-[#9ca3af] group-hover:text-[#4b6fa8]' }}"></i>
            <span class="truncate">Riwayat Review</span>
        </a>

    </nav>

    <!-- Bottom: Settings & Support -->
    <div class="p-2 px-2 sm:px-3 pb-4 border-t border-[#f0f2f5] flex flex-col gap-0.5">
        <a href="#" onclick="featureInDevelopment('Settings')" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[12px] sm:text-[13.5px] font-medium text-[#4b5563] hover:text-[#2563eb] no-underline transition-all duration-150 hover:bg-[#f5f7fa]">
            <i class="fas fa-gear text-[13px] sm:text-[14px] w-5 text-center text-[#9ca3af] shrink-0"></i>
            <span class="truncate">Settings</span>
        </a>
        <a href="#" onclick="featureInDevelopment('Support')" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[12px] sm:text-[13.5px] font-medium text-[#4b5563] hover:text-[#2563eb] no-underline transition-all duration-150 hover:bg-[#f5f7fa]">
            <i class="fas fa-circle-question text-[13px] sm:text-[14px] w-5 text-center text-[#9ca3af] shrink-0"></i>
            <span class="truncate">Support</span>
        </a>
    </div>
</div>

<!-- ═══════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════ -->
<div class="lg:ml-64 min-h-screen bg-[#f4f6f9]">

    <!-- Top Navbar -->
    <div class="bg-white border-b border-[#e8ecf0] px-4 sm:px-6 lg:px-7 h-14 sm:h-[58px] flex items-center justify-between sticky top-0 z-40">
        
        <!-- Left Side: Hamburger + Title -->
        <div class="flex items-center gap-3 sm:gap-3.5 min-w-0">
            <!-- Hamburger Menu (Mobile & Tablet) -->
            <button id="sidebarToggle" onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-[#f5f7fa] transition-colors flex-shrink-0">
                <i class="fas fa-bars text-[#4b5563] text-lg"></i>
            </button>

            <!-- Title -->
            <div class="flex items-center gap-2 min-w-0">
                <h1 class="text-base sm:text-lg lg:text-[20px] font-bold text-[#0f1e2e] tracking-[-0.4px] m-0 truncate">@yield('page-title', 'Dashboard')</h1>
                <div class="w-px h-4 sm:h-[18px] bg-[#d1d5db] flex-shrink-0 hidden sm:block"></div>
            </div>

            <!-- Header Action / Breadcrumb -->
            @hasSection('header-action')
                @yield('header-action')
            @else
                <div class="hidden sm:inline-flex items-center gap-1.5 text-[11px] sm:text-[12.5px] font-medium text-[#6b7280] flex-shrink-0">
                    <i class="fas fa-chalkboard-user text-[11px] sm:text-[12px] text-[#9ca3af]"></i>
                    <span class="truncate">@yield('breadcrumb', 'Dashboard')</span>
                </div>
            @endif
        </div>

        @php
            $reviewerUnreadCount = auth()->check() ? auth()->user()->notifications()->where('status', 'unread')->count() : 0;
        @endphp

        <!-- Right Side: Notifications + Profile -->
        <div class="flex items-center gap-2 sm:gap-3.5 flex-shrink-0">
            <!-- Notification Dropdown (Desktop & Tablet) -->
            <div class="relative hidden sm:block">
                <button type="button" onclick="toggleNotificationDropdown()" id="notificationBtn" class="p-2 rounded-full hover:bg-surface-container-low transition-all duration-300 relative notification-btn">
                    <span class="material-symbols-outlined text-on-surface-variant text-2xl notification-icon">notifications</span>
                    <span id="notificationBadge" class="notification-badge has-new" style="display: {{ $reviewerUnreadCount > 0 ? 'block' : 'none' }};">{{ $reviewerUnreadCount > 9 ? '9+' : $reviewerUnreadCount }}</span>
                </button>

                <div id="notificationDropdown" style="display: none;" class="absolute right-0 mt-2 w-80 md:w-72 bg-white rounded-xl shadow-lg border border-outline-variant z-50 overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-outline-variant bg-surface-container-low">
                        <h3 class="font-semibold text-primary text-sm">Notifikasi</h3>
                        <a href="{{ route('reviewer.notifikasi.index') }}" class="text-xs text-primary hover:underline">Lihat Semua</a>
                    </div>
                    <div id="notificationList" class="max-h-96 overflow-y-auto">
                        <div class="p-4 text-center text-on-surface-variant text-sm">Memuat...</div>
                    </div>
                </div>
            </div>

            <!-- Mobile Notification Badge (Mobile Only) -->
            <button type="button" class="sm:hidden p-2 rounded-full hover:bg-surface-container-low transition-all duration-300 relative notification-btn" onclick="openMobileNotif(event)">
                <span class="material-symbols-outlined text-on-surface-variant text-2xl notification-icon">notifications</span>
                <span id="notificationBadgeMobile" class="notification-badge has-new absolute top-1 right-1 text-xs" style="display: {{ $reviewerUnreadCount > 0 ? 'block' : 'none' }}; width: 16px; height: 16px; min-width: 16px;">{{ $reviewerUnreadCount > 9 ? '9+' : $reviewerUnreadCount }}</span>
            </button>

            <!-- Mobile Notification Dropdown Modal -->
            <div id="notificationDropdownMobile" style="display: none;" class="sm:hidden fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-2xl shadow-2xl border-t border-outline-variant">
                <div class="max-h-[70vh] flex flex-col">
                    <div class="flex items-center justify-between px-4 py-4 border-b border-outline-variant bg-surface-container-low sticky top-0">
                        <h3 class="font-semibold text-primary">Notifikasi</h3>
                        <button type="button" onclick="closeMobileNotif()" class="p-2 rounded-lg hover:bg-white transition-colors">
                            <i class="fas fa-times text-[#4b5563]"></i>
                        </button>
                    </div>
                    <div id="notificationListMobile" class="overflow-y-auto flex-1">
                        <div class="p-4 text-center text-on-surface-variant">Memuat...</div>
                    </div>
                    <div class="border-t border-outline-variant p-4 sticky bottom-0 bg-white">
                        <a href="{{ route('reviewer.notifikasi.index') }}" class="block w-full text-center py-2 px-4 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                            Lihat Semua Notifikasi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile -->
            <div class="flex items-center gap-2 cursor-pointer" onclick="featureInDevelopment('Profile')">
                <div class="text-right hidden sm:block">
                    <span class="text-[13.5px] font-semibold text-[#0f1e2e] block leading-tight">{{ Auth::user()->name ?? 'Reviewer' }}</span>
                    <span class="text-[10px] font-bold tracking-[0.6px] uppercase text-[#94a3b8] block">{{ Auth::user()->role ?? 'Reviewer' }}</span>
                </div>
                <div class="w-9 h-9 rounded-full bg-[#1a3a5c] text-white text-[12px] font-bold flex items-center justify-center border-2 border-[#e8ecf0] flex-shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'RV', 0, 2)) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="p-3 sm:p-4 md:p-6 lg:px-7 lg:py-6">
        @yield('content')
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-2.5 pointer-events-none"></div>

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
        toast.innerHTML = `<i class="fas ${type === 'info' ? 'fa-circle-info' : 'fa-circle-check'} text-sm"></i><span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('toast-in');
            toast.classList.add('toast-out');
            setTimeout(() => toast.remove(), 200);
        }, 3000);
    }

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
        fetch('{{ route("reviewer.notifikasi.latest") }}', {
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
                    container.innerHTML = `
                        <div class="text-center py-8">

                            <div class="w-16 h-16 mx-auto bg-surface-container-low rounded-full flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-outline text-3xl">
                                    notifications_none
                                </span>
                            </div>

                            <p class="text-sm text-on-surface-variant">
                                Tidak ada notifikasi baru
                            </p>

                        </div>
                        `;
                    return;
                }

                container.innerHTML = data.notifications.map(notif => {

                    const iconStyle = getIconStyle(notif.type);

                    return `
                        <a href="{{ url('/reviewer/notifikasi/redirect') }}/${notif.id}"
                        class="notification-dropdown-link flex items-start gap-3 p-4 hover:bg-surface-container-low transition-colors border-b border-outline-variant last:border-0 ${notif.status === 'unread' ? 'bg-surface-container-low' : ''}">

                            <div class="w-10 h-10 rounded-full ${iconStyle.bg} flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined ${iconStyle.text} text-lg">
                                    ${getIconByType(notif.type)}
                                </span>
                            </div>

                            <div class="flex-1 min-w-0">

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-1">

                                    <p class="text-sm font-semibold flex items-center gap-2
                                        ${notif.status === 'unread' ? 'text-primary' : 'text-on-surface'}">

                                        ${notif.title}

                                        ${
                                            notif.status === 'unread'
                                            ? '<span class="w-2 h-2 rounded-full bg-primary inline-block"></span>'
                                            : ''
                                        }

                                    </p>

                                    <span class="text-xs text-on-surface-variant whitespace-nowrap">
                                        ${new Date(notif.created_at).toLocaleDateString('id-ID')}
                                    </span>

                                </div>

                                <p class="text-sm text-on-surface-variant truncate">
                                    ${notif.message}
                                </p>

                            </div>

                        </a>
                    `;

                }).join('');
            })
            .catch(error => {
                console.error('Error loading reviewer notifications:', error);
                const container = document.getElementById('notificationList');
                if (container) {
                    container.innerHTML = '<div class="p-4 text-center text-error">Gagal memuat notifikasi</div>';
                }
            });
    }

    function getIconByType(type) {
        const icons = {
            'review_assignment': 'assignment',
            'proposal_status': 'article',
            'revision_request': 'edit_note',
            'document_ready': 'description',
            'account_activation': 'check_circle',
        };
        return icons[type] || 'notifications';
    }

    function getIconStyle(type) {
        const styles = {
            'review_assignment': {
                bg: 'bg-blue-50',
                text: 'text-blue-600'
            },

            'proposal_status': {
                bg: 'bg-primary/10',
                text: 'text-primary'
            },

            'revision_request': {
                bg: 'bg-amber-50',
                text: 'text-amber-600'
            },

            'document_ready': {
                bg: 'bg-secondary/10',
                text: 'text-secondary'
            },

            'account_activation': {
                bg: 'bg-emerald-50',
                text: 'text-emerald-600'
            }
        };

        return styles[type] || {
            bg: 'bg-surface-container-low',
            text: 'text-on-surface-variant'
        };
    }

    document.addEventListener('click', function(event) {
        const btn = document.getElementById('notificationBtn');
        const dropdown = document.getElementById('notificationDropdown');
        if (!btn || !dropdown) return;

        if (!btn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
            notificationDropdownOpen = false;
        }
    });

    // ═══════════════════════════════════════════════════════
    // RESPONSIVE SIDEBAR FUNCTIONS
    // ═══════════════════════════════════════════════════════

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        
        const isOpen = sidebar.classList.contains('translate-x-0');
        
        if (isOpen) {
            closeSidebar();
        } else {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            backdrop.classList.remove('hidden');
        }
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        backdrop.classList.add('hidden');
    }

    // Close sidebar when clicking on a link
    document.querySelectorAll('#sidebar a').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });

    // Close sidebar when window resizes to lg or larger
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });

    // ═══════════════════════════════════════════════════════
    // SIMPLE MOBILE NOTIFICATION FUNCTIONS
    // ═══════════════════════════════════════════════════════

    function openMobileNotif(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const modal = document.getElementById('notificationDropdownMobile');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Load notifications
        fetch('{{ route("reviewer.notifikasi.latest") }}', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('notificationListMobile');
            
            if (data.unread_count > 0) {
                const badge = document.getElementById('notificationBadgeMobile');
                if (badge) {
                    badge.style.display = 'block';
                    badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                }
            }

            if (!data.notifications || data.notifications.length === 0) {
                container.innerHTML = '<div class="text-center py-8"><div class="w-16 h-16 mx-auto bg-surface-container-low rounded-full flex items-center justify-center mb-3"><span class="material-symbols-outlined text-outline text-3xl">notifications_none</span></div><p class="text-sm text-on-surface-variant">Tidak ada notifikasi baru</p></div>';
                return;
            }

            container.innerHTML = data.notifications.map(notif => {
                const iconStyle = getIconStyle(notif.type);
                return `
                    <a href="{{ url('/reviewer/notifikasi/redirect') }}/${notif.id}" onclick="closeMobileNotif()" class="notification-dropdown-link flex items-start gap-3 p-4 hover:bg-surface-container-low transition-colors border-b border-outline-variant last:border-0 ${notif.status === 'unread' ? 'bg-surface-container-low' : ''}">
                        <div class="w-10 h-10 rounded-full ${iconStyle.bg} flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined ${iconStyle.text} text-lg">${getIconByType(notif.type)}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col gap-1">
                                <p class="text-sm font-semibold ${notif.status === 'unread' ? 'text-primary' : 'text-on-surface'}">${notif.title}</p>
                                <p class="text-xs text-on-surface-variant">${new Date(notif.created_at).toLocaleDateString('id-ID')}</p>
                                <p class="text-sm text-on-surface-variant">${notif.message}</p>
                            </div>
                        </div>
                    </a>
                `;
            }).join('');
        })
        .catch(err => {
            console.error(err);
            document.getElementById('notificationListMobile').innerHTML = '<div class="p-4 text-center text-error">Gagal memuat notifikasi</div>';
        });
    }

    function closeMobileNotif() {
        const modal = document.getElementById('notificationDropdownMobile');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking backdrop
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('notificationDropdownMobile');
        if (modal && modal.style.display === 'block' && e.target === modal) {
            closeMobileNotif();
        }
    });
</script>
@stack('scripts')
</body>
</html>