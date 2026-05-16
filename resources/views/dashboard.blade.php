@extends('layouts.dashboard')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 space-y-16 lg:space-y-24">
    
    <!-- Hero Section dengan Background Image -->
    <section class="relative rounded-3xl overflow-hidden bg-primary min-h-[500px] lg:min-h-[600px] flex items-center">
        <!-- Background Image dengan Overlay -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/70 to-primary/40 z-10"></div>
            <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=1600&h=800&fit=crop" alt="Research Background" class="w-full h-full object-cover">
        </div>
        
        <div class="relative z-20 max-w-4xl mx-auto px-6 lg:px-12 py-16 text-center lg:text-left lg:ml-20">
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md rounded-full px-4 py-2 mb-6 text-sm text-white font-medium">
                <span class="material-symbols-outlined text-sm">verified</span>
                <span>Terakreditasi Kemenristekdikti</span>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                Ethical Clearance <br>Management System
            </h1>
            <p class="text-lg lg:text-xl text-white/90 mb-8 max-w-2xl mx-auto lg:mx-0">
                Layanan terpadu untuk pengajuan, peninjauan, dan manajemen persetujuan etik penelitian secara transparan dan akuntabel.
            </p>
            <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                @auth
                    @if(auth()->user()->hasRole('peneliti') && auth()->user()->status === 'active')
                        <button onclick="window.location.href='{{ route('pengajuan.upload-proposal') }}'" class="group bg-white text-primary px-8 py-3 rounded-full font-semibold text-base hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
                            Mulai Pengajuan Baru
                            <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </button>
                    @else
                        <button onclick="window.location.href='{{ route('login') }}'" class="group bg-white text-primary px-8 py-3 rounded-full font-semibold text-base hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
                            Login untuk Mulai Pengajuan
                            <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">login</span>
                        </button>
                    @endif
                    <button class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold text-base hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-1">
                        Pelajari Lebih Lanjut
                    </button>
                @else
                    <button onclick="window.location.href='{{ route('login') }}'" class="group bg-white text-primary px-8 py-3 rounded-full font-semibold text-base hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
                        Mulai Pengajuan Baru
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                    <button class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold text-base hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-1">
                        Pelajari Lebih Lanjut
                    </button>
                @endauth
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
    </section>

    <!-- 1. Informasi Umum -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
        <div class="space-y-4">
        <h2 class="text-3xl lg:text-4xl font-bold text-primary leading-tight">
            <br>
            <a href="{{ route('home') }}" class="text-5xl lg:text-6xl font-bold tracking-tighter bg-gradient-to-r from-primary bg-clip-text hover:opacity-80 transition-opacity">
                Ethical Clearance ?
            </a>
        </h2>
            <p class="text-on-surface-variant leading-relaxed">
                Persetujuan etik merupakan syarat mutlak yang harus dipenuhi sebelum penelitian dilaksanakan, terutama bagi penelitian yang melibatkan manusia sebagai subjek.
            </p>
        </div>
        
        <div class="bg-white rounded-2xl border border-outline-variant shadow-lg p-6 lg:p-8 hover:shadow-xl transition-all duration-300">
            <div class="space-y-4">
                <p class="text-on-surface-variant leading-relaxed">
                    <span class="font-semibold text-primary">Ethical Clearance (Persetujuan Etik)</span> adalah keterangan tertulis yang diberikan oleh Komisi Etik Penelitian untuk subjek manusia maupun hewan yang menyatakan bahwa suatu proposal penelitian layak dilaksanakan setelah memenuhi persyaratan tertentu.
                </p>
                <p class="text-on-surface-variant leading-relaxed">
                    Komisi Etik Penelitian bertugas untuk melindungi hak-hak, kesejahteraan, dan martabat partisipan penelitian.
                </p>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4">
                    <div class="bg-surface-container-low rounded-xl p-4 text-center hover:bg-primary/5 transition-colors">
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-outlined text-primary">shield</span>
                        </div>
                        <h3 class="font-semibold text-sm text-primary">Perlindungan Partisipan</h3>
                        <p class="text-xs text-on-surface-variant mt-1">Memastikan partisipan terlindungi</p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-4 text-center hover:bg-primary/5 transition-colors">
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-outlined text-primary">gavel</span>
                        </div>
                        <h3 class="font-semibold text-sm text-primary">Kepatuhan Hukum</h3>
                        <p class="text-xs text-on-surface-variant mt-1">Memenuhi regulasi</p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-4 text-center hover:bg-primary/5 transition-colors">
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-outlined text-primary">verified</span>
                        </div>
                        <h3 class="font-semibold text-sm text-primary">Legitimasi Penelitian</h3>
                        <p class="text-xs text-on-surface-variant mt-1">Meningkatkan kredibilitas</p>
                    </div>
                </div>
                
                <div class="pt-3">
                    <a href="{{ route('panduan.syarat-pendaftaran') }}" class="inline-flex items-center gap-2 text-primary font-semibold text-sm hover:gap-3 transition-all duration-300 group">
                        Baca lengkap syarat pendaftaran
                        <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Biaya Pengajuan - Card Grid yang Menarik -->
    <section class="space-y-8">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl lg:text-4xl font-bold text-primary">Biaya Pengajuan</h2>
            <div class="w-20 h-1 bg-primary rounded-full mx-auto mt-4"></div>
            <p class="text-on-surface-variant mt-4">Rincian biaya berdasarkan kategori peneliti</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 - Internal -->
            <div class="group relative bg-white rounded-2xl border border-outline-variant shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-all"></div>
                <div class="p-6 text-center">
                    <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined text-primary text-3xl">school</span>
                    </div>
                    <h3 class="text-xl font-bold text-primary">Peneliti Internal</h3>
                    <p class="text-sm text-on-surface-variant mb-3">Dosen & Staf Kampus</p>
                    <p class="text-4xl font-bold text-primary mb-4">Rp 250k</p>
                    <div class="border-t border-outline-variant pt-4 mt-2">
                        <ul class="text-sm text-on-surface-variant space-y-2 text-left">
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-sm">check_circle</span> Dosen tetap universitas</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-sm">check_circle</span> Staf peneliti institusi</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-sm">check_circle</span> Peneliti pascasarjana</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Card 2 - Mahasiswa (Gratis) -->
            <div class="group relative bg-gradient-to-br from-emerald-50 to-white rounded-2xl border-2 border-emerald-200 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-visible">
                <div class="absolute -top-2 -right-5 bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full rotate-12 z-10">Hemat Biaya!</div>
                <div class="p-6 text-center">
                    <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined text-emerald-600 text-3xl">account_balance</span>
                    </div>
                    <h3 class="text-xl font-bold text-primary">Mahasiswa S1/S2</h3>
                    <p class="text-sm text-on-surface-variant mb-3">Penelitian Mandiri</p>
                    <p class="text-4xl font-bold text-emerald-600 mb-4">Gratis</p>
                    <div class="border-t border-emerald-100 pt-4 mt-2">
                        <ul class="text-sm text-on-surface-variant space-y-2 text-left">
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span> Skripsi S1 (mandiri)</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span> Tesis S2 (mandiri)</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span> Penelitian tugas akhir</li>
                        </ul>
                    </div>
                    <div class="mt-3 text-xs text-emerald-600 bg-emerald-50 p-2 rounded-lg">*Khusus penelitian yang didanai mandiri</div>
                </div>
            </div>
            <!-- Card 3 - Eksternal -->
            <div class="group relative bg-white rounded-2xl border border-outline-variant shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-all"></div>
                <div class="p-6 text-center">
                    <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined text-primary text-3xl">business</span>
                    </div>
                    <h3 class="text-xl font-bold text-primary">Peneliti Eksternal</h3>
                    <p class="text-sm text-on-surface-variant mb-3">Institusi Luar</p>
                    <p class="text-4xl font-bold text-primary mb-4">Rp 750k</p>
                    <div class="border-t border-outline-variant pt-4 mt-2">
                        <ul class="text-sm text-on-surface-variant space-y-2 text-left">
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-sm">check_circle</span> Peneliti dari institusi luar</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-sm">check_circle</span> Penelitian kolaborasi</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-sm">check_circle</span> Penelitian berbadan hukum</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-surface-container-low rounded-xl p-4 text-center">
            <p class="text-sm text-on-surface-variant">! Biaya dapat berubah sesuai kebijakan institusi. Informasi lengkap pembayaran dapat dilihat pada menu profil.</p>
        </div>
    </section>

    <!-- 3. FAQ - Modern Accordion dengan Icon -->
    <section class="space-y-8">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl lg:text-4xl font-bold text-primary">Frequently Asked Questions</h2>
            <div class="w-20 h-1 bg-primary rounded-full mx-auto mt-4"></div>
            <p class="text-on-surface-variant mt-4">Pertanyaan yang sering diajukan tentang ethical clearance</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- FAQ Column 1 -->
            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div x-data="{ open: false }" class="bg-white rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all duration-300">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-5 text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-primary text-sm">schedule</span>
                            </div>
                            <span class="font-semibold text-on-surface">Berapa lama proses review?</span>
                        </div>
                        <span class="material-symbols-outlined text-outline transition-transform duration-300" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="px-5 pb-5 text-sm text-on-surface-variant leading-relaxed border-t border-outline-variant pt-4">
                        <p>Rata-rata proses review memakan waktu <span class="font-semibold text-primary">14-21 hari kerja</span> tergantung pada:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1 ml-2">
                            <li>Kelengkapan dokumen yang diunggah</li>
                            <li>Jenis review yang ditentukan (Exempted, Expedited, atau Full Board)</li>
                            <li>Ketersediaan reviewer yang sesuai bidang keahlian</li>
                        </ul>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div x-data="{ open: false }" class="bg-white rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all duration-300">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-5 text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-primary text-sm">description</span>
                            </div>
                            <span class="font-semibold text-on-surface">Apa saja dokumen yang harus disiapkan?</span>
                        </div>
                        <span class="material-symbols-outlined text-outline transition-transform duration-300" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="px-5 pb-5 text-sm text-on-surface-variant leading-relaxed border-t border-outline-variant pt-4">
                        <p>Dokumen wajib yang harus diunggah: Surat pengantar institusi, proposal penelitian, formulir penjelasan partisipan, ICF, alat pengumpulan data, daftar tim peneliti, dan CV peneliti utama.</p>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div x-data="{ open: false }" class="bg-white rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all duration-300">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-5 text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-primary text-sm">edit_note</span>
                            </div>
                            <span class="font-semibold text-on-surface">Apakah revisi proposal diperbolehkan?</span>
                        </div>
                        <span class="material-symbols-outlined text-outline transition-transform duration-300" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="px-5 pb-5 text-sm text-on-surface-variant leading-relaxed border-t border-outline-variant pt-4">
                        <p>Ya, revisi proposal diperbolehkan. Ada dua skenario: <span class="font-semibold text-primary">Approved with Recommendation</span> (perbaikan minor, tetap dapat surat) dan <span class="font-semibold text-amber-700">Resubmission</span> (perbaikan substantif, perlu ditelaah ulang).</p>
                    </div>
                </div>
            </div>

            <!-- FAQ Column 2 -->
            <div class="space-y-4">
                <!-- FAQ 4 -->
                <div x-data="{ open: false }" class="bg-white rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all duration-300">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-5 text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-primary text-sm">person_add</span>
                            </div>
                            <span class="font-semibold text-on-surface">Bagaimana cara aktivasi akun?</span>
                        </div>
                        <span class="material-symbols-outlined text-outline transition-transform duration-300" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="px-5 pb-5 text-sm text-on-surface-variant leading-relaxed border-t border-outline-variant pt-4">
                        <p>Setelah registrasi, sekretariat akan melakukan verifikasi dan aktivasi akun dalam maksimal 2x24 jam. Peneliti akan menerima notifikasi email saat akun sudah aktif.</p>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div x-data="{ open: false }" class="bg-white rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all duration-300">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-5 text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-primary text-sm">verified</span>
                            </div>
                            <span class="font-semibold text-on-surface">Berapa lama masa berlaku surat?</span>
                        </div>
                        <span class="material-symbols-outlined text-outline transition-transform duration-300" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="px-5 pb-5 text-sm text-on-surface-variant leading-relaxed border-t border-outline-variant pt-4">
                        <p>Surat Ethical Clearance berlaku selama <span class="font-semibold text-primary">1 tahun</span> sejak tanggal diterbitkan. Perpanjangan dapat diajukan paling lambat 1 bulan sebelum masa berlaku habis.</p>
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div x-data="{ open: false }" class="bg-white rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all duration-300">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-5 text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-primary text-sm">category</span>
                            </div>
                            <span class="font-semibold text-on-surface">Apa perbedaan jenis review?</span>
                        </div>
                        <span class="material-symbols-outlined text-outline transition-transform duration-300" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="px-5 pb-5 border-t border-outline-variant pt-4">
                        <div class="grid grid-cols-1 gap-3 text-sm">
                            <div class="bg-emerald-50 rounded-lg p-3"><span class="font-semibold text-emerald-700">Exempted Review:</span> Risiko minimal, langsung terbit</div>
                            <div class="bg-blue-50 rounded-lg p-3"><span class="font-semibold text-blue-700">Expedited Review:</span> 3 reviewer, proses 7-14 hari</div>
                            <div class="bg-amber-50 rounded-lg p-3"><span class="font-semibold text-amber-700">Full Board Review:</span> Rapat panel, 14-21 hari</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Informasi Terkini - Banner dengan Background Image -->

<section class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-gray-700 to-gray-900 min-h-[200px] flex items-center">        <div class="absolute inset-0 opacity-5">
            <img src="https://images.unsplash.com/photo-1573164713988-8665fc963095?w=1600&h=500&fit=crop" alt="Background" class="w-full h-full object-cover">
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-6 px-8 py-8 w-full">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <span class="material-symbols-outlined text-white text-3xl">campaign</span>
                </div>
                <div class="text-white">
                    <h3 class="font-bold text-xl mb-1">Informasi Terkini</h3>
                    <p class="text-white/90">Pengajuan ethical clearance untuk periode Januari - Juni 2026 telah dibuka. Batas akhir 30 Juni 2026.</p>
                    <p class="text-sm text-white/80 mt-2 flex items-center gap-1">! Segera ajukan proposal Anda sebelum batas waktu berakhir!</p>
                </div>
            </div>
            <div class="bg-white/20 backdrop-blur-md rounded-full px-4 py-2 text-sm text-white whitespace-nowrap">
                Diperbarui: {{ date('d M Y') }}
            </div>
        </div>
    </section>

    <!-- Call to Action Bottom -->
    <section class="text-center py-8">
        <div class="inline-flex flex-col sm:flex-row gap-4">
            @auth
                @if(auth()->user()->hasRole('peneliti') && auth()->user()->status === 'active')
                    <button onclick="window.location.href='{{ route('pengajuan.upload-proposal') }}'" class="group px-8 py-3 bg-primary text-on-primary rounded-xl font-semibold hover:bg-primary-container transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center gap-2">
                        Mulai Pengajuan Sekarang
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                    <button onclick="window.location.href='{{ route('pengajuan.riwayat-pengajuan') }}'" class="px-8 py-3 border-2 border-primary text-primary rounded-xl font-semibold hover:bg-primary/5 transition-all duration-300 transform hover:-translate-y-1">
                        Lihat Riwayat Pengajuan
                    </button>
                @elseif(auth()->user()->status === 'pending')
                    <div class="px-8 py-3 bg-amber-100 text-amber-700 rounded-xl font-semibold flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-600">hourglass_empty</span>
                        Akun Anda sedang menunggu aktivasi oleh sekretariat
                    </div>
                @endif
            @else
                <button onclick="window.location.href='{{ route('login') }}'" class="group px-8 py-3 bg-primary text-on-primary rounded-xl font-semibold hover:bg-primary-container transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
                    Login untuk Mengajukan
                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">login</span>
                </button>
                <button onclick="window.location.href='{{ route('register') }}'" class="px-8 py-3 border-2 border-primary text-primary rounded-xl font-semibold hover:bg-primary/5 transition-all duration-300">
                    Daftar Akun Baru
                </button>
            @endauth
        </div>
    </section>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop" class="fixed bottom-8 right-8 w-12 h-12 rounded-full bg-primary text-on-primary shadow-lg hover:bg-primary-container transition-all duration-300 transform translate-y-16 opacity-0 flex items-center justify-center z-50 hover:scale-110 active:scale-95">
        <span class="material-symbols-outlined">arrow_upward</span>
    </button>
</div>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>

<style>
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fade-in 0.6s ease-out forwards;
    }
    
    .animate-slide-up {
        animation: slide-up 0.6s ease-out forwards;
        opacity: 0;
    }
    
    .animation-delay-200 { animation-delay: 0.2s; }
    .animation-delay-400 { animation-delay: 0.4s; }
    
    button, a, .group-hover\:scale-110 {
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endsection