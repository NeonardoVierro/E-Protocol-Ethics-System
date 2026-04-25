@extends('layouts.dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Dashboard Sekretaris</h2>
        <p class="text-gray-600">Selamat datang, {{ Auth::user()->name }}!</p>
        
        {{-- Tampilkan menu berdasarkan role --}}
        @hasrole('sekretaris')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="bg-blue-50 p-4 rounded">
                    <h3 class="font-semibold">Proposal Masuk</h3>
                    <p class="text-2xl">0</p>
                </div>
                <div class="bg-green-50 p-4 rounded">
                    <h3 class="font-semibold">Verifikasi Pending</h3>
                    <p class="text-2xl">0</p>
                </div>
                <div class="bg-purple-50 p-4 rounded">
                    <h3 class="font-semibold">Reviewer Terassign</h3>
                    <p class="text-2xl">0</p>
                </div>
            </div>
        @endhasrole
        
        {{-- Tampilkan untuk Ketua --}}
        @hasrole('ketua')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <div class="bg-blue-50 p-4 rounded">
                    <h3 class="font-semibold">Menunggu Tanda Tangan</h3>
                    <p class="text-2xl">0</p>
                </div>
                <div class="bg-green-50 p-4 rounded">
                    <h3 class="font-semibold">Dokumen Selesai</h3>
                    <p class="text-2xl">0</p>
                </div>
            </div>
        @endhasrole
    </div>
</div>
@endsection