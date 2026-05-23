@extends('layouts.dashboard')

@section('title', $title)

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    <div class="bg-amber-50/50 rounded-xl border border-amber-200 p-8 lg:p-12 text-center">
        <div class="max-w-md mx-auto">
            <div class="w-24 h-24 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-amber-600 text-5xl">{{ $icon }}</span>
            </div>
            <h2 class="text-2xl font-bold text-amber-700 mb-3">Akun Belum Diaktivasi</h2>
            <p class="text-amber-600 mb-6">{{ $message }}</p>
            <div class="bg-amber-100/50 rounded-lg p-4 text-left">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-600">info</span>
                    <div>
                        <p class="text-sm text-amber-700 font-semibold mb-1">Apa yang harus dilakukan?</p>
                        <ul class="text-sm text-amber-600 space-y-1">
                            <li>✓ Tunggu notifikasi email aktivasi dari sekretariat</li>
                            <li>✓ Atau hubungi sekretariat Komisi Etik Penelitian</li>
                            <li>✓ Setelah diaktivasi, Anda dapat mengakses semua fitur</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-primary hover:underline">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection