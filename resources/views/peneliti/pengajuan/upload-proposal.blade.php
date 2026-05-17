@extends('layouts.dashboard')

@section('title', 'Upload Proposal')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-0 to-slate-100 py-8 lg:py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        @auth
            @if(auth()->user()->hasRole('peneliti') && auth()->user()->status === 'active')
                <!-- Konten untuk user yang sudah login dan aktif -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Form -->
                    <div class="lg:col-span-2">
                        <!-- Stepper -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between">
                                <!-- Step 1 -->
                                <div class="flex items-center flex-1">
                                    <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-semibold text-sm">1</div>
                                    <div class="flex-1 h-1 bg-slate-300 mx-3"></div>
                                </div>
                                <!-- Step 2 -->
                                <div class="flex items-center flex-1">
                                    <div class="flex items-center justify-center w-10 h-10 bg-slate-300 text-slate-600 rounded-full font-semibold text-sm">2</div>
                                    <div class="flex-1 h-1 bg-slate-300 mx-3"></div>
                                </div>
                                <!-- Step 3 -->
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 bg-slate-300 text-slate-600 rounded-full font-semibold text-sm">3</div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <p class="text-xs font-medium text-slate-600">Informasi Dasar</p>
                                <p class="text-xs font-medium text-slate-500">Upload Berkas</p>
                                <p class="text-xs font-medium text-slate-500">Review & Submit</p>
                            </div>
                        </div>

                        <!-- Form Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-8 border border-slate-200">
                            <h1 class="text-3xl font-bold text-slate-900 mb-2">Ajukan Proposal Penelitian</h1>
                            <p class="text-slate-600 mb-8">Lengkapi informasi dasar penelitian Anda di bawah ini</p>

                            <form action="{{ route('pengajuan.store') ?? '#' }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                                @csrf

                                @php
                                    $proposalData = $proposalData ?? [];
                                @endphp

                                <!-- Section: Informasi Dasar -->
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900 mb-6">Informasi Penelitian</h3>
                                    <div class="space-y-6">
                                        <!-- Nama Peneliti -->
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-900 mb-2">
                                                <span class="text-red-500">*</span> Nama Peneliti
                                            </label>
                                            <input type="text" name="nama_peneliti" placeholder="Masukkan nama peneliti" 
                                                value="{{ old('nama_peneliti', $proposalData['nama_peneliti'] ?? '') }}"
                                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                required>
                                        </div>

                                        <!-- Asal Instansi -->
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-900 mb-2">
                                                <span class="text-red-500">*</span> Asal Instansi
                                            </label>
                                            <input type="text" name="asal_instansi" placeholder="Masukkan nama instansi" 
                                                value="{{ old('asal_instansi', $proposalData['asal_instansi'] ?? '') }}"
                                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                required>
                                        </div>

                                        <!-- Judul Penelitian -->
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-900 mb-2">
                                                <span class="text-red-500">*</span> Judul Penelitian
                                            </label>
                                            <input type="text" name="judul_penelitian" placeholder="Masukkan judul penelitian Anda" 
                                                value="{{ old('judul_penelitian', $proposalData['judul_penelitian'] ?? '') }}"
                                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                required>
                                        </div>

                                        <!-- Jenis Penelitian -->
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                                    <span class="text-red-500">*</span> Jenis Penelitian
                                                </label>
                                                <select name="jenis_penelitian" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                                                    <option value="">Pilih jenis penelitian</option>
                                                    <option value="Kualitatif" {{ old('jenis_penelitian', $proposalData['jenis_penelitian'] ?? '') === 'Kualitatif' ? 'selected' : '' }}>Kualitatif</option>
                                                    <option value="Kuantitatif" {{ old('jenis_penelitian', $proposalData['jenis_penelitian'] ?? '') === 'Kuantitatif' ? 'selected' : '' }}>Kuantitatif</option>
                                                    <option value="Mixed Methods" {{ old('jenis_penelitian', $proposalData['jenis_penelitian'] ?? '') === 'Mixed Methods' ? 'selected' : '' }}>Mixed Methods</option>
                                                    <option value="Eksperimental" {{ old('jenis_penelitian', $proposalData['jenis_penelitian'] ?? '') === 'Eksperimental' ? 'selected' : '' }}>Eksperimental</option>
                                                    <option value="Deskriptif" {{ old('jenis_penelitian', $proposalData['jenis_penelitian'] ?? '') === 'Deskriptif' ? 'selected' : '' }}>Deskriptif</option>
                                                </select>
                                            </div>

                                            <!-- Bidang Ilmu -->
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                                    <span class="text-red-500">*</span> Bidang Ilmu
                                                </label>
                                                <select name="bidang_ilmu" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" required>
                                                    <option value="">Pilih bidang ilmu</option>
                                                    <option value="Biomedis" {{ old('bidang_ilmu', $proposalData['bidang_ilmu'] ?? '') === 'Biomedis' ? 'selected' : '' }}>Biomedis</option>
                                                    <option value="Sosial" {{ old('bidang_ilmu', $proposalData['bidang_ilmu'] ?? '') === 'Sosial' ? 'selected' : '' }}>Sosial</option>
                                                    <option value="Pendidikan" {{ old('bidang_ilmu', $proposalData['bidang_ilmu'] ?? '') === 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                                                    <option value="Teknik" {{ old('bidang_ilmu', $proposalData['bidang_ilmu'] ?? '') === 'Teknik' ? 'selected' : '' }}>Teknik</option>
                                                    <option value="Humaniora" {{ old('bidang_ilmu', $proposalData['bidang_ilmu'] ?? '') === 'Humaniora' ? 'selected' : '' }}>Humaniora</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Lokasi Penelitian -->
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-900 mb-2">
                                                <span class="text-red-500">*</span> Lokasi Penelitian
                                            </label>
                                            <input type="text" name="lokasi_penelitian" placeholder="Masukkan lokasi penelitian" 
                                                value="{{ old('lokasi_penelitian', $proposalData['lokasi_penelitian'] ?? '') }}"
                                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                required>
                                        </div>
                                    </div>
                                </div>



                                <!-- Form Actions -->
                                <div class="pt-8 border-t border-slate-200 flex items-center gap-4">
                                    <button type="reset" class="px-6 py-3 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-colors">
                                        Bersihkan
                                    </button>
                                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                                        Lanjut ke Upload Berkas
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <!-- Info Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200 sticky top-8">
                            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Panduan Pengajuan
                            </h3>

                            <div class="space-y-4 text-sm text-slate-600">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold">1</div>
                                    <div>
                                        <p class="font-medium text-slate-900">Isi Informasi Dasar</p>
                                        <p class="text-xs mt-1">Berikan informasi lengkap tentang penelitian Anda termasuk judul, jenis, dan lokasi penelitian.</p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold">2</div>
                                    <div>
                                        <p class="font-medium text-slate-900">Upload Dokumen</p>
                                        <p class="text-xs mt-1">Unggah dokumen proposal Anda dalam format PDF dengan ukuran maksimal 5MB.</p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold">3</div>
                                    <div>
                                        <p class="font-medium text-slate-900">Review & Submit</p>
                                        <p class="text-xs mt-1">Periksa kembali semua informasi dan dokumen Anda, kemudian submit untuk review.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-slate-200">
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <p class="text-xs font-semibold text-blue-900 mb-2">⏱️ Waktu Pemrosesan</p>
                                    <p class="text-xs text-blue-800">Proposalmu akan diproses dalam 2-3 hari kerja setelah submit.</p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button class="w-full px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-colors">
                                    📖 Lihat Panduan Lengkap
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->status === 'pending')
                <!-- Konten untuk user yang statusnya pending (belum diaktivasi) -->
                <div class="bg-amber-50 rounded-xl border border-amber-200 p-8 text-center max-w-md mx-auto">
                    <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-amber-700 mb-2">Akun Belum Diaktivasi</h2>
                    <p class="text-amber-600 mb-4">Akun Anda sedang menunggu aktivasi oleh sekretariat Komisi Etik Penelitian.</p>
                    <p class="text-sm text-amber-600">Silakan tunggu notifikasi email aktivasi atau hubungi sekretariat.</p>
                </div>
            @endif
        @else
            <!-- Konten untuk user yang belum login -->
            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-12 text-center max-w-md mx-auto">
                <div class="w-24 h-24 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-3">Login Diperlukan</h2>
                <p class="text-slate-600 mb-6">
                    Untuk mengajukan proposal penelitian, Anda harus login terlebih dahulu.<br>
                    Belum punya akun? Daftar sekarang gratis!
                </p>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                        Login Sekarang
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-3 border-2 border-blue-600 text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                        Daftar Akun Baru
                    </a>
                </div>
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <p class="text-xs text-slate-500">
                        Hanya peneliti dengan akun terverifikasi yang dapat mengajukan ethical clearance.
                    </p>
                </div>
            </div>
        @endauth
    </div>
</div>
@endsection