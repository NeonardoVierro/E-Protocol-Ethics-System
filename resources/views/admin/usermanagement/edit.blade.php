@extends('layouts.admin')

@section('title', 'Edit User - Admin')
@section('page-title', 'Edit User')

@section('content')
<div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <form id="edit-user-form" method="POST" action="{{ route('admin.usermanagement.update', $user) }}" class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        @csrf
        @method('PUT')

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" required>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Role</label>
            <select name="role" required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                @foreach($roles as $role)
                    <option value="{{ $role }}" {{ (old('role', $user->roles->first()->name ?? '') === $role) ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
            <select name="status" required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="pending" {{ old('status', $user->status) === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inaktif</option>
            </select>
        </div>

        <div class="lg:col-span-2 flex flex-wrap gap-3">
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-[#1e3a5f] px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#162d4a]">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.usermanagement.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-user-form');
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const name = form.querySelector('input[name="name"]').value || '';
            Swal.fire({
                title: 'Simpan perubahan?',
                text: `Simpan perubahan untuk ${name}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
