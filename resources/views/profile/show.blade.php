@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="min-h-screen bg-[#f3f6fb] py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-[32px] border border-[#dce3ec] bg-white p-8 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
            <div class="flex flex-col gap-4 lg:flex-row relative lg:items-center lg:justify-between">
                <a href="{{ route('dashboard') }}" class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-[#dce3ec] bg-white text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 text-slate-700" fill="currentColor">
                            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                        </svg>
                    </a>
                <div class="flex items-center text-center gap-4">
                    <div>
                        <h1 class="text-4xl font-semibold text-slate-950">Profil Saya</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Lihat detail akun Anda dan lanjutkan ke pengeditan profil jika diperlukan.</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 rounded-2xl bg-[#1e3a5f] px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#162d4a]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm2.92.67l9.19-9.19 1.49 1.49-9.19 9.19H5.92v-1.49zm12.23-11.06a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/>
                    </svg>
                    <span>Edit Profil</span>
                </a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-[32px] border border-[#dce3ec] bg-white p-8 shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                <h2 class="text-xl font-semibold text-slate-950 mb-6">Informasi Akun</h2>
                <div class="space-y-4">
                    <div class="rounded-3xl border border-[#e2e8f0] bg-[#f8fafc] p-5">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400 mb-2">Nama</p>
                        <p class="text-base font-semibold text-slate-900">{{ $user->name }}</p>
                    </div>
                    <div class="rounded-3xl border border-[#e2e8f0] bg-[#f8fafc] p-5">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400 mb-2">Email</p>
                        <p class="text-base font-semibold text-slate-900">{{ $user->email }}</p>
                    </div>
                    <div class="rounded-3xl border border-[#e2e8f0] bg-[#f8fafc] p-5">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400 mb-2">Role</p>
                        <p class="text-base font-semibold text-slate-900">{{ $user->roles->pluck('name')->first() ?? 'Pengguna' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[32px] border border-[#dce3ec] bg-white p-8 shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                <h2 class="text-xl font-semibold text-slate-950 mb-6">Keamanan & Aktivitas</h2>
                <div class="space-y-4">
                    <div class="rounded-3xl border border-[#e2e8f0] bg-[#f8fafc] p-5">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400 mb-2">Status Akun</p>
                        <p class="text-base font-semibold text-slate-900">{{ ucfirst($user->status ?? 'aktif') }}</p>
                    </div>
                    <div class="rounded-3xl border border-[#e2e8f0] bg-[#f8fafc] p-5">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400 mb-2">Dibuat</p>
                        <p class="text-base font-semibold text-slate-900">{{ $user->created_at?->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
