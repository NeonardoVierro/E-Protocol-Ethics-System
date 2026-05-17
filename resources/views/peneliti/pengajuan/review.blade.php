@extends('layouts.dashboard')

@section('title', 'Review & Submit Proposal')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-0 to-slate-100 py-8 lg:py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        @auth
            @if(auth()->user()->hasRole('peneliti') && auth()->user()->status === 'active')
                <!-- Konten untuk user yang sudah login dan aktif -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <!-- Stepper -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between">
                                <!-- Step 1 -->
                                <div class="flex items-center flex-1">
                                    <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full font-semibold text-sm">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 h-1 bg-green-600 mx-3"></div>
                                </div>
                                <!-- Step 2 -->
                                <div class="flex items-center flex-1">
                                    <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full font-semibold text-sm">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 h-1 bg-blue-600 mx-3"></div>
                                </div>
                                <!-- Step 3 -->
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-semibold text-sm">3</div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <p class="text-xs font-medium text-slate-600">Informasi Dasar</p>
                                <p class="text-xs font-medium text-slate-600">Upload Berkas</p>
                                <p class="text-xs font-medium text-blue-600">Review & Submit</p>
                            </div>
                        </div>

                        <!-- Review Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-8 border border-slate-200">
                            <h1 class="text-3xl font-bold text-slate-900 mb-2">Review Proposal Anda</h1>
                            <p class="text-slate-600 mb-8">Pastikan semua informasi dan dokumen sudah benar sebelum mengirimkan</p>

                            <!-- Informasi Dasar Section -->
                            <div class="mb-8">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-xl font-semibold text-slate-900">Informasi Penelitian</h3>
                                    <a href="{{ route('pengajuan.upload-proposal') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                </div>

                                <div class="space-y-4">
                                    <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                        <p class="text-sm text-slate-600 mb-1">Judul Penelitian</p>
                                        <p class="text-lg font-semibold text-slate-900">{{ $proposalData['judul_penelitian'] }}</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                            <p class="text-sm text-slate-600 mb-1">Jenis Penelitian</p>
                                            <p class="text-lg font-semibold text-slate-900">{{ $proposalData['jenis_penelitian'] }}</p>
                                        </div>
                                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                            <p class="text-sm text-slate-600 mb-1">Bidang Ilmu</p>
                                            <p class="text-lg font-semibold text-slate-900">{{ $proposalData['bidang_ilmu'] }}</p>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                        <p class="text-sm text-slate-600 mb-1">Lokasi Penelitian</p>
                                        <p class="text-lg font-semibold text-slate-900">{{ $proposalData['lokasi_penelitian'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Dokumen Section -->
                            <div class="mb-8 pt-8 border-t border-slate-200">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-xl font-semibold text-slate-900">Dokumen yang Diupload</h3>
                                    <a href="{{ route('pengajuan.upload-berkas') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                </div>

                                <div class="space-y-4">
                                    @foreach($proposalFiles as $fieldName => $fileData)
                                    <div class="flex items-center gap-4 bg-blue-50 rounded-lg p-4 border border-blue-200">
                                        <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4 3a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v14H4V5zm3 4a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm0 3a1 1 0 011-1h6a1 1 0 110 2H8a1 1 0 01-1-1zm0 3a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1z"/>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-slate-900">{{ $fileData['template_name'] }}</p>
                                            <p class="text-xs text-slate-600">{{ $fileData['original_name'] }}</p>
                                        </div>
                                        <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Siap</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Terms Agreement -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                                <label class="flex items-start gap-4 cursor-pointer">
                                    <input type="checkbox" id="agreement" class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" required>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">Persetujuan Pengajuan</p>
                                        <p class="text-xs text-slate-600 mt-2">
                                            Saya dengan ini menyatakan bahwa semua informasi dan dokumen yang saya ajukan adalah benar dan lengkap. 
                                            Saya memahami bahwa memberikan informasi yang salah atau dokumen yang tidak sesuai dapat mengakibatkan 
                                            penolakan proposal atau tindakan lainnya sesuai dengan kebijakan institusi.
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex items-center gap-4">
                                <a href="{{ route('pengajuan.upload-berkas') }}" class="px-6 py-3 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-colors">
                                    Kembali
                                </a>
                                <form action="{{ route('pengajuan.final-submit') ?? '#' }}" method="POST" class="flex gap-4 flex-1" onsubmit="return submitProposal()">
                                    @csrf
                                    <button type="submit" id="submitBtn" disabled class="flex-1 px-6 py-3 bg-slate-300 text-slate-600 font-semibold rounded-lg cursor-not-allowed transition-colors">
                                        Kirim Proposal
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <!-- Checklist Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200 sticky top-8">
                            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Checklist Pengajuan
                            </h3>

                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                    </svg>
                                    <p class="text-sm text-slate-700">Informasi dasar lengkap</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                    </svg>
                                    <p class="text-sm text-slate-700">3 dokumen sudah diupload</p>
                                </div>

                                <div class="flex items-start gap-3" id="agreementCheckItem">
                                    <svg class="w-5 h-5 text-slate-300 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                    </svg>
                                    <p class="text-sm text-slate-600">Persetujuan telah disetujui</p>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-slate-200">
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <p class="text-xs font-semibold text-blue-900 mb-2">ℹ️ Informasi Pengajuan</p>
                                    <p class="text-xs text-blue-800 space-y-1">
                                        Setelah Anda mengirimkan proposal, tim reviewer akan mengevaluasinya. 
                                        Anda akan menerima notifikasi email mengenai status pengajuan Anda.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button class="w-full px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-colors">
                                    📋 Lihat Kebijakan Pengajuan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->status === 'pending')
                <div class="bg-amber-50 rounded-xl border border-amber-200 p-8 text-center max-w-md mx-auto">
                    <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-amber-700 mb-2">Akun Belum Diaktivasi</h2>
                </div>
            @endif
        @else
            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-12 text-center max-w-md mx-auto">
                <div class="w-24 h-24 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-3">Login Diperlukan</h2>
                <p class="text-slate-600 mb-6">Silakan login untuk melanjutkan review proposal Anda.</p>
                <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg inline-block">
                    Login Sekarang
                </a>
            </div>
        @endauth
    </div>
</div>

<script>
    const agreementCheckbox = document.getElementById('agreement');
    const submitBtn = document.getElementById('submitBtn');
    const agreementCheckItem = document.getElementById('agreementCheckItem');

    agreementCheckbox.addEventListener('change', function() {
        if (this.checked) {
            submitBtn.disabled = false;
            submitBtn.className = 'flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg';
            agreementCheckItem.querySelector('svg').className = 'w-5 h-5 text-green-600 flex-shrink-0 mt-0.5';
            agreementCheckItem.querySelector('p').className = 'text-sm text-slate-700';
        } else {
            submitBtn.disabled = true;
            submitBtn.className = 'flex-1 px-6 py-3 bg-slate-300 text-slate-600 font-semibold rounded-lg cursor-not-allowed transition-colors';
            agreementCheckItem.querySelector('svg').className = 'w-5 h-5 text-slate-300 flex-shrink-0 mt-0.5';
            agreementCheckItem.querySelector('p').className = 'text-sm text-slate-600';
        }
    });

    function submitProposal() {
        if (!document.getElementById('agreement').checked) {
            alert('Silakan setujui pernyataan sebelum mengirimkan proposal.');
            return false;
        }
    }
</script>
@endsection
