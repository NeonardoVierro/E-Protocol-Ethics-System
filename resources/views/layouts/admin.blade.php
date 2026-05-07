<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ethical Clearance System')</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
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
<body class="font-['DM_Sans'] m-0 bg-[#f4f6f9]">

<!-- ═══════════════════════════════════
     SIDEBAR
═══════════════════════════════════ -->
<div class="w-64 min-h-screen bg-white border-r border-[#e8ecf0] fixed left-0 top-0 flex flex-col z-[100]">

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

        <!-- New Application -->
        <a href="#" class="flex items-center justify-center gap-[7px] mx-3 mb-2 px-4 py-2.5 bg-[#1e4d8c] text-white text-[13.5px] font-semibold rounded-xl no-underline cursor-pointer transition-all duration-180 hover:bg-[#1a3f78] hover:-translate-y-px w-[calc(100%-1.5rem)]">
            <i class="fas fa-plus text-[11px]"></i>
            New Application
        </a>
    </nav>

    <!-- Bottom: Settings & Support -->
    <div class="p-2 px-3 pb-4 border-t border-[#f0f2f5] flex flex-col gap-0.5">
        <a href="#" class="flex items-center gap-[11px] px-3 py-2 rounded-lg text-[13.5px] font-medium text-[#6b7280] no-underline transition-all duration-150 hover:bg-[#f5f7fa] hover:text-[#374151]">
            <i class="fas fa-gear text-[14px] w-[18px] text-center text-[#9ca3af] shrink-0"></i>
            Settings
        </a>
        <a href="#" class="flex items-center gap-[11px] px-3 py-2 rounded-lg text-[13.5px] font-medium text-[#6b7280] no-underline transition-all duration-150 hover:bg-[#f5f7fa] hover:text-[#374151]">
            <i class="fas fa-circle-question text-[14px] w-[18px] text-center text-[#9ca3af] shrink-0"></i>
            Support
        </a>
    </div>

</div>

<!-- ═══════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════ -->
<div class="ml-64 min-h-screen bg-[#f4f6f9]">

    <!-- Top Navbar -->
    <div class="bg-white border-b border-[#e8ecf0] px-7 h-[58px] flex items-center justify-between sticky top-0 z-[50]">
        <!-- Kiri: Title + Date Range -->
    <div class="flex items-center gap-3.5">
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

            <div class="flex items-center gap-2.5 cursor-pointer" onclick="featureInDevelopment('Profile')">
                <div class="text-right">
                    <span class="text-[13.5px] font-semibold text-[#0f1e2e] block leading-tight">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] font-bold tracking-[0.6px] uppercase text-[#94a3b8] block">{{ Auth::user()->role ?? 'Admin' }}</span>
                </div>
                <div class="w-9 h-9 rounded-full bg-[#1a3a5c] text-white text-[12px] font-bold flex items-center justify-center border-2 border-[#e8ecf0] shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
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
</script>
@stack('scripts')

</body>
</html>