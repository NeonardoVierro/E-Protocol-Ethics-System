@extends('layouts.admin')

@section('title', 'User Management - Admin')
@section('page-title', 'User Management')
@section('breadcrumb', 'Kelola manajemen user untuk ethical clearance')

@section('content')

<div class="flex items-start justify-between mb-6">
    <button id="button-toggle-create"
            class="inline-flex items-center gap-2 bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
        <i class="fas fa-plus text-xs"></i>
        Tambah User Baru
    </button>
</div>

@if(session('success'))
    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div id="create-user-panel" class="hidden mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between gap-4 mb-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Tambah User Baru</h2>
            <p class="text-sm text-slate-500">Buat akun baru sesuai peran yang tersedia.</p>
        </div>
        <button id="button-close-create" type="button" class="text-slate-500 hover:text-slate-800">Batal</button>
    </div>

    <form id="create-user-form" method="POST" action="{{ route('admin.usermanagement.store') }}" class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        @csrf

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Role</label>
            <select name="role" required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                <option value="">Pilih role</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
            <select name="status" required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inaktif</option>
            </select>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Password</label>
            <div class="relative">
                <input type="password" name="password" id="password-field"
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
                <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-eye text-sm"></i>
                </button>
            </div>
        </div>

        <div class="lg:col-span-2 flex flex-wrap gap-3">
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-[#1e3a5f] px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#162d4a]">
                Simpan User
            </button>
            <button type="button" id="button-cancel-create"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50">
                Batal
            </button>
        </div>
    </form>
</div>

<div class="grid grid-cols-3 gap-5 mb-6">
    @php
        $statCards = [
            ['icon' => 'fas fa-user-group',   'iconBg' => 'bg-blue-50',    'iconColor' => 'text-blue-500',    'label' => 'TOTAL USER',          'value' => number_format($stats['total'])],
            ['icon' => 'fas fa-shield-check', 'iconBg' => 'bg-emerald-50', 'iconColor' => 'text-emerald-500', 'label' => 'USER AKTIF',          'value' => number_format($stats['active'])],
            ['icon' => 'fas fa-comment-dots', 'iconBg' => 'bg-orange-50',  'iconColor' => 'text-orange-400',  'label' => 'MENUNGGU VERIFIKASI', 'value' => number_format($stats['pending'])],
        ];
    @endphp
    @foreach($statCards as $card)
    <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 {{ $card['iconBg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="{{ $card['icon'] }} {{ $card['iconColor'] }} text-lg"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">{{ $card['label'] }}</p>
            <p class="text-[22px] font-bold text-slate-900 leading-none tracking-tight">{{ $card['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <form id="filter-form" method="GET" action="{{ route('admin.usermanagement.index') }}" class="flex flex-col gap-4 px-6 py-4 border-b border-slate-100 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <select name="role" class="appearance-none pl-9 pr-8 py-2 text-[13px] font-medium text-slate-700 border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all cursor-pointer">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <i class="fas fa-sliders absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>

            <div class="relative">
                <select name="status" class="appearance-none pl-9 pr-8 py-2 text-[13px] font-medium text-slate-700 border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inaktif</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                </select>
                <i class="fas fa-circle-check absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>

            <div class="relative min-w-[240px]">
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau email"
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-[13px] text-slate-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" />
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-[12.5px] text-slate-400">Menampilkan <span class="font-semibold text-slate-600">{{ $users->count() }}</span> dari <span class="font-semibold text-slate-600">{{ $users->total() }}</span> user</span>
            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-[#1e3a5f] px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-[#162d4a]">Terapkan</button>
        </div>
    </form>

    <table class="w-full" id="user-table">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">User Info</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Role</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Status</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Dibuat</th>
                <th class="text-right text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50" id="user-tbody">
            @forelse($users as $user)
                @php
                    $roleName = $user->primary_role;
                    $roleClasses = [
                        'admin' => 'bg-[#1e3a5f] text-white',
                        'peneliti' => 'bg-slate-100 text-slate-600',
                        'sekretaris' => 'bg-amber-50 text-amber-700',
                        'reviewer' => 'bg-slate-100 text-slate-600',
                        'ketua' => 'bg-indigo-50 text-indigo-700',
                    ];
                    $roleClass = $roleClasses[$roleName] ?? 'bg-slate-100 text-slate-600';
                    $nameWords = preg_split('/\s+/', trim($user->name));
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
                @endphp
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ $initials ?: 'US' }}
                            </div>
                            <div>
                                <div class="text-[13.5px] font-semibold text-slate-800">{{ $user->name }}</div>
                                <div class="text-[11.5px] text-slate-400">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase {{ $roleClass }}">{{ strtoupper($roleName) }}</span>
                    </td>

                    <td class="px-4 py-4">
                        @if($user->status === 'active')
                            <span class="inline-flex items-center gap-1.5 text-[13px] font-medium text-slate-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_0_3px_#d1fae5]"></span> Aktif
                            </span>
                        @elseif($user->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-orange-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span> Menunggu Verifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-[13px] font-medium text-red-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Inaktif
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-4 text-[13px] text-slate-500">{{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}</td>

                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.usermanagement.edit', $user) }}" 
                               class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" title="Edit">
                                <i class="fas fa-pen text-xs"></i>
                            </a>

                            <form method="POST" action="{{ route('admin.usermanagement.reset', $user) }}">
                                @csrf
                                <button type="submit" 
                                        class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" title="Reset Password">
                                    <i class="fas fa-rotate-right text-xs"></i>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.usermanagement.toggle', $user) }}">
                                @csrf
                                <button type="submit" 
                                        class="w-7 h-7 rounded-md flex items-center justify-center {{ $user->status === 'active' ? 'text-slate-400 hover:bg-red-50 hover:text-red-400' : 'text-slate-400 hover:bg-slate-100 hover:text-slate-600' }} transition-colors" title="{{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas fa-ban text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-slate-500">Tidak ada user yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="flex flex-col gap-3 px-6 py-4 border-t border-slate-100 lg:flex-row lg:items-center lg:justify-between">
        <span class="text-[12.5px] text-slate-400">Halaman <span class="font-semibold text-slate-600">{{ $users->currentPage() }}</span> dari <span class="font-semibold text-slate-600">{{ $users->lastPage() }}</span></span>
        <div class="flex flex-wrap items-center gap-2">
            {{ $users->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const createPanel = document.getElementById('create-user-panel');
        const toggleButton = document.getElementById('button-toggle-create');
        const closeButton = document.getElementById('button-close-create');
        const cancelButton = document.getElementById('button-cancel-create');

        function toggleCreatePanel() {
            createPanel.classList.toggle('hidden');
            if (!createPanel.classList.contains('hidden')) {
                window.scrollTo({ top: createPanel.offsetTop - 20, behavior: 'smooth' });
            }
        }

        toggleButton.addEventListener('click', toggleCreatePanel);
        closeButton.addEventListener('click', toggleCreatePanel);
        cancelButton.addEventListener('click', toggleCreatePanel);

        // Toggle password visibility
        const togglePasswordBtn = document.getElementById('toggle-password');
        const passwordField = document.getElementById('password-field');
        if (togglePasswordBtn && passwordField) {
            togglePasswordBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                togglePasswordBtn.querySelector('i').classList.toggle('fa-eye');
                togglePasswordBtn.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }

        // Intercept reset-password forms
        document.querySelectorAll('form[action*="/reset-password"]').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const email = form.closest('tr')?.querySelector('td:nth-child(1) .text-slate-400')?.textContent?.trim() || '';
                Swal.fire({
                    title: 'Reset Password?',
                    text: `Reset password untuk ${email} ke default?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, reset',
                    cancelButtonText: 'Batal',
                }).then(result => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Intercept toggle-status forms
        document.querySelectorAll('form[action*="/toggle-status"]').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const email = form.closest('tr')?.querySelector('td:nth-child(1) .text-slate-400')?.textContent?.trim() || '';
                const isActive = form.querySelector('button[title]')?.getAttribute('title')?.toLowerCase()?.includes('nonaktif') ?? true;
                const actionText = isActive ? 'Nonaktifkan' : 'Aktifkan';
                Swal.fire({
                    title: actionText + ' user?',
                    text: `${actionText} akun ${email}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: actionText,
                    cancelButtonText: 'Batal',
                }).then(result => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        const createUserForm = document.getElementById('create-user-form');
        if (createUserForm) {
            createUserForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const name = this.querySelector('input[name="name"]').value.trim();
                const email = this.querySelector('input[name="email"]').value.trim();
                Swal.fire({
                    title: 'Simpan pengguna baru?',
                    text: `Buat akun untuk ${name || 'user baru'} (${email || 'tanpa email'})?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan',
                    cancelButtonText: 'Batal',
                }).then(result => {
                    if (result.isConfirmed) {
                        createUserForm.submit();
                    }
                });
            });
        }

        // Show flash success as SweetAlert if present
        const flash = {!! json_encode(session('success')) !!};
        if (flash) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: flash,
                timer: 2500,
                showConfirmButton: false
            });
        }
    });
</script>
@endpush
