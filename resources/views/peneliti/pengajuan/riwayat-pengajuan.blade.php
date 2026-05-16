@extends('layouts.dashboard')

@section('title', 'Riwayat Pengajuan')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    @auth
        @if(auth()->user()->hasRole('peneliti') && auth()->user()->status === 'active')
            <!-- Konten untuk user yang sudah login dan aktif -->
            <div class="bg-white rounded-xl border border-outline-variant p-8">
                <div class="text-center mb-8">
                    <div class="w-20 h-20 mx-auto bg-surface-container-low rounded-full flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-primary text-4xl">history</span>
                    </div>
                    <h2 class="text-xl font-semibold text-primary mb-2">Riwayat Pengajuan</h2>
                    <p class="text-on-surface-variant">Belum ada data pengajuan.</p>
                </div>

                <!-- Contoh Progress Bar (akan diisi dinamis nanti) -->
                <div class="max-w-2xl mx-auto mt-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-on-surface">Status: <span class="text-primary">Belum Ada Pengajuan</span></span>
                    </div>
                    <div class="w-full bg-surface-container-high rounded-full h-2.5">
                        <div class="bg-primary h-2.5 rounded-full" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between mt-3 text-xs text-on-surface-variant">
                        <span class="text-center flex-1">New</span>
                        <span class="text-center flex-1">Review</span>
                        <span class="text-center flex-1">Approved</span>
                    </div>
                </div>

                <div class="text-center mt-8 p-6 bg-surface-container-low rounded-lg">
                    <span class="material-symbols-outlined text-outline text-3xl mb-2">inbox</span>
                    <p class="text-on-surface-variant text-sm">Anda belum memiliki riwayat pengajuan ethical clearance.</p>
                    <a href="{{ route('pengajuan.upload-proposal') }}" class="inline-block mt-3 text-primary text-sm font-medium hover:underline">
                        Mulai Pengajuan Baru →
                    </a>
                </div>
            </div>
        @elseif(auth()->user()->status === 'pending')
            <div class="bg-amber-50 rounded-xl border border-amber-200 p-8 text-center">
                <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-amber-600 text-4xl">pending</span>
                </div>
                <h2 class="text-xl font-semibold text-amber-700 mb-2">Akun Belum Diaktivasi</h2>
                <p class="text-amber-600 mb-4">Akun Anda sedang menunggu aktivasi oleh sekretariat Komisi Etik Penelitian.</p>
                <p class="text-sm text-amber-600">Setelah akun diaktivasi, Anda dapat mengajukan ethical clearance dan melihat riwayat pengajuan.</p>
            </div>
        @endif
    @else
        <!-- Konten untuk user yang belum login -->
        <div class="bg-gradient-to-r from-primary/5 to-surface-container-low rounded-xl border border-primary/20 p-8 lg:p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-primary text-5xl">lock</span>
                </div>
                <h2 class="text-2xl font-bold text-primary mb-3">Akses Diperlukan</h2>
                <p class="text-on-surface-variant mb-6">
                    Untuk melihat riwayat pengajuan dan tracking proposal, Anda harus login terlebih dahulu.<br>
                    Login untuk melihat status pengajuan ethical clearance Anda.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('login') }}" class="px-6 py-3 bg-primary text-on-primary rounded-lg font-semibold hover:bg-primary-container transition-all duration-300 shadow-md hover:shadow-lg">
                        Login Sekarang
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-3 border-2 border-primary text-primary rounded-lg font-semibold hover:bg-primary/5 transition-all duration-300">
                        Daftar Akun Baru
                    </a>
                </div>
                <div class="mt-6 pt-6 border-t border-outline-variant">
                    <p class="text-sm text-on-surface-variant">
                        Lacak status pengajuan Anda: <strong>New Proposal → On Review → Approved</strong>
                    </p>
                </div>
            </div>
        </div>
    @endauth
</div>
@endsection