<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sekretaris Panel') | Ethical Clearance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes toast-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes toast-out { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(6px); } }
        .toast-in { animation: toast-in 0.26s cubic-bezier(.34,1.56,.64,1) forwards; }
        .toast-out { animation: toast-out 0.2s ease forwards; }
        .sidebar-link.active { background: #eef3fb; color: #1e4d8c; font-weight: 600; border-left-color: #2563eb; }
    </style>
</head>
<body class="font-['DM_Sans'] bg-[#f4f6f9]">

<div class="w-64 min-h-screen bg-white border-r border-[#e8ecf0] fixed left-0 top-0 flex flex-col z-[100]">
    <div class="flex items-center gap-2.5 px-5 py-5 border-b">
        <div class="w-[38px] h-[38px] bg-[#1a3a5c] rounded-xl flex items-center justify-center">
            <i class="fas fa-shield-halved text-white"></i>
        </div>
        <div>
            <span class="text-[15px] font-bold text-[#0f1e2e]">Ethics Portal</span>
            <span class="text-[9px] font-semibold uppercase text-[#94a3b8] block">Sekretariat</span>
        </div>
    </div>
    <nav class="flex-1 p-4 px-3">
        <a href="{{ route('sekretaris.dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sekretaris.dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt w-5"></i> Dashboard</a>
        <a href="{{ route('sekretaris.manajemen-proposal') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sekretaris.manajemen-proposal') ? 'active' : '' }}"><i class="fas fa-file-alt w-5"></i> Manajemen Proposal</a>
        <a href="{{ route('sekretaris.assign-reviewer') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sekretaris.assign-reviewer') ? 'active' : '' }}"><i class="fas fa-user-check w-5"></i> Assign Reviewer</a>
        <a href="{{ route('sekretaris.hasil-review') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sekretaris.hasil-review') ? 'active' : '' }}"><i class="fas fa-clipboard-list w-5"></i> Hasil Review</a>
        <a href="{{ route('sekretaris.keputusan') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sekretaris.keputusan') ? 'active' : '' }}"><i class="fas fa-gavel w-5"></i> Keputusan</a>
        <a href="{{ route('sekretaris.draft-ethical') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sekretaris.draft-ethical') ? 'active' : '' }}"><i class="fas fa-file-signature w-5"></i> Draft Ethical Clearance</a>
        <a href="{{ route('sekretaris.arsip') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sekretaris.arsip') ? 'active' : '' }}"><i class="fas fa-archive w-5"></i> Arsip Dokumen</a>
    </nav>
</div>

<div class="ml-64 min-h-screen">
    <div class="bg-white border-b px-7 h-[58px] flex items-center justify-between sticky top-0 z-50">
        <h1 class="text-xl font-bold">@yield('page-title')</h1>
        <div class="flex items-center gap-3">
            <i class="fas fa-bell text-gray-500 text-xl cursor-pointer" onclick="featureInDevelopment('Notifikasi')"></i>
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold">{{ Auth::user()->name ?? 'Sekretaris' }}</span>
                <div class="w-9 h-9 rounded-full bg-[#1a3a5c] text-white flex items-center justify-center">{{ strtoupper(substr(Auth::user()->name ?? 'SK', 0, 2)) }}</div>
            </div>
        </div>
    </div>
    <div class="p-6">
        @yield('content')
    </div>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-2"></div>
<script>
    function featureInDevelopment(feature) { showToast(feature + ' sedang dalam pengembangan', 'info'); }
    function showToast(message, type = 'info') {
        const container = document.getElementById('toast-container');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = 'bg-[#0f1e2e] text-white px-4 py-3 rounded-xl text-sm shadow-xl flex items-center gap-2 pointer-events-auto toast-in';
        toast.innerHTML = `<i class="fas ${type === 'info' ? 'fa-circle-info' : 'fa-circle-check'}"></i><span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('toast-in');
            toast.classList.add('toast-out');
            setTimeout(() => toast.remove(), 200);
        }, 3000);
    }
</script>
</body>
</html>