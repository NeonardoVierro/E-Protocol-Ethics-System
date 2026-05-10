@extends('layouts.admin')

@section('title', 'Role & Permission - Admin')
@section('page-title', 'Role & Permission')
@section('breadcrumb', 'Kelola peran dan perizinan untuk ethical clearance')

@section('content')

{{-- ═══════════════════════════════════════════
     Page Header
═══════════════════════════════════════════ --}}
<div class="flex items-start justify-between mb-6">
    <button onclick="featureInDevelopment('Tambah Role Baru')"
            class="inline-flex items-center gap-2 bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors cursor-pointer">
        <i class="fas fa-circle-plus text-xs"></i>
        Tambah Role Baru
    </button>
</div>

{{-- ═══════════════════════════════════════════
     2 Kolom: Daftar Role (kiri) + Konfigurasi Izin (kanan)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-12 gap-5" x-data="rolePermission()">

    {{-- ── Daftar Role (kiri) ── --}}
    <div class="col-span-4 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <span class="text-[15px] font-bold text-slate-900">Daftar Role</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-emerald-50 text-emerald-700">6 Active</span>
        </div>

        <div class="divide-y divide-slate-50 p-2">
            @php
            $roles = [
                ['key'=>'superadmin', 'icon'=>'fas fa-shield-halved', 'iconBg'=>'bg-[#1e3a5f]',  'iconColor'=>'text-white',      'name'=>'Superadmin',    'desc'=>'Akses penuh sistem'],
                ['key'=>'admin',      'icon'=>'fas fa-user-tie',      'iconBg'=>'bg-slate-100',   'iconColor'=>'text-slate-500',  'name'=>'Admin',         'desc'=>'Pengelola operasional'],
                ['key'=>'reviewer',   'icon'=>'fas fa-comment-dots',  'iconBg'=>'bg-slate-100',   'iconColor'=>'text-slate-500',  'name'=>'Reviewer',      'desc'=>'Tim penilai proposal'],
                ['key'=>'ketua',      'icon'=>'fas fa-building-columns','iconBg'=>'bg-slate-100', 'iconColor'=>'text-slate-500',  'name'=>'Ketua Komisi',  'desc'=>'Otoritas persetujuan akhir'],
                ['key'=>'sekretaris', 'icon'=>'fas fa-id-card',       'iconBg'=>'bg-slate-100',   'iconColor'=>'text-slate-500',  'name'=>'Sekretaris',    'desc'=>'Administrasi & korespondensi'],
                ['key'=>'peneliti',   'icon'=>'fas fa-flask',         'iconBg'=>'bg-slate-100',   'iconColor'=>'text-slate-500',  'name'=>'Peneliti',      'desc'=>'Pengaju proposal etik'],
            ];
            @endphp

            @foreach($roles as $role)
            <button @click="selectRole('{{ $role['key'] }}')"
                    :class="selected === '{{ $role['key'] }}' ? 'bg-blue-50 border border-blue-200 rounded-xl' : 'hover:bg-slate-50 rounded-xl border border-transparent'"
                    class="w-full flex items-center gap-3 px-3 py-3 transition-all text-left cursor-pointer">
                <div class="w-9 h-9 {{ $role['iconBg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="{{ $role['icon'] }} {{ $role['iconColor'] }} text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[13.5px] font-semibold text-slate-800 leading-tight"
                         :class="selected === '{{ $role['key'] }}' ? 'text-[#1e3a5f]' : ''">
                        {{ $role['name'] }}
                    </div>
                    <div class="text-[11.5px] text-slate-400 truncate">{{ $role['desc'] }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all"
                         :class="selected === '{{ $role['key'] }}' ? 'border-[#1e3a5f] bg-[#1e3a5f]' : 'border-slate-300'">
                        <div class="w-1.5 h-1.5 rounded-full bg-white"
                             x-show="selected === '{{ $role['key'] }}'"></div>
                    </div>
                </div>
            </button>
            @endforeach
        </div>
    </div>

    {{-- ── Konfigurasi Izin (kanan) ── --}}
    <div class="col-span-8 bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col overflow-hidden">

        {{-- Header konfigurasi --}}
        <div class="flex items-start justify-between px-7 py-5 border-b border-slate-100">
            <div>
                <h3 class="text-[17px] font-bold text-slate-900">
                    Konfigurasi Izin:
                    <span class="text-[#1e6fbf]" x-text="roleLabel"></span>
                </h3>
                <p class="text-[12.5px] text-slate-500 mt-1">Mengatur secara detail modul apa saja yang dapat diakses oleh role ini.</p>
            </div>
            <button onclick="featureInDevelopment('Simpan Perubahan')"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[13px] font-semibold px-4 py-2.5 rounded-xl transition-colors cursor-pointer flex-shrink-0">
                <i class="fas fa-floppy-disk text-xs"></i>
                Simpan Perubahan
            </button>
        </div>

        {{-- Permission Groups --}}
        <div class="flex-1 overflow-y-auto px-7 py-5 space-y-7">

            @php
            $groups = [
                [
                    'icon'  => 'fas fa-user-group',
                    'label' => 'USER MANAGEMENT',
                    'perms' => [
                        ['key'=>'view-users',   'label'=>'view-users',   'desc'=>'Dapat melihat daftar semua pengguna'],
                        ['key'=>'create-users', 'label'=>'create-users', 'desc'=>'Dapat menambahkan user baru ke sistem'],
                        ['key'=>'edit-users',   'label'=>'edit-users',   'desc'=>'Dapat mengubah profil & info user'],
                        ['key'=>'delete-users', 'label'=>'delete-users', 'desc'=>'Dapat menghapus/menonaktifkan user'],
                    ],
                ],
                [
                    'icon'  => 'fas fa-file-lines',
                    'label' => 'PROPOSAL MANAGEMENT',
                    'perms' => [
                        ['key'=>'view-proposal',   'label'=>'view-proposal',   'desc'=>'Akses baca semua dokumen proposal'],
                        ['key'=>'edit-proposal',   'label'=>'edit-proposal',   'desc'=>'Modifikasi konten proposal yang diajukan'],
                        ['key'=>'assign-reviewer', 'label'=>'assign-reviewer', 'desc'=>'Menugaskan penilai ke proposal tertentu'],
                        ['key'=>'publish-result',  'label'=>'publish-result',  'desc'=>'Menerbitkan sertifikat kliring etik'],
                    ],
                ],
                [
                    'icon'  => 'fas fa-gear',
                    'label' => 'SYSTEM & SECURITY',
                    'perms' => [
                        ['key'=>'manage-roles', 'label'=>'manage-roles', 'desc'=>'Konfigurasi struktur role & permission'],
                        ['key'=>'view-logs',    'label'=>'view-logs',    'desc'=>'Melihat audit trail aktivitas sistem'],
                    ],
                ],
            ];
            @endphp

            @foreach($groups as $group)
            <div>
                {{-- Group Label --}}
                <div class="flex items-center gap-2 mb-4">
                    <i class="{{ $group['icon'] }} text-slate-400 text-sm"></i>
                    <span class="text-[10.5px] font-bold tracking-widest uppercase text-slate-400">{{ $group['label'] }}</span>
                </div>

                {{-- Permission Items Grid --}}
                <div class="grid grid-cols-2 gap-3">
                    @foreach($group['perms'] as $perm)
                    <label class="flex items-start gap-3 p-3.5 border border-slate-200 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50/40 transition-all group"
                           :class="isChecked('{{ $perm['key'] }}') ? 'border-blue-300 bg-blue-50/40' : ''">
                        <div class="relative flex-shrink-0 mt-0.5">
                            <input type="checkbox"
                                   class="sr-only"
                                   :checked="isChecked('{{ $perm['key'] }}')"
                                   @change="togglePerm('{{ $perm['key'] }}')">
                            <div class="w-5 h-5 rounded flex items-center justify-center transition-all border-2"
                                 :class="isChecked('{{ $perm['key'] }}') ? 'bg-[#1e3a5f] border-[#1e3a5f]' : 'border-slate-300 bg-white'">
                                <i class="fas fa-check text-white text-[9px]"
                                   x-show="isChecked('{{ $perm['key'] }}')"></i>
                            </div>
                        </div>
                        <div>
                            <div class="text-[13px] font-semibold text-slate-700">{{ $perm['label'] }}</div>
                            <div class="text-[11.5px] text-slate-400 mt-0.5 leading-snug">{{ $perm['desc'] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>

        {{-- Footer buttons --}}
        <div class="flex items-center justify-between px-7 py-4 border-t border-slate-100 bg-slate-50/50">
            <button onclick="featureInDevelopment('Reset Default')"
                    class="text-[13.5px] font-semibold text-slate-600 hover:text-slate-800 transition-colors cursor-pointer px-4 py-2 rounded-xl hover:bg-slate-100">
                Reset Default
            </button>
            <button onclick="featureInDevelopment('Simpan Perubahan')"
                    class="inline-flex items-center gap-2 bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-[13.5px] font-semibold px-6 py-2.5 rounded-xl transition-colors cursor-pointer">
                Simpan Perubahan
            </button>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function rolePermission() {
    return {
        selected: 'superadmin',

        // Label tampilan per role
        roleLabels: {
            superadmin: 'Superadmin',
            admin:      'Admin',
            reviewer:   'Reviewer',
            ketua:      'Ketua Komisi',
            sekretaris: 'Sekretaris',
            peneliti:   'Peneliti',
        },

        // Permissions default tiap role
        rolePerms: {
            superadmin: ['view-users','create-users','edit-users','delete-users','view-proposal','edit-proposal','assign-reviewer','publish-result','manage-roles','view-logs'],
            admin:      ['view-users','create-users','edit-users','view-proposal','edit-proposal','assign-reviewer','view-logs'],
            reviewer:   ['view-proposal'],
            ketua:      ['view-proposal','edit-proposal','publish-result','view-logs'],
            sekretaris: ['view-users','view-proposal','assign-reviewer'],
            peneliti:   ['view-proposal'],
        },

        get roleLabel() {
            return this.roleLabels[this.selected] ?? this.selected;
        },

        selectRole(key) {
            this.selected = key;
        },

        isChecked(permKey) {
            return (this.rolePerms[this.selected] ?? []).includes(permKey);
        },

        togglePerm(permKey) {
            const perms = this.rolePerms[this.selected] ?? [];
            const idx   = perms.indexOf(permKey);
            if (idx === -1) {
                this.rolePerms[this.selected] = [...perms, permKey];
            } else {
                this.rolePerms[this.selected] = perms.filter(p => p !== permKey);
            }
        },
    };
}
</script>
@endpush