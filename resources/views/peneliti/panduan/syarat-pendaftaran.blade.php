@extends('layouts.dashboard')

@section('title', 'Syarat Pendaftaran Ethical Clearance')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 space-y-8">

    <!-- Header Section -->
    <div class="relative bg-gradient-to-r from-primary/5 via-surface-container-low to-primary/5 rounded-2xl p-6 lg:p-8 border border-outline-variant overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-primary/5 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary text-3xl">assignment</span>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-primary mb-2">Syarat Pendaftaran Ethical Clearance</h1>
                    <p class="text-on-surface-variant max-w-2xl">
                        Berikut adalah syarat dan ketentuan yang harus dipenuhi sebelum mengajukan ethical clearance 
                        kepada Komisi Etik Penelitian.
                    </p>
                </div>
            </div>
            <div class="flex-shrink-0">
                <div class="bg-primary/10 rounded-full px-4 py-2 text-sm text-primary font-semibold">
                    Terakhir diperbarui: Januari 2026
                </div>
            </div>
        </div>
    </div>

    <!-- Persyaratan Umum -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
        <div class="border-b border-outline-variant bg-surface-container-low px-6 py-4">
            <h2 class="text-lg font-semibold text-primary flex items-center gap-2">
                <span class="material-symbols-outlined">verified</span>
                Persyaratan Umum
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3 p-3 bg-surface-container-low rounded-lg hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-primary text-xl mt-0.5">check_circle</span>
                    <div>
                        <p class="font-semibold text-sm text-on-surface">Akun Teraktivasi</p>
                        <p class="text-xs text-on-surface-variant">Peneliti harus memiliki akun yang telah diaktivasi oleh sekretariat Komisi Etik Penelitian</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-surface-container-low rounded-lg hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-primary text-xl mt-0.5">check_circle</span>
                    <div>
                        <p class="font-semibold text-sm text-on-surface">Kepatuhan Etik</p>
                        <p class="text-xs text-on-surface-variant">Penelitian harus mematuhi prinsip-prinsip etik penelitian (menghormati orang, keadilan, integritas)</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-surface-container-low rounded-lg hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-primary text-xl mt-0.5">check_circle</span>
                    <div>
                        <p class="font-semibold text-sm text-on-surface">Format Dokumen</p>
                        <p class="text-xs text-on-surface-variant">Semua dokumen harus diunggah dalam format PDF maksimal 10MB per file</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-surface-container-low rounded-lg hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-primary text-xl mt-0.5">check_circle</span>
                    <div>
                        <p class="font-semibold text-sm text-on-surface">Keaslian Data</p>
                        <p class="text-xs text-on-surface-variant">Data dan informasi yang disampaikan harus asli dan dapat dipertanggungjawabkan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dokumen yang Diperlukan -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
        <div class="border-b border-outline-variant bg-surface-container-low px-6 py-4">
            <h2 class="text-lg font-semibold text-primary flex items-center gap-2">
                <span class="material-symbols-outlined">description</span>
                Dokumen yang Diperlukan
            </h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-container-low">
                        <tr class="border-b border-outline-variant">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-primary w-16">No</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-primary">Dokumen</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-primary w-24">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-primary w-32">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-3 px-4 text-sm text-on-surface-variant">1</td>
                            <td class="py-3 px-4 text-sm text-on-surface font-medium">Surat pengantar dari institusi</td>
                            <td class="py-3 px-4"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Wajib</span></td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">Ditujukan kepada Komisi Etik Penelitian</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-3 px-4 text-sm text-on-surface-variant">2</td>
                            <td class="py-3 px-4 text-sm text-on-surface font-medium">Proposal/protokol yang sudah disahkan</td>
                            <td class="py-3 px-4"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Wajib</span></td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">Mencakup latar belakang, metode, dan analisis data</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-3 px-4 text-sm text-on-surface-variant">3</td>
                            <td class="py-3 px-4 text-sm text-on-surface font-medium">Formulir penjelasan kepada calon partisipan</td>
                            <td class="py-3 px-4"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Wajib</span></td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">Penjelasan tentang tujuan, prosedur, dan risiko penelitian</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-3 px-4 text-sm text-on-surface-variant">4</td>
                            <td class="py-3 px-4 text-sm text-on-surface font-medium">Informed Consent Form (ICF)</td>
                            <td class="py-3 px-4"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Wajib</span></td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">Lembar persetujuan setelah penjelasan</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-3 px-4 text-sm text-on-surface-variant">5</td>
                            <td class="py-3 px-4 text-sm text-on-surface font-medium">Alat pengumpulan data</td>
                            <td class="py-3 px-4"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Wajib</span></td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">Kuesioner, panduan wawancara, FGD, dll</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-3 px-4 text-sm text-on-surface-variant">6</td>
                            <td class="py-3 px-4 text-sm text-on-surface font-medium">Daftar nama tim peneliti</td>
                            <td class="py-3 px-4"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Wajib</span></td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">Lengkap dengan afiliasi institusi</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-3 px-4 text-sm text-on-surface-variant">7</td>
                            <td class="py-3 px-4 text-sm text-on-surface font-medium">CV peneliti utama</td>
                            <td class="py-3 px-4"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Wajib</span></td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">Menunjukkan pengalaman dan kompetensi peneliti</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-3 px-4 text-sm text-on-surface-variant">8</td>
                            <td class="py-3 px-4 text-sm text-on-surface font-medium">Anggaran penelitian</td>
                            <td class="py-3 px-4"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Opsional</span></td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">Baik yang mendapatkan dana sponsor atau mandiri</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-3 px-4 text-sm text-on-surface-variant">9</td>
                            <td class="py-3 px-4 text-sm text-on-surface font-medium">Iklan/Advertisement</td>
                            <td class="py-3 px-4"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Opsional</span></td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">Jika penelitian menggunakan media rekrutmen</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-3 px-4 text-sm text-on-surface-variant">10</td>
                            <td class="py-3 px-4 text-sm text-on-surface font-medium">Brosur penelitian</td>
                            <td class="py-3 px-4"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Opsional</span></td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">Materi informasi untuk calon partisipan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Ketentuan Tambahan -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
        <div class="border-b border-outline-variant bg-surface-container-low px-6 py-4">
            <h2 class="text-lg font-semibold text-primary flex items-center gap-2">
                <span class="material-symbols-outlined">gavel</span>
                Ketentuan Tambahan
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-secondary/5 to-transparent rounded-xl border border-secondary/10 hover:border-secondary/30 transition-all">
                    <span class="material-symbols-outlined text-secondary text-2xl">schedule</span>
                    <div>
                        <p class="font-semibold text-sm text-on-surface">Masa Berlaku</p>
                        <p class="text-sm text-on-surface-variant mt-1">Surat Ethical Clearance berlaku selama <span class="font-semibold text-primary">1 tahun</span> sejak tanggal diterbitkan</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-secondary/5 to-transparent rounded-xl border border-secondary/10 hover:border-secondary/30 transition-all">
                    <span class="material-symbols-outlined text-secondary text-2xl">autorenew</span>
                    <div>
                        <p class="font-semibold text-sm text-on-surface">Perpanjangan</p>
                        <p class="text-sm text-on-surface-variant mt-1">Perpanjangan dapat diajukan paling lambat <span class="font-semibold text-primary">1 bulan</span> sebelum masa berlaku habis</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-secondary/5 to-transparent rounded-xl border border-secondary/10 hover:border-secondary/30 transition-all">
                    <span class="material-symbols-outlined text-secondary text-2xl">edit_note</span>
                    <div>
                        <p class="font-semibold text-sm text-on-surface">Amandemen</p>
                        <p class="text-sm text-on-surface-variant mt-1">Perubahan protokol penelitian (amendment) harus mendapatkan <span class="font-semibold text-primary">persetujuan ulang</span> dari Komisi Etik</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-secondary/5 to-transparent rounded-xl border border-secondary/10 hover:border-secondary/30 transition-all">
                    <span class="material-symbols-outlined text-secondary text-2xl">report_problem</span>
                    <div>
                        <p class="font-semibold text-sm text-on-surface">Penghentian Dini</p>
                        <p class="text-sm text-on-surface-variant mt-1">Peneliti wajib melaporkan jika penelitian <span class="font-semibold text-primary">dihentikan sebelum waktunya</span> (termination)</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-secondary/5 to-transparent rounded-xl border border-secondary/10 hover:border-secondary/30 transition-all md:col-span-2">
                    <span class="material-symbols-outlined text-secondary text-2xl">account_balance</span>
                    <div>
                        <p class="font-semibold text-sm text-on-surface">Tindak Lanjut</p>
                        <p class="text-sm text-on-surface-variant mt-1">Peneliti wajib melaporkan hasil penelitian dan kejadian tidak diinginkan (adverse events) selama penelitian berlangsung</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Template Section -->
    <div class="bg-gradient-to-r from-primary/10 via-surface-container-low to-primary/10 rounded-xl p-6 border border-primary/20">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-2xl">download</span>
                </div>
                <div>
                    <h3 class="font-semibold text-primary">Belum memiliki template dokumen?</h3>
                    <p class="text-sm text-on-surface-variant">Download template formulir pengajuan ethical clearance</p>
                </div>
            </div>
            <button onclick="window.location.href='{{ route('pengajuan.download-template') }}'" class="px-6 py-2.5 bg-primary text-on-primary rounded-lg font-semibold text-sm hover:bg-primary-container transition-all duration-300 shadow-md hover:shadow-lg flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">download</span>
                Download Template
            </button>
        </div>
    </div>
        <!-- Scroll to Top Button -->
    <button id="scrollToTop" class="fixed bottom-8 right-8 w-12 h-12 rounded-full bg-primary text-on-primary shadow-lg hover:bg-primary-container transition-all duration-300 transform translate-y-16 opacity-0 flex items-center justify-center z-50 hover:scale-110 active:scale-95">
        <span class="material-symbols-outlined">arrow_upward</span>
    </button>

    <!-- Scroll to Top Logic -->
    <script>
    const scrollToTopBtn = document.getElementById('scrollToTop');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            scrollToTopBtn.classList.remove('translate-y-16', 'opacity-0');
            scrollToTopBtn.classList.add('translate-y-0', 'opacity-100');
        } else {
            scrollToTopBtn.classList.remove('translate-y-0', 'opacity-100');
            scrollToTopBtn.classList.add('translate-y-16', 'opacity-0');
        }
    });
    
    scrollToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    </script>

    <!-- Call to Action -->
    <div class="text-center pt-4">
        <div class="inline-flex flex-col sm:flex-row gap-4">
            @auth
                @if(auth()->user()->hasRole('peneliti') && auth()->user()->status === 'active')
                    <button onclick="window.location.href='{{ route('pengajuan.upload-proposal') }}'" class="px-8 py-3 bg-primary text-on-primary rounded-xl font-semibold hover:bg-primary-container transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Mulai Pengajuan Sekarang
                    </button>
                @elseif(auth()->user()->status === 'pending')
                    <div class="px-8 py-3 bg-amber-100 text-amber-700 rounded-xl font-semibold">
                        Akun Anda sedang menunggu aktivasi oleh sekretariat
                    </div>
                @endif
            @else
                <button onclick="window.location.href='{{ route('login') }}'" class="px-8 py-3 bg-primary text-on-primary rounded-xl font-semibold hover:bg-primary-container transition-all duration-300 shadow-lg hover:shadow-xl">
                    Login untuk Mengajukan
                </button>
                <button onclick="window.location.href='{{ route('register') }}'" class="px-8 py-3 border-2 border-primary text-primary rounded-xl font-semibold hover:bg-primary/5 transition-all duration-300">
                    Daftar Akun Baru
                </button>
            @endauth
        </div>
    </div>

</div>
@endsection