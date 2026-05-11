@extends('layouts.dashboard')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 space-y-8 lg:space-y-12">
    
    <!-- Hero Section -->
    <section class="relative rounded-2xl overflow-hidden bg-primary text-on-primary p-8 sm:p-12 lg:p-20 xl:p-24 flex flex-col items-center text-center shadow-xl group">
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></div>
        </div>
        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-1.5 mb-6 text-sm animate-fade-in">
                <span class="material-symbols-outlined text-sm">verified</span>
                <span>Terakreditasi Kemenristekdikti</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight mb-4 animate-slide-up">
                Ethical Clearance Management System
            </h1>
            <p class="text-base sm:text-lg lg:text-xl text-primary-fixed mb-8 animate-slide-up animation-delay-200">
                Layanan terpadu untuk pengajuan, peninjauan, dan manajemen persetujuan etik penelitian secara transparan dan akuntabel.
            </p>
            <div class="flex flex-wrap justify-center gap-4 animate-slide-up animation-delay-400">
                @auth
                    @if(auth()->user()->hasRole('peneliti') && auth()->user()->status === 'active')
                        <button onclick="window.location.href='#'" class="bg-white text-primary px-6 sm:px-8 py-3 rounded-full font-semibold text-base hover:bg-primary-fixed transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:scale-95">
                            Mulai Pengajuan Baru
                        </button>
                    @else
                        <button onclick="window.location.href='{{ route('login') }}'" class="bg-white text-primary px-6 sm:px-8 py-3 rounded-full font-semibold text-base hover:bg-primary-fixed transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:scale-95">
                            Login untuk Mulai Pengajuan
                        </button>
                    @endif
                    <button class="border-2 border-white text-white px-6 sm:px-8 py-3 rounded-full font-semibold text-base hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-0.5 active:scale-95">
                        Pelajari Lebih Lanjut
                    </button>
                @else
                    <button onclick="window.location.href='{{ route('login') }}'" class="bg-white text-primary px-6 sm:px-8 py-3 rounded-full font-semibold text-base hover:bg-primary-fixed transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:scale-95">
                        Mulai Pengajuan Baru
                    </button>
                    <button class="border-2 border-white text-white px-6 sm:px-8 py-3 rounded-full font-semibold text-base hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-0.5 active:scale-95">
                        Pelajari Lebih Lanjut
                    </button>
                @endauth
            </div>
        </div>
    </section>

    <!-- Quick Stats (Only for logged in researcher) -->
    @auth
        @if(auth()->user()->hasRole('peneliti') && auth()->user()->status === 'active')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-outline-variant p-5 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-primary text-3xl">description</span>
                        <span class="text-xs text-on-surface-variant bg-surface-container-low px-2 py-1 rounded-full">Total</span>
                    </div>
                    <p class="text-3xl font-bold text-primary">0</p>
                    <p class="text-sm text-on-surface-variant mt-1">Total Pengajuan</p>
                </div>
                <div class="bg-white rounded-xl border border-outline-variant p-5 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-secondary text-3xl">pending</span>
                        <span class="text-xs text-on-surface-variant bg-surface-container-low px-2 py-1 rounded-full">Proses</span>
                    </div>
                    <p class="text-3xl font-bold text-secondary">0</p>
                    <p class="text-sm text-on-surface-variant mt-1">Sedang Diproses</p>
                </div>
                <div class="bg-white rounded-xl border border-outline-variant p-5 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-emerald-600 text-3xl">verified</span>
                        <span class="text-xs text-on-surface-variant bg-surface-container-low px-2 py-1 rounded-full">Terbit</span>
                    </div>
                    <p class="text-3xl font-bold text-emerald-600">0</p>
                    <p class="text-sm text-on-surface-variant mt-1">Surat Terbit</p>
                </div>
                <div class="bg-white rounded-xl border border-outline-variant p-5 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-amber-600 text-3xl">edit_note</span>
                        <span class="text-xs text-on-surface-variant bg-surface-container-low px-2 py-1 rounded-full">Revisi</span>
                    </div>
                    <p class="text-3xl font-bold text-amber-600">0</p>
                    <p class="text-sm text-on-surface-variant mt-1">Perlu Revisi</p>
                </div>
            </div>
        @endif
    @endauth

    <!-- Three Main Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        
        <!-- 1. Informasi Umum -->
        <section class="bg-white rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">
            <div class="relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2"></div>
                <div class="p-6 lg:p-8">
                    <div class="mb-4">
                        <div class="w-12 h-12 rounded-full bg-surface-container-low flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-primary text-3xl">info</span>
                        </div>
                        <h2 class="text-xl lg:text-2xl font-semibold text-primary">Informasi Umum</h2>
                        <div class="w-12 h-0.5 bg-primary/30 mt-2 group-hover:w-20 transition-all duration-300"></div>
                    </div>
                    <p class="text-on-surface-variant leading-relaxed mb-6">
                        Ethical Clearance (Persetujuan Etik) adalah keterangan tertulis yang diberikan oleh Komisi Etik Penelitian 
                        untuk subjek manusia maupun hewan yang menyatakan bahwa suatu proposal penelitian layak dilaksanakan 
                        setelah memenuhi persyaratan tertentu.
                    </p>
                    <a href="#" class="inline-flex items-center gap-2 text-primary font-semibold text-sm hover:gap-3 transition-all duration-300 group/link">
                        Baca selengkapnya 
                        <span class="material-symbols-outlined text-sm group-hover/link:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- 2. Biaya Pengajuan -->
        <section class="bg-white rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">
            <div class="relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-secondary/5 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2"></div>
                <div class="p-6 lg:p-8">
                    <div class="mb-4">
                        <div class="w-12 h-12 rounded-full bg-surface-container-low flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-secondary text-3xl">payments</span>
                        </div>
                        <h2 class="text-xl lg:text-2xl font-semibold text-primary">Biaya Pengajuan</h2>
                        <div class="w-12 h-0.5 bg-primary/30 mt-2 group-hover:w-20 transition-all duration-300"></div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 rounded-lg bg-surface-container-low border border-outline-variant/50 hover:border-primary/30 transition-colors">
                            <div>
                                <p class="font-semibold text-on-surface">Peneliti Internal</p>
                                <p class="text-xs text-on-surface-variant">Dosen & Staf Kampus</p>
                            </div>
                            <p class="text-xl font-bold text-primary">Rp 250k</p>
                        </div>
                        <div class="flex justify-between items-center p-3 rounded-lg hover:bg-surface-container-low transition-colors">
                            <div>
                                <p class="font-semibold text-on-surface">Mahasiswa S1/S2</p>
                                <p class="text-xs text-on-surface-variant">Penelitian Mandiri</p>
                            </div>
                            <p class="text-xl font-bold text-emerald-600">Gratis</p>
                        </div>
                        <div class="flex justify-between items-center p-3 rounded-lg hover:bg-surface-container-low transition-colors">
                            <div>
                                <p class="font-semibold text-on-surface">Peneliti Eksternal</p>
                                <p class="text-xs text-on-surface-variant">Institusi Luar</p>
                            </div>
                            <p class="text-xl font-bold text-primary">Rp 750k</p>
                        </div>
                    </div>
                    <div class="mt-5 pt-4 border-t border-outline-variant">
                        <button class="w-full text-primary font-semibold text-sm hover:underline flex items-center justify-center gap-2 transition-all">
                            Lihat Rincian Pembayaran
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. FAQ Ringan -->
        <section class="bg-white rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">
            <div class="relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-tertiary/5 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2"></div>
                <div class="p-6 lg:p-8">
                    <div class="mb-4">
                        <div class="w-12 h-12 rounded-full bg-surface-container-low flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-tertiary text-3xl">help_center</span>
                        </div>
                        <h2 class="text-xl lg:text-2xl font-semibold text-primary">FAQ Ringan</h2>
                        <div class="w-12 h-0.5 bg-primary/30 mt-2 group-hover:w-20 transition-all duration-300"></div>
                    </div>
                    <div class="space-y-5">
                        <!-- FAQ Item 1 -->
                        <div x-data="{ open: false }" class="border-b border-outline-variant pb-3 last:border-0">
                            <button @click="open = !open" class="w-full flex justify-between items-center text-left group/faq">
                                <span class="font-semibold text-sm text-on-surface">Berapa lama proses review?</span>
                                <span class="material-symbols-outlined text-outline transition-transform" :class="open ? 'rotate-180' : ''">
                                    expand_more
                                </span>
                            </button>
                            <div x-show="open" x-collapse class="mt-2 text-sm text-on-surface-variant leading-relaxed">
                                Rata-rata memakan waktu 14-21 hari kerja tergantung kelengkapan dokumen dan jenis review (Exempted, Expedited, atau Full Board).
                            </div>
                        </div>
                        <!-- FAQ Item 2 -->
                        <div x-data="{ open: false }" class="border-b border-outline-variant pb-3 last:border-0">
                            <button @click="open = !open" class="w-full flex justify-between items-center text-left group/faq">
                                <span class="font-semibold text-sm text-on-surface">Apakah revisi diperbolehkan?</span>
                                <span class="material-symbols-outlined text-outline transition-transform" :class="open ? 'rotate-180' : ''">
                                    expand_more
                                </span>
                            </button>
                            <div x-show="open" x-collapse class="mt-2 text-sm text-on-surface-variant leading-relaxed">
                                Ya, peneliti dapat melakukan revisi sesuai catatan dari reviewer melalui dashboard ini. Status akan berubah menjadi "Revised" setelah revisi diunggah.
                            </div>
                        </div>
                        <!-- FAQ Item 3 -->
                        <div x-data="{ open: false }" class="border-b border-outline-variant pb-3 last:border-0">
                            <button @click="open = !open" class="w-full flex justify-between items-center text-left group/faq">
                                <span class="font-semibold text-sm text-on-surface">Bagaimana cara aktivasi akun?</span>
                                <span class="material-symbols-outlined text-outline transition-transform" :class="open ? 'rotate-180' : ''">
                                    expand_more
                                </span>
                            </button>
                            <div x-show="open" x-collapse class="mt-2 text-sm text-on-surface-variant leading-relaxed">
                                Setelah registrasi, akun Anda akan diaktivasi oleh pihak sekretariat. Anda akan menerima notifikasi email saat akun sudah aktif.
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 pt-4">
                        <button class="w-full py-2.5 rounded-lg border-2 border-primary text-primary font-semibold text-sm hover:bg-primary/5 transition-all duration-300 flex items-center justify-center gap-2">
                            Lihat Semua FAQ
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Pengumuman / Informasi Terkini -->
    <div class="bg-gradient-to-r from-primary/5 via-surface-container-low to-primary/5 rounded-xl border border-outline-variant p-6 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary">campaign</span>
                </div>
                <div>
                    <h3 class="font-semibold text-primary mb-1">Informasi Terkini</h3>
                    <p class="text-sm text-on-surface-variant">Pengajuan ethical clearance untuk periode Januari - Juni 2026 telah dibuka.</p>
                </div>
            </div>
            <span class="text-xs text-primary whitespace-nowrap bg-primary/10 px-3 py-1 rounded-full">Diperbarui: {{ date('d M Y') }}</span>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop" class="fixed bottom-8 right-8 w-12 h-12 rounded-full bg-primary text-on-primary shadow-lg hover:bg-primary-container transition-all duration-300 transform translate-y-16 opacity-0 flex items-center justify-center z-50 hover:scale-110 active:scale-95">
        <span class="material-symbols-outlined">arrow_upward</span>
    </button>
</div>

<!-- Alpine.js for FAQ Accordion -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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

<!-- Additional Styles for Animations -->
<style>
    @keyframes fade-in {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes slide-up {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fade-in 0.6s ease-out forwards;
    }
    
    .animate-slide-up {
        animation: slide-up 0.6s ease-out forwards;
        opacity: 0;
    }
    
    .animation-delay-200 {
        animation-delay: 0.2s;
    }
    
    .animation-delay-400 {
        animation-delay: 0.4s;
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 640px) {
        .space-y-8 {
            margin-bottom: 1rem;
        }
    }
    
    /* Smooth transitions for all interactive elements */
    button, a, .group-hover\:scale-110 {
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endsection