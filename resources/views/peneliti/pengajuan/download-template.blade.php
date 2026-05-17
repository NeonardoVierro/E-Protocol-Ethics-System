@extends('layouts.dashboard')

@section('title', 'Download Template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-0 to-slate-100 py-8 lg:py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">

        @auth
            @if(auth()->user()->hasRole('peneliti') && auth()->user()->status === 'active')

            @if(session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl mb-6 text-sm font-medium">
                <span class="material-symbols-outlined text-red-500 text-lg">error</span>
                {{ session('error') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ── Kiri: Card putih besar ── --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-8 border border-slate-200">

                        <h1 class="text-3xl font-bold text-slate-900 mb-2">Download Template Dokumen</h1>
                        <p class="text-slate-600 mb-8">
                            Unduh format dokumen resmi yang diperlukan untuk pengajuan kaji etik Anda. Pastikan
                            menggunakan versi terbaru untuk memperlancar proses review.
                        </p>

                        @if($templates->isEmpty())
                        <div class="py-12 text-center">
                            <div class="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-slate-900 mb-1">Belum Ada Template</h3>
                            <p class="text-sm text-slate-500">Admin belum mengunggah template. Silakan cek kembali nanti.</p>
                        </div>

                        @else
                        <div class="space-y-8">
                            @foreach($templates as $kategori => $items)
                            <div>
                                @php
                                    $katIcon  = match($kategori) {
                                        'Biomedis' => 'biotech',
                                        'Sosial'   => 'groups',
                                        default    => 'description',
                                    };
                                    $katColor = match($kategori) {
                                        'Biomedis' => 'text-blue-600 bg-blue-50 border-blue-200',
                                        'Sosial'   => 'text-emerald-600 bg-emerald-50 border-emerald-200',
                                        default    => 'text-purple-600 bg-purple-50 border-purple-200',
                                    };
                                @endphp
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-wider uppercase border {{ $katColor }}">
                                        <span class="material-symbols-outlined text-sm">{{ $katIcon }}</span>
                                        {{ $kategori }}
                                    </span>
                                    <span class="text-xs text-slate-500">{{ $items->count() }} template</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($items as $template)
                                    @php
                                        $ftUpper    = strtoupper($template->file_type);
                                        $badgeColor = match($ftUpper) {
                                            'PDF'  => 'bg-red-100 text-red-600',
                                            'XLSX' => 'bg-green-100 text-green-700',
                                            default => 'bg-blue-100 text-blue-700',
                                        };
                                        $iconName = match($ftUpper) {
                                            'PDF'  => 'picture_as_pdf',
                                            'XLSX' => 'table_chart',
                                            default => 'description',
                                        };
                                        $iconBg = match($ftUpper) {
                                            'PDF'  => 'bg-red-50 text-red-400',
                                            'XLSX' => 'bg-green-50 text-green-500',
                                            default => 'bg-blue-50 text-blue-500',
                                        };
                                    @endphp
                                    <div class="border border-slate-200 rounded-xl p-5 flex flex-col gap-3 hover:border-blue-300 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200">

                                        {{-- Icon + badge --}}
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 {{ $iconBg }}">
                                                <span class="material-symbols-outlined text-xl">{{ $iconName }}</span>
                                            </div>
                                            <span class="text-xs font-bold px-2 py-0.5 rounded-md {{ $badgeColor }}">
                                                {{ $ftUpper }}
                                            </span>
                                        </div>

                                        {{-- Nama + deskripsi --}}
                                        <div class="flex-1">
                                            <h3 class="text-sm font-semibold text-slate-900 leading-snug mb-1">
                                                {{ $template->nama_dokumen }}
                                            </h3>
                                            {{-- Deskripsi singkat dari admin --}}
                                            @if($template->deskripsi)
                                            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">
                                                {{ $template->deskripsi }}
                                            </p>
                                            @endif
                                        </div>

                                        {{-- Versi + download --}}
                                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                            <span class="text-xs text-slate-400">
                                                {{ $template->versi }} &middot; Updated {{ \Carbon\Carbon::parse($template->updated_at)->translatedFormat('M Y') }}
                                            </span>
                                            <a href="{{ route('pengajuan.download-template.file', $template->id) }}"
                                               class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors duration-150">
                                                <span class="material-symbols-outlined text-base">download</span>
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                    </div>
                </div>

                {{-- ── Kanan: Sidebar sticky ── --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-8 space-y-4">

                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200">
                            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Petunjuk Penggunaan
                            </h3>

                            <div class="space-y-4 text-sm text-slate-600">
                                <div class="flex gap-3">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Gunakan template yang disediakan untuk menghindari penolakan administratif oleh tim reviewer.</span>
                                </div>
                                <div class="flex gap-3">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Pastikan semua kolom yang bertanda bintang (*) dalam template telah diisi dengan benar.</span>
                                </div>
                                <div class="flex gap-3">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Dokumen akhir harus diunggah dalam format <strong class="font-semibold text-slate-900">PDF</strong> dengan ukuran maksimal 5MB.</span>
                                </div>
                            </div>

                            <div class="mt-6 pt-5 border-t border-slate-200">
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Butuh Bantuan?</p>
                                <a href="mailto:ethics.helpdesk@university.ac.id"
                                   class="flex items-center gap-2 text-sm text-slate-700 hover:text-blue-600 transition-colors mb-2">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    ethics.helpdesk@university.ac.id
                                </a>
                                <div class="flex items-center gap-2 text-sm text-slate-700">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    +62 21 8888 7777 (Ext. 102)
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-900 rounded-2xl p-6">
                            <p class="text-base font-bold text-white mb-1">Siap Submit?</p>
                            <p class="text-sm text-slate-400 mb-4 leading-relaxed">
                                Setelah melengkapi dokumen, klik tombol di bawah untuk memulai proses pengajuan kaji etik.
                            </p>
                            <a href="{{ route('pengajuan.upload-proposal') }}"
                               class="flex items-center justify-center w-full py-2.5 bg-white text-slate-900 rounded-xl text-sm font-semibold hover:bg-slate-100 transition-all duration-200 shadow-sm">
                                Mulai Pengajuan Baru
                            </a>
                        </div>

                    </div>
                </div>

            </div>

            @elseif(auth()->user()->status === 'pending')
            <div class="bg-amber-50 rounded-2xl border border-amber-200 p-10 text-center shadow-sm max-w-md mx-auto">
                <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-amber-700 mb-2">Akun Belum Diaktivasi</h2>
                <p class="text-amber-600 mb-1">Akun Anda sedang menunggu aktivasi oleh sekretariat Komisi Etik Penelitian.</p>
                <p class="text-sm text-amber-500">Silakan tunggu notifikasi email aktivasi atau hubungi sekretariat.</p>
            </div>
            @endif

        @else
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-12 text-center max-w-md mx-auto">
            <div class="w-24 h-24 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-3">Akses Diperlukan</h2>
            <p class="text-slate-600 mb-6 leading-relaxed">
                Untuk mendownload template pengajuan, Anda harus login terlebih dahulu.<br>
                Login atau daftar akun baru untuk mengakses template.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('login') }}"
                   class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                    Login Sekarang
                </a>
                <a href="{{ route('register') }}"
                   class="px-6 py-3 border-2 border-blue-600 text-blue-600 rounded-xl font-semibold hover:bg-blue-50 transition-colors">
                    Daftar Akun Baru
                </a>
            </div>
            <div class="mt-6 pt-5 border-t border-slate-200">
                <p class="text-xs text-slate-500">
                    Template tersedia: Formulir Pengajuan Telaah Etik &amp; Formulir Ringkasan Protokol Penelitian
                </p>
            </div>
        </div>
        @endauth

    </div>
</div>
@endsection