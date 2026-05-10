@extends('layouts.admin')

@section('title', 'User Management - Admin')
@section('page-title', 'User Management')
@section('breadcrumb', 'Kelola manajemen user untuk ethical clearance')

@section('content')

{{-- ═══════════════════════════════════════════
     Page Header
═══════════════════════════════════════════ --}}
<div class="flex items-start justify-between mb-6">
    <button onclick="featureInDevelopment('Tambah User Baru')"
            class="inline-flex items-center gap-2 bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors cursor-pointer">
        <i class="fas fa-plus text-xs"></i>
        Tambah User Baru
    </button>
</div>

{{-- ═══════════════════════════════════════════
     Stat Cards (3 kolom)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-3 gap-5 mb-6">
    @php
    $stats = [
        ['icon'=>'fas fa-user-group',    'iconBg'=>'bg-blue-50',    'iconColor'=>'text-blue-500',   'label'=>'TOTAL USER',           'value'=>'1,248'],
        ['icon'=>'fas fa-shield-check',  'iconBg'=>'bg-emerald-50', 'iconColor'=>'text-emerald-500','label'=>'USER AKTIF',           'value'=>'1,152'],
        ['icon'=>'fas fa-comment-dots',  'iconBg'=>'bg-orange-50',  'iconColor'=>'text-orange-400', 'label'=>'MENUNGGU VERIFIKASI',  'value'=>'96'],
    ];
    @endphp
    @foreach($stats as $s)
    <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 {{ $s['iconBg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="{{ $s['icon'] }} {{ $s['iconColor'] }} text-lg"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">{{ $s['label'] }}</p>
            <p class="text-[22px] font-bold text-slate-900 leading-none tracking-tight">{{ $s['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════
     Filter Bar + Tabel
═══════════════════════════════════════════ --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- Filter Bar --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            {{-- Dropdown Semua Role --}}
            <div class="relative">
                <select id="filter-role"
                        class="appearance-none pl-9 pr-8 py-2 text-[13px] font-medium text-slate-700 border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all cursor-pointer">
                    <option value="">Semua Role</option>
                    <option>Reviewer</option>
                    <option>Researcher</option>
                    <option>Admin</option>
                    <option>Secretary</option>
                    <option>Superadmin</option>
                </select>
                <i class="fas fa-sliders absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>

            {{-- Dropdown Semua Status --}}
            <div class="relative">
                <select id="filter-status"
                        class="appearance-none pl-9 pr-8 py-2 text-[13px] font-medium text-slate-700 border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all cursor-pointer">
                    <option value="">Semua Status</option>
                    <option>Aktif</option>
                    <option>Inaktif</option>
                    <option>Menunggu Verifikasi</option>
                </select>
                <i class="fas fa-circle-check absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>
        </div>
        <span class="text-[12.5px] text-slate-400">Menampilkan <span class="font-semibold text-slate-600">10</span> dari <span class="font-semibold text-slate-600">1,248</span> user</span>
    </div>

    {{-- Table --}}
    <table class="w-full" id="user-table">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">User Info</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Role</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Status</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Last Login</th>
                <th class="text-right text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50" id="user-tbody">
            @php
            $users = [
                [
                    'initials' => null,
                    'avatar'   => 'https://i.pravatar.cc/40?img=11',
                    'name'     => 'Prof. Dr. Budiman Santoso',
                    'email'    => 'budiman.santoso@univ.ac.id',
                    'role'     => 'REVIEWER',
                    'roleClass'=> 'bg-slate-100 text-slate-600',
                    'status'   => 'aktif',
                    'login'    => '12 Okt 2023, 08:45',
                ],
                [
                    'initials' => null,
                    'avatar'   => 'https://i.pravatar.cc/40?img=5',
                    'name'     => 'Dr. Sarah Wijaya',
                    'email'    => 'sarah.wijaya@research.id',
                    'role'     => 'RESEARCHER',
                    'roleClass'=> 'bg-slate-100 text-slate-600',
                    'status'   => 'menunggu',
                    'login'    => 'Belum Pernah',
                ],
                [
                    'initials' => 'AM',
                    'avatar'   => null,
                    'name'     => 'Andi Maulana, S.Kom',
                    'email'    => 'andi.maulana@it.univ.ac.id',
                    'role'     => 'ADMIN',
                    'roleClass'=> 'bg-[#1e3a5f] text-white',
                    'status'   => 'aktif',
                    'login'    => '11 Okt 2023, 16:20',
                ],
                [
                    'initials' => null,
                    'avatar'   => 'https://i.pravatar.cc/40?img=52',
                    'name'     => 'Dr. Hendra Kurniawan',
                    'email'    => 'h.kurniawan@ethics.univ.ac.id',
                    'role'     => 'SECRETARY',
                    'roleClass'=> 'bg-amber-50 text-amber-700',
                    'status'   => 'aktif',
                    'login'    => '12 Okt 2023, 10:15',
                ],
                [
                    'initials' => 'RT',
                    'avatar'   => null,
                    'name'     => 'Rina Tandjung, M.Si',
                    'email'    => 'rina.tandjung@univ.ac.id',
                    'role'     => 'RESEARCHER',
                    'roleClass'=> 'bg-slate-100 text-slate-600',
                    'status'   => 'inaktif',
                    'login'    => '05 Sep 2023, 14:02',
                ],
            ];
            @endphp

            @foreach($users as $u)
            <tr class="hover:bg-slate-50/60 transition-colors">
                {{-- User Info --}}
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        @if($u['avatar'])
                            <img src="{{ $u['avatar'] }}" alt="{{ $u['name'] }}"
                                 class="w-9 h-9 rounded-full object-cover border border-slate-200 flex-shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ $u['initials'] }}
                            </div>
                        @endif
                        <div>
                            <div class="text-[13.5px] font-semibold text-slate-800">{{ $u['name'] }}</div>
                            <div class="text-[11.5px] text-slate-400">{{ $u['email'] }}</div>
                        </div>
                    </div>
                </td>

                {{-- Role --}}
                <td class="px-4 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase {{ $u['roleClass'] }}">
                        {{ $u['role'] }}
                    </span>
                </td>

                {{-- Status --}}
                <td class="px-4 py-4">
                    @if($u['status'] === 'aktif')
                        <span class="inline-flex items-center gap-1.5 text-[13px] font-medium text-slate-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_0_3px_#d1fae5]"></span> Aktif
                        </span>
                    @elseif($u['status'] === 'menunggu')
                        <span class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-orange-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span> Menunggu Verifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-[13px] font-medium text-red-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Inaktif
                        </span>
                    @endif
                </td>

                {{-- Last Login --}}
                <td class="px-4 py-4 text-[13px] text-slate-500">{{ $u['login'] }}</td>

                {{-- Actions --}}
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="featureInDevelopment('Edit User')"
                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer" title="Edit">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        <button onclick="featureInDevelopment('Reset Password')"
                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer" title="Reset Password">
                            <i class="fas fa-rotate-right text-xs"></i>
                        </button>
                        <button onclick="featureInDevelopment('Nonaktifkan User')"
                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-400 transition-colors cursor-pointer" title="Nonaktifkan">
                            <i class="fas fa-ban text-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
        <span class="text-[12.5px] text-slate-400">Halaman <span class="font-semibold text-slate-600">1</span> dari <span class="font-semibold text-slate-600">125</span></span>
        <div class="flex items-center gap-1">
            <button onclick="featureInDevelopment('Previous')"
                    class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors cursor-pointer">
                <i class="fas fa-chevron-left text-xs"></i>
            </button>
            <button class="w-8 h-8 flex items-center justify-center bg-[#1e3a5f] text-white text-[13px] font-semibold rounded-lg cursor-pointer">1</button>
            <button onclick="featureInDevelopment('Page 2')"
                    class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-[13px] text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">2</button>
            <button onclick="featureInDevelopment('Page 3')"
                    class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-[13px] text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">3</button>
            <span class="w-8 h-8 flex items-center justify-center text-slate-400 text-[13px]">...</span>
            <button onclick="featureInDevelopment('Page 125')"
                    class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-[13px] text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">125</button>
            <button onclick="featureInDevelopment('Next')"
                    class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors cursor-pointer">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Filter by role & status (client-side dummy filter)
    const roleSelect   = document.getElementById('filter-role');
    const statusSelect = document.getElementById('filter-status');
    const rows         = document.querySelectorAll('#user-tbody tr');

    function applyFilter() {
        const role   = roleSelect.value.toUpperCase();
        const status = statusSelect.value.toLowerCase();
        rows.forEach(row => {
            const rowRole   = row.querySelector('td:nth-child(2)')?.textContent.trim().toUpperCase() ?? '';
            const rowStatus = row.querySelector('td:nth-child(3)')?.textContent.trim().toLowerCase() ?? '';
            const matchRole   = !role   || rowRole.includes(role);
            const matchStatus = !status || rowStatus.includes(status);
            row.style.display = matchRole && matchStatus ? '' : 'none';
        });
    }

    roleSelect.addEventListener('change', applyFilter);
    statusSelect.addEventListener('change', applyFilter);
});
</script>
@endpush