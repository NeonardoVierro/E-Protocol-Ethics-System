@extends('layouts.dashboard')

@section('title', $title)

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    <div class="bg-gradient-to-r from-primary/5 to-surface-container-low rounded-xl border border-primary/20 p-8 lg:p-12 text-center">
        <div class="max-w-md mx-auto">
            <div class="w-24 h-24 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-primary text-5xl">{{ $icon }}</span>
            </div>
            <h2 class="text-2xl font-bold text-primary mb-3">Akses Diperlukan</h2>
            <p class="text-on-surface-variant mb-6">{{ $message }}</p>
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
                    Hanya peneliti dengan akun terverifikasi yang dapat mengajukan ethical clearance.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection