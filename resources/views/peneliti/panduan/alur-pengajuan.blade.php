@extends('layouts.dashboard')

@section('title', 'Alur Pengajuan Ethical Clearance')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 space-y-8">
    <!-- Header Section -->
    <div class="relative bg-gradient-to-r from-primary/5 via-surface-container-low to-primary/5 rounded-2xl p-6 lg:p-8 border border-outline-variant overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-primary/5 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary text-3xl">timeline</span>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-primary mb-2">Alur Pengajuan Ethical Clearance</h1>
                    <p class="text-on-surface-variant max-w-2xl">
                        Ikuti langkah-langkah berikut untuk mengajukan ethical clearance secara online.
                    </p>
                </div>
            </div>
            <div class="flex-shrink-0">
                <div class="bg-primary/10 rounded-full px-4 py-2 text-sm text-primary font-semibold">
                    Durasi: 14-21 hari kerja
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Stepper -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="p-6 lg:p-8">
            <!-- Desktop Timeline -->
            <div class="hidden md:block relative">
                <div class="absolute left-[10%] right-[10%] top-8 h-0.5 bg-outline-variant"></div>
                <div class="relative flex justify-between">
                    <!-- Step 1 -->
                    <div class="flex flex-col items-center text-center w-1/5 group">
                        <div class="relative z-10 w-16 h-16 rounded-full bg-surface-container-high border-4 border-primary flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-primary text-2xl">assignment</span>
                        </div>
                        <div class="mt-4">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary text-white text-xs font-bold mb-2">1</span>
                            <h3 class="font-semibold text-primary">Registrasi Akun</h3>
                            <p class="text-xs text-on-surface-variant mt-1 max-w-[150px]">Daftar dan tunggu aktivasi</p>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div class="flex flex-col items-center text-center w-1/5 group">
                        <div class="relative z-10 w-16 h-16 rounded-full bg-surface-container-high border-4 border-outline-variant flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-outline text-2xl">upload_file</span>
                        </div>
                        <div class="mt-4">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-surface-container-high text-outline text-xs font-bold mb-2">2</span>
                            <h3 class="font-semibold text-on-surface">Upload Proposal</h3>
                            <p class="text-xs text-on-surface-variant mt-1 max-w-[150px]">Isi dan upload dokumen</p>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div class="flex flex-col items-center text-center w-1/5 group">
                        <div class="relative z-10 w-16 h-16 rounded-full bg-surface-container-high border-4 border-outline-variant flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-outline text-2xl">rate_review</span>
                        </div>
                        <div class="mt-4">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-surface-container-high text-outline text-xs font-bold mb-2">3</span>
                            <h3 class="font-semibold text-on-surface">Verifikasi & Review</h3>
                            <p class="text-xs text-on-surface-variant mt-1 max-w-[150px]">Sekretariat dan reviewer</p>
                        </div>
                    </div>
                    <!-- Step 4 -->
                    <div class="flex flex-col items-center text-center w-1/5 group">
                        <div class="relative z-10 w-16 h-16 rounded-full bg-surface-container-high border-4 border-outline-variant flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-outline text-2xl">edit_note</span>
                        </div>
                        <div class="mt-4">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-surface-container-high text-outline text-xs font-bold mb-2">4</span>
                            <h3 class="font-semibold text-on-surface">Revisi (Jika Diperlukan)</h3>
                            <p class="text-xs text-on-surface-variant mt-1 max-w-[150px]">Perbaiki sesuai saran</p>
                        </div>
                    </div>
                    <!-- Step 5 -->
                    <div class="flex flex-col items-center text-center w-1/5 group">
                        <div class="relative z-10 w-16 h-16 rounded-full bg-surface-container-high border-4 border-outline-variant flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-outline text-2xl">verified</span>
                        </div>
                        <div class="mt-4">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-surface-container-high text-outline text-xs font-bold mb-2">5</span>
                            <h3 class="font-semibold text-on-surface">Selesai & Terbit</h3>
                            <p class="text-xs text-on-surface-variant mt-1 max-w-[150px]">Surat kelayakan etik</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Timeline (Vertical) -->
            <div class="md:hidden space-y-6">
                <div class="flex items-start gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-xl">assignment</span>
                        </div>
                        <div class="w-0.5 h-12 bg-outline-variant mt-2"></div>
                    </div>
                    <div class="flex-1 pb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-full">Langkah 1</span>
                        </div>
                        <h3 class="font-semibold text-primary">Registrasi Akun</h3>
                        <p class="text-sm text-on-surface-variant">Daftar dan tunggu aktivasi oleh sekretariat</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high border-2 border-outline-variant flex items-center justify-center">
                            <span class="material-symbols-outlined text-outline text-xl">upload_file</span>
                        </div>
                        <div class="w-0.5 h-12 bg-outline-variant mt-2"></div>
                    </div>
                    <div class="flex-1 pb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-on-surface-variant bg-surface-container-high px-2 py-0.5 rounded-full">Langkah 2</span>
                        </div>
                        <h3 class="font-semibold text-on-surface">Upload Proposal</h3>
                        <p class="text-sm text-on-surface-variant">Isi formulir dan upload dokumen pendukung</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high border-2 border-outline-variant flex items-center justify-center">
                            <span class="material-symbols-outlined text-outline text-xl">rate_review</span>
                        </div>
                        <div class="w-0.5 h-12 bg-outline-variant mt-2"></div>
                    </div>
                    <div class="flex-1 pb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-on-surface-variant bg-surface-container-high px-2 py-0.5 rounded-full">Langkah 3</span>
                        </div>
                        <h3 class="font-semibold text-on-surface">Verifikasi & Review</h3>
                        <p class="text-sm text-on-surface-variant">Sekretariat memverifikasi kelengkapan dan reviewer menilai</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high border-2 border-outline-variant flex items-center justify-center">
                            <span class="material-symbols-outlined text-outline text-xl">edit_note</span>
                        </div>
                        <div class="w-0.5 h-12 bg-outline-variant mt-2"></div>
                    </div>
                    <div class="flex-1 pb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-on-surface-variant bg-surface-container-high px-2 py-0.5 rounded-full">Langkah 4</span>
                        </div>
                        <h3 class="font-semibold text-on-surface">Revisi (Jika Diperlukan)</h3>
                        <p class="text-sm text-on-surface-variant">Perbaiki proposal sesuai feedback reviewer</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high border-2 border-outline-variant flex items-center justify-center">
                            <span class="material-symbols-outlined text-outline text-xl">verified</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-on-surface-variant bg-surface-container-high px-2 py-0.5 rounded-full">Langkah 5</span>
                        </div>
                        <h3 class="font-semibold text-on-surface">Selesai & Terbit</h3>
                        <p class="text-sm text-on-surface-variant">Surat kelayakan etik diterbitkan dan dapat diunduh</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Penjelasan Detail Setiap Tahap -->
    <div class="space-y-4">
        <h2 class="text-xl font-semibold text-primary flex items-center gap-2">
            <span class="material-symbols-outlined">menu_book</span>
            Penjelasan Detail Setiap Tahap
        </h2>
        
        <div class="grid grid-cols-1 gap-4">
            <!-- Tahap 1 -->
            <div class="bg-white rounded-xl border border-outline-variant overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="border-b border-outline-variant bg-surface-container-low px-6 py-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold">1</span>
                        <h3 class="font-semibold text-primary">Registrasi dan Aktivasi Akun</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-on-surface-variant text-sm leading-relaxed">
                                Peneliti mendaftarkan akun melalui aplikasi web yang telah disediakan Komisi Etik Penelitian. 
                                Setelah mendaftar, sekretariat akan melakukan aktivasi akun dalam waktu maksimal 2x24 jam.
                            </p>
                            <div class="mt-4 bg-surface-container-low rounded-lg p-3">
                                <p class="text-xs font-semibold text-primary mb-2">📋 Yang perlu disiapkan:</p>
                                <ul class="text-xs text-on-surface-variant space-y-1 list-disc list-inside">
                                    <li>Email aktif</li>
                                    <li>Nama lengkap sesuai identitas</li>
                                    <li>Nomor telepon/HP</li>
                                    <li>Alamat dan institusi</li>
                                </ul>
                            </div>
                        </div>
                        <div class="flex items-center justify-center bg-surface-container-low rounded-lg p-4">
                            <div class="text-center">
                                <span class="material-symbols-outlined text-primary text-5xl">mark_email_unread</span>
                                <p class="text-xs text-on-surface-variant mt-2">Notifikasi aktivasi akan dikirim ke email</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tahap 2 -->
            <div class="bg-white rounded-xl border border-outline-variant overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="border-b border-outline-variant bg-surface-container-low px-6 py-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-surface-container-high text-outline flex items-center justify-center text-sm font-bold border border-outline-variant">2</span>
                        <h3 class="font-semibold text-on-surface">Mengajukan Ethical Clearance</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-on-surface-variant text-sm leading-relaxed">
                                Peneliti mengunduh template formulir, mengisi sesuai ketentuan, dan mengupload dokumen lengkap 
                                melalui aplikasi web. Status awal pengajuan adalah <span class="font-semibold text-primary">"New Proposal"</span>.
                            </p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">New Proposal</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-surface-container-high text-on-surface-variant">Menunggu Verifikasi</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-center bg-surface-container-low rounded-lg p-4">
                            <div class="text-center">
                                <span class="material-symbols-outlined text-primary text-5xl">cloud_upload</span>
                                <p class="text-xs text-on-surface-variant mt-2">Upload dokumen maksimal 10MB per file</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tahap 3 -->
            <div class="bg-white rounded-xl border border-outline-variant overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="border-b border-outline-variant bg-surface-container-low px-6 py-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-surface-container-high text-outline flex items-center justify-center text-sm font-bold border border-outline-variant">3</span>
                        <h3 class="font-semibold text-on-surface">Pemeriksaan dan Proses Review</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-surface-container-low rounded-lg p-4 text-center">
                            <span class="material-symbols-outlined text-primary text-3xl">checklist</span>
                            <p class="font-semibold text-sm mt-2">Verifikasi Kelengkapan</p>
                            <p class="text-xs text-on-surface-variant mt-1">Sekretariat memeriksa kelengkapan dokumen</p>
                        </div>
                        <div class="bg-surface-container-low rounded-lg p-4 text-center">
                            <span class="material-symbols-outlined text-primary text-3xl">real_estate_agent</span>
                            <p class="font-semibold text-sm mt-2">Penentuan Jenis Review</p>
                            <p class="text-xs text-on-surface-variant mt-1">Exempted / Expedited / Full Board</p>
                        </div>
                        <div class="bg-surface-container-low rounded-lg p-4 text-center">
                            <span class="material-symbols-outlined text-primary text-3xl">rate_review</span>
                            <p class="font-semibold text-sm mt-2">Telaah Reviewer</p>
                            <p class="text-xs text-on-surface-variant mt-1">Minimal 3 reviewer untuk Expedited</p>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-surface-container-low rounded-full">
                            <span class="material-symbols-outlined text-secondary text-sm">schedule</span>
                            <span class="text-xs text-on-surface-variant">Status berubah menjadi <span class="font-semibold text-secondary">"On Review"</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tahap 4 - Status Kemungkinan -->
            <div class="bg-white rounded-xl border border-outline-variant overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="border-b border-outline-variant bg-surface-container-low px-6 py-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-surface-container-high text-outline flex items-center justify-center text-sm font-bold border border-outline-variant">4</span>
                        <h3 class="font-semibold text-on-surface">Hasil Telaah & Kemungkinan Status</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 rounded-xl border-l-4 border-emerald-500 bg-emerald-50/30">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                                <span class="font-semibold text-emerald-700">Approved</span>
                            </div>
                            <p class="text-xs text-on-surface-variant">Lolos etik, langsung mendapat Surat Kelaikan Etik</p>
                        </div>
                        <div class="p-4 rounded-xl border-l-4 border-blue-500 bg-blue-50/30">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-blue-600">rate_review</span>
                                <span class="font-semibold text-blue-700">Approved with Recommendation</span>
                            </div>
                            <p class="text-xs text-on-surface-variant">Disetujui dengan catatan perbaikan minor</p>
                        </div>
                        <div class="p-4 rounded-xl border-l-4 border-amber-500 bg-amber-50/30">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-amber-600">autorenew</span>
                                <span class="font-semibold text-amber-700">Resubmission</span>
                            </div>
                            <p class="text-xs text-on-surface-variant">Perlu revisi dan ditelaah ulang</p>
                        </div>
                        <div class="p-4 rounded-xl border-l-4 border-red-500 bg-red-50/30">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-red-600">cancel</span>
                                <span class="font-semibold text-red-700">Disapproved</span>
                            </div>
                            <p class="text-xs text-on-surface-variant">Tidak layak etik, penelitian tidak dapat dilanjutkan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tahap 5 -->
            <div class="bg-white rounded-xl border border-outline-variant overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="border-b border-outline-variant bg-surface-container-low px-6 py-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-surface-container-high text-outline flex items-center justify-center text-sm font-bold border border-outline-variant">5</span>
                        <h3 class="font-semibold text-on-surface">Penerbitan Surat Kelaikan Etik</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-on-surface-variant text-sm leading-relaxed">
                                Setelah dinyatakan <span class="font-semibold text-emerald-600">Approved</span>, surat kelaikan etik akan diterbitkan 
                                dan dapat diunduh melalui dashboard peneliti. Surat berlaku selama 1 tahun.
                            </p>
                            <div class="mt-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">download</span>
                                <span class="text-sm text-on-surface-variant">Dokumen tersedia dalam format PDF</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-center gap-4">
                            <div class="text-center">
                                <span class="material-symbols-outlined text-primary text-4xl">description</span>
                                <p class="text-xs text-on-surface-variant mt-1">Surat Kelaikan Etik</p>
                            </div>
                            <span class="material-symbols-outlined text-outline">arrow_forward</span>
                            <div class="text-center">
                                <span class="material-symbols-outlined text-primary text-4xl">assignment_add</span>
                                <p class="text-xs text-on-surface-variant mt-1">Dapat digunakan untuk penelitian</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar Example -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6">
        <h2 class="text-lg font-semibold text-primary flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined">track_changes</span>
            Contoh Tracking Status Proposal
        </h2>
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-on-surface">Status Saat Ini: <span class="text-primary">On Review</span></span>
                <span class="text-xs text-on-surface-variant">Update: 2 hari yang lalu</span>
            </div>
            <div class="w-full bg-surface-container-high rounded-full h-3">
                <div class="bg-primary h-3 rounded-full transition-all duration-500" style="width: 40%"></div>
            </div>
            <div class="flex justify-between mt-3 text-xs text-on-surface-variant">
                <div class="text-center flex-1">
                    <span class="material-symbols-outlined text-primary text-sm block mx-auto">check_circle</span>
                    <span>New</span>
                </div>
                <div class="text-center flex-1">
                    <span class="material-symbols-outlined text-primary text-sm block mx-auto">radio_button_checked</span>
                    <span>Review</span>
                </div>
                <div class="text-center flex-1">
                    <span class="material-symbols-outlined text-outline text-sm block mx-auto">radio_button_unchecked</span>
                    <span>Approved</span>
                </div>
            </div>
        </div>
        <div class="text-center mt-6 pt-4 border-t border-outline-variant">
            <p class="text-sm text-on-surface-variant">
                Status proposal akan diperbarui secara otomatis dan notifikasi akan dikirim ke email Anda.
            </p>
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
</div>
@endsection