@extends('layouts.dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <!-- Redirect jika yang login tapi bukan peneliti -->
    @auth
        @if(!Auth::user()->hasRole('peneliti'))
            <script>
                window.location.href = '{{ route("dashboard") }}';
            </script>
        @endif
    @endauth

    <!-- Dashboard Peneliti - Sama untuk semua orang (login atau tidak) -->
    <div class="py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Ethical Clearance System</h1>
            <p class="text-xl text-gray-600">Sistem Pengajuan Etika Penelitian</p>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Apa itu Ethical Clearance?</h3>
                <p class="text-gray-600 text-sm">Etika penelitian adalah prinsip dan standar untuk melindungi hak, kesejahteraan, dan privasi subjek penelitian dalam setiap kegiatan penelitian.</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Mengapa Diperlukan?</h3>
                <p class="text-gray-600 text-sm">Untuk memastikan penelitian dilakukan dengan standar etika tertinggi dan memberikan perlindungan maksimal kepada seluruh pihak yang terlibat.</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Proses Sederhana</h3>
                <p class="text-gray-600 text-sm">Ajukan proposal, tunggu review dari tim ahli, dapatkan sertifikat etika untuk penelitian Anda.</p>
            </div>
        </div>

        <!-- Statistik (untuk semua) -->
        @auth
            <div class="bg-white rounded-lg shadow p-6 mb-12">
                <h2 class="text-2xl font-bold mb-6">Dashboard Saya</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <h3 class="text-gray-700 font-semibold mb-2">Proposal Saya</h3>
                        <p class="text-3xl font-bold text-blue-600">0</p>
                    </div>
                    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                        <h3 class="text-gray-700 font-semibold mb-2">Pengajuan Aktif</h3>
                        <p class="text-3xl font-bold text-green-600">0</p>
                    </div>
                    <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                        <h3 class="text-gray-700 font-semibold mb-2">Notifikasi</h3>
                        <p class="text-3xl font-bold text-yellow-600">0</p>
                    </div>
                </div>
            </div>
        @endauth

        <!-- CTA Section -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-12 text-center text-white">
            <h2 class="text-3xl font-bold mb-4">Siap Mengajukan Ethical Clearance?</h2>
            <p class="text-blue-100 mb-8 text-lg">Ajukan proposal penelitian Anda sekarang dan dapatkan persetujuan etika dari tim ahli kami</p>
            
            @auth
                <a href="#" class="inline-block bg-white text-blue-600 font-semibold py-3 px-8 rounded-lg hover:bg-blue-50 transition duration-200">
                    Ajukan Ethical Clearance
                </a>
            @else
                <div class="space-y-3 max-w-sm mx-auto">
                    <a href="{{ route('login') }}" class="block bg-white text-blue-600 font-semibold py-3 px-8 rounded-lg hover:bg-blue-50 transition duration-200">
                        Login untuk Ajukan EC
                    </a>
                    <p class="text-blue-100">atau</p>
                    <a href="{{ route('register') }}" class="block bg-blue-500 text-white font-semibold py-3 px-8 rounded-lg hover:bg-blue-700 transition duration-200">
                        Daftar Akun Baru
                    </a>
                </div>
            @endauth
        </div>

        <!-- Info untuk guest -->
        @guest
            <div class="mt-12 bg-gray-50 rounded-lg shadow p-8">
                <h3 class="text-xl font-semibold mb-4 text-gray-900">Demo Account Tersedia</h3>
                <p class="text-gray-600 mb-4">Coba sistem kami dengan akun demo berikut:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded border border-gray-200">
                        <p class="font-semibold text-gray-900">Email:</p>
                        <p class="text-gray-600 font-mono">peneliti@ethical.com</p>
                    </div>
                    <div class="bg-white p-4 rounded border border-gray-200">
                        <p class="font-semibold text-gray-900">Password:</p>
                        <p class="text-gray-600 font-mono">password</p>
                    </div>
                </div>
            </div>
        @endguest
    </div>
</div>
@endsection
