@extends('layouts.admin')

@section('title', 'Profil Admin')
@section('page-title', 'Profil Admin')
@section('breadcrumb', 'Kelola profil dan keluar dari aplikasi')

@section('content')
<div class="grid grid-cols-12 gap-5">
    <div class="col-span-7">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Informasi Akun</h2>
            <div class="grid gap-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 mb-2">Nama</p>
                    <p class="text-base font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 mb-2">Email</p>
                    <p class="text-base font-semibold text-slate-900">{{ auth()->user()->email }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 mb-2">Role</p>
                    <p class="text-base font-semibold text-slate-900">{{ auth()->user()->primary_role ?? auth()->user()->role ?? 'Admin' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 mb-2">Status Akun</p>
                    <p class="text-base font-semibold text-slate-900">{{ ucfirst(auth()->user()->status ?? 'active') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900 mb-3">Tindakan</h2>
            <p class="text-sm text-slate-500 mb-6">Edit informasi profil atau keluar dari aplikasi untuk menjaga keamanan akun Anda.</p>

            <a href="{{ route('profile.edit') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-[#1e3a5f] px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#162d4a]">
                Edit Profil
            </a>
        </div>
    </div>
</div>
@endsection
