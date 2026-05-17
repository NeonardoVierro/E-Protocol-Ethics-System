@extends('layouts.dashboard')

@section('title', 'Proposal Berhasil Diajukan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-0 to-slate-100 py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-10 text-center">
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-emerald-50 mb-6">
                <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-slate-900 mb-4">Proposal Berhasil Diajukan!</h1>
            <p class="text-slate-600 mb-6">Terima kasih. Proposal Anda telah berhasil dikirim dan akan segera ditinjau oleh tim reviewer.</p>
            <div class="space-y-4">
                <a href="{{ route('peneliti.dashboard') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                    Kembali ke Dashboard
                </a>
                <a href="{{ route('pengajuan.riwayat-pengajuan') }}" class="inline-flex items-center justify-center w-full px-6 py-3 border border-slate-300 text-slate-700 rounded-xl font-semibold hover:bg-slate-50 transition-colors">
                    Lihat Riwayat Pengajuan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection