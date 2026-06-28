@extends('layouts.sekretaris')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('breadcrumb', 'Aktivasi Akun Peneliti')

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <h2 class="text-2xl font-semibold text-slate-900">Manajemen User</h2>
                <p class="max-w-2xl text-sm text-slate-500">Kelola akun peneliti yang menunggu aktivasi. Gunakan tombol aksi untuk menyetujui dan mengaktifkan akses mereka dengan cepat.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative w-full sm:w-72">
                    <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="search" placeholder="Cari nama atau email" class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-100" disabled>
                </div>
                <div class="inline-flex items-center gap-2 rounded-2xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-800">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    {{ $pendingUsers->count() }} user menunggu aktivasi
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="mt-1 rounded-2xl bg-emerald-100 p-2 text-emerald-700">
                <i class="fas fa-check"></i>
            </div>
            <div>
                <h3 class="font-semibold text-emerald-900">Berhasil</h3>
                <p class="text-sm text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="bg-slate-50 px-6 py-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Daftar Pengguna Pending</p>
                    <p class="text-sm text-slate-500">Tinjau akun dan aktifkan pengguna ketika Anda siap.</p>
                </div>
                <div class="text-sm text-slate-500">Total: <span class="font-semibold text-slate-900">{{ $pendingUsers->count() }}</span></div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Institusi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-slate-50">
                    @forelse($pendingUsers as $user)
                    <tr class="transition hover:bg-white">
                        <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $user->institution ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-amber-700">Pending</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('sekretaris.user-management.activate', $user->id) }}" method="POST" class="inline-block" data-user-name="{{ $user->name }}">
                                @csrf
                                <button type="button" data-username="{{ $user->name }}" class="activate-user-btn inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                                    <i class="fas fa-user-check"></i>
                                    Aktifkan
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">Tidak ada user pending aktivasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.activate-user-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            var form = button.closest('form');
            var userName = button.dataset.username || 'pengguna ini';

            if (!form) {
                return;
            }

            Swal.fire({
                title: 'Aktifkan akun?',
                html: '<strong>' + userName + '</strong> akan diberikan akses sebagai Peneliti.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, aktifkan',
                cancelButtonText: 'Batal',
                customClass: {
                    action: 'gap-3',
                    confirmButton: 'swal2-confirm bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl px-4 py-2',
                    cancelButton: 'swal2-cancel bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl px-4 py-2'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Sukses',
        text: @json(session('success')),
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false
    });
    @endif
});
</script>
@endpush