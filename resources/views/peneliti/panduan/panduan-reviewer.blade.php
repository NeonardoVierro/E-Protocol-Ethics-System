@extends('layouts.dashboard')

@section('title', 'Panduan Reviewer')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 space-y-8">
    <!-- Header Section -->
    <div class="relative bg-gradient-to-r from-primary/5 via-surface-container-low to-primary/5 rounded-2xl p-6 lg:p-8 border border-outline-variant overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-primary/5 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary text-3xl">rate_review</span>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-primary mb-2">Panduan Reviewer</h1>
                    <p class="text-on-surface-variant max-w-2xl">
                        Memahami proses penilaian ethical clearance oleh reviewer Komisi Etik Penelitian.
                    </p>
                </div>
            </div>
            <div class="flex-shrink-0">
                <div class="bg-primary/10 rounded-full px-4 py-2 text-sm text-primary font-semibold flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">groups</span>
                    Minimal 3 Reviewer
                </div>
            </div>
        </div>
    </div>

    <!-- Reviewer Information Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-outline-variant p-6 text-center hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-primary text-3xl">school</span>
            </div>
            <h3 class="font-semibold text-primary mb-2">Siapa Reviewer?</h3>
            <p class="text-sm text-on-surface-variant">
                Reviewer adalah pakar di bidangnya yang ditunjuk oleh Komisi Etik Penelitian untuk menilai kelayakan etik proposal.
            </p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-6 text-center hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-primary text-3xl">balance</span>
            </div>
            <h3 class="font-semibold text-primary mb-2">Prinsip Penilaian</h3>
            <p class="text-sm text-on-surface-variant">
                Objektif, independen, dan berdasarkan standar etik penelitian yang berlaku.
            </p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-6 text-center hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
            <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-primary text-3xl">verified</span>
            </div>
            <h3 class="font-semibold text-primary mb-2">Kualifikasi</h3>
            <p class="text-sm text-on-surface-variant">
                Memiliki kompetensi di bidang penelitian dan memahami prinsip etik.
            </p>
        </div>
    </div>

    <!-- Alur Review dari Perspektif Reviewer -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="border-b border-outline-variant bg-surface-container-low px-6 py-4">
            <h2 class="text-lg font-semibold text-primary flex items-center gap-2">
                <span class="material-symbols-outlined">timeline</span>
                Alur Proses Review (Perspektif Reviewer)
            </h2>
        </div>
        <div class="p-6">
            <!-- Timeline Steps -->
            <div class="space-y-6">
                <!-- Step 1 -->
                <div class="flex flex-col md:flex-row gap-4 items-start">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center font-bold">1</div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-primary text-lg">Menerima Notifikasi Penugasan</h3>
                        <p class="text-on-surface-variant mt-1">
                            Reviewer akan menerima notifikasi email ketika ditugaskan untuk menelaah proposal oleh sekretariat.
                        </p>
                        <div class="mt-3 bg-surface-container-low rounded-lg p-3 inline-block">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-sm">mark_email_read</span>
                                <span class="text-sm text-on-surface-variant">Email notifikasi berisi link menuju dashboard reviewer</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex flex-col md:flex-row gap-4 items-start">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-surface-container-high border-2 border-outline-variant text-outline flex items-center justify-center font-bold">2</div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-on-surface text-lg">Mengakses Proposal</h3>
                        <p class="text-on-surface-variant mt-1">
                            Reviewer login ke sistem dan mengakses proposal yang ditugaskan melalui menu "Proposal Masuk".
                        </p>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="bg-surface-container-low rounded-lg p-3">
                                <span class="material-symbols-outlined text-primary text-sm">description</span>
                                <p class="text-xs text-on-surface-variant mt-1">Melihat dokumen proposal lengkap</p>
                            </div>
                            <div class="bg-surface-container-low rounded-lg p-3">
                                <span class="material-symbols-outlined text-primary text-sm">download</span>
                                <p class="text-xs text-on-surface-variant mt-1">Mengunduh file pendukung</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex flex-col md:flex-row gap-4 items-start">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-surface-container-high border-2 border-outline-variant text-outline flex items-center justify-center font-bold">3</div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-on-surface text-lg">Melakukan Penilaian</h3>
                        <p class="text-on-surface-variant mt-1">
                            Reviewer menilai proposal berdasarkan aspek-aspek etik penelitian.
                        </p>
                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border border-outline-variant rounded-lg p-3">
                                <p class="font-semibold text-sm text-primary">Aspek yang Dinilai:</p>
                                <ul class="mt-2 space-y-1 text-xs text-on-surface-variant">
                                    <li class="flex items-start gap-2">• Kelayakan metodologi penelitian</li>
                                    <li class="flex items-start gap-2">• Perlindungan terhadap partisipan</li>
                                    <li class="flex items-start gap-2">• Informed consent process</li>
                                    <li class="flex items-start gap-2">• Manfaat vs risiko penelitian</li>
                                    <li class="flex items-start gap-2">• Kerahasiaan data partisipan</li>
                                </ul>
                            </div>
                            <div class="border border-outline-variant rounded-lg p-3">
                                <p class="font-semibold text-sm text-primary">Dokumen yang Ditelaah:</p>
                                <ul class="mt-2 space-y-1 text-xs text-on-surface-variant">
                                    <li class="flex items-start gap-2">• Proposal/protokol penelitian</li>
                                    <li class="flex items-start gap-2">• Informed Consent Form (ICF)</li>
                                    <li class="flex items-start gap-2">• Alat pengumpulan data</li>
                                    <li class="flex items-start gap-2">• Formulir penjelasan partisipan</li>
                                    <li class="flex items-start gap-2">• CV dan daftar peneliti</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex flex-col md:flex-row gap-4 items-start">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-surface-container-high border-2 border-outline-variant text-outline flex items-center justify-center font-bold">4</div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-on-surface text-lg">Memberikan Feedback & Rekomendasi</h3>
                        <p class="text-on-surface-variant mt-1">
                            Reviewer mengisi formulir feedback dan memberikan rekomendasi kelayakan.
                        </p>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="bg-emerald-50 rounded-lg p-3 text-center">
                                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                                <p class="font-semibold text-sm text-emerald-700">Approved</p>
                                <p class="text-xs text-emerald-600 mt-1">Layak Etik</p>
                            </div>
                            <div class="bg-amber-50 rounded-lg p-3 text-center">
                                <span class="material-symbols-outlined text-amber-600">edit_note</span>
                                <p class="font-semibold text-sm text-amber-700">Revision</p>
                                <p class="text-xs text-amber-600 mt-1">Perlu Revisi</p>
                            </div>
                            <div class="bg-red-50 rounded-lg p-3 text-center">
                                <span class="material-symbols-outlined text-red-600">cancel</span>
                                <p class="font-semibold text-sm text-red-700">Rejected</p>
                                <p class="text-xs text-red-600 mt-1">Tidak Layak</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="flex flex-col md:flex-row gap-4 items-start">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-surface-container-high border-2 border-outline-variant text-outline flex items-center justify-center font-bold">5</div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-on-surface text-lg">Menyelesaikan Review</h3>
                        <p class="text-on-surface-variant mt-1">
                            Reviewer mengirimkan hasil review melalui sistem. Status review berubah menjadi "Completed".
                        </p>
                        <div class="mt-3 bg-surface-container-low rounded-lg p-3">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-sm">schedule</span>
                                <span class="text-sm text-on-surface-variant">Batas waktu review: 7-14 hari kerja sejak ditugaskan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jenis Review -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="border-b border-outline-variant bg-surface-container-low px-6 py-4">
            <h2 class="text-lg font-semibold text-primary flex items-center gap-2">
                <span class="material-symbols-outlined">category</span>
                Jenis Review Proposal
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border border-outline-variant rounded-xl p-5 hover:border-primary/30 transition-all">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-emerald-600">checklist</span>
                    </div>
                    <h3 class="font-semibold text-primary text-lg">Exempted Review</h3>
                    <p class="text-xs text-on-surface-variant mt-1 mb-3">Dibebaskan dari Telaah</p>
                    <p class="text-sm text-on-surface-variant">
                        Proposal dengan risiko minimal dapat langsung diberi surat kelaikan etik tanpa melalui proses telaah reviewer.
                    </p>
                    <div class="mt-3 pt-3 border-t border-outline-variant">
                        <span class="text-xs text-primary font-semibold">Contoh:</span>
                        <p class="text-xs text-on-surface-variant mt-1">Penelitian survei anonim tanpa intervensi</p>
                    </div>
                </div>

                <div class="border border-outline-variant rounded-xl p-5 hover:border-primary/30 transition-all">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-blue-600">speed</span>
                    </div>
                    <h3 class="font-semibold text-primary text-lg">Expedited Review</h3>
                    <p class="text-xs text-on-surface-variant mt-1 mb-3">Telaah Cepat</p>
                    <p class="text-sm text-on-surface-variant">
                        Ditelaah oleh minimal 3 orang reviewer yang ditunjuk oleh sekretariat komisi etik.
                    </p>
                    <div class="mt-3 pt-3 border-t border-outline-variant">
                        <span class="text-xs text-primary font-semibold">Contoh:</span>
                        <p class="text-xs text-on-surface-variant mt-1">Penelitian risiko rendah, minimal intervensi</p>
                    </div>
                </div>

                <div class="border border-outline-variant rounded-xl p-5 hover:border-primary/30 transition-all">
                    <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-amber-600">groups</span>
                    </div>
                    <h3 class="font-semibold text-primary text-lg">Full Board Review</h3>
                    <p class="text-xs text-on-surface-variant mt-1 mb-3">Telaah Panel Penuh</p>
                    <p class="text-sm text-on-surface-variant">
                        Proposal didiskusikan dalam rapat panel Komisi Etik untuk pengambilan keputusan.
                    </p>
                    <div class="mt-3 pt-3 border-t border-outline-variant">
                        <span class="text-xs text-primary font-semibold">Contoh:</span>
                        <p class="text-xs text-on-surface-variant mt-1">Penelitian risiko tinggi, populasi rentan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Etika Reviewer -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="border-b border-outline-variant bg-surface-container-low px-6 py-4">
            <h2 class="text-lg font-semibold text-primary flex items-center gap-2">
                <span class="material-symbols-outlined">gavel</span>
                Kode Etik Reviewer
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3 p-3 bg-surface-container-low rounded-lg">
                    <span class="material-symbols-outlined text-primary">lock</span>
                    <div>
                        <p class="font-semibold text-sm">Kerahasiaan</p>
                        <p class="text-xs text-on-surface-variant">Reviewer wajib menjaga kerahasiaan proposal dan data penelitian</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-surface-container-low rounded-lg">
                    <span class="material-symbols-outlined text-primary">balance</span>
                    <div>
                        <p class="font-semibold text-sm">Objektivitas</p>
                        <p class="text-xs text-on-surface-variant">Penilaian harus objektif dan bebas dari konflik kepentingan</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-surface-container-low rounded-lg">
                    <span class="material-symbols-outlined text-primary">schedule</span>
                    <div>
                        <p class="font-semibold text-sm">Ketepatan Waktu</p>
                        <p class="text-xs text-on-surface-variant">Review harus diselesaikan sesuai batas waktu yang ditentukan</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-surface-container-low rounded-lg">
                    <span class="material-symbols-outlined text-primary">comment</span>
                    <div>
                        <p class="font-semibold text-sm">Feedback Konstruktif</p>
                        <p class="text-xs text-on-surface-variant">Memberikan masukan yang membangun dan jelas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hal yang Dinilai Reviewer -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="border-b border-outline-variant bg-surface-container-low px-6 py-4">
            <h2 class="text-lg font-semibold text-primary flex items-center gap-2">
                <span class="material-symbols-outlined">quiz</span>
                Hal yang Dinilai oleh Reviewer
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border border-outline-variant rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-primary">psychology</span>
                        <span class="font-semibold text-primary">Aspek Ilmiah</span>
                    </div>
                    <ul class="space-y-2 text-sm text-on-surface-variant">
                        <li>• Apakah metodologi penelitian sesuai dengan tujuan?</li>
                        <li>• Apakah desain penelitian memadai?</li>
                        <li>• Apakah instrumen penelitian valid dan reliabel?</li>
                        <li>• Apakah analisis data direncanakan dengan baik?</li>
                    </ul>
                </div>
                <div class="border border-outline-variant rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-primary">handshake</span>
                        <span class="font-semibold text-primary">Aspek Etik</span>
                    </div>
                    <ul class="space-y-2 text-sm text-on-surface-variant">
                        <li>• Apakah informed consent memadai?</li>
                        <li>• Apakah risiko partisipan diminimalkan?</li>
                        <li>• Apakah kerahasiaan data terjamin?</li>
                        <li>• Apakah ada perlindungan untuk populasi rentan?</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Reviewer Screenshot Placeholder -->
    <div class="bg-gradient-to-r from-primary/5 via-surface-container-low to-primary/5 rounded-xl p-6 border border-primary/20">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="flex-1">
                <h3 class="font-semibold text-primary text-lg mb-2">Dashboard Reviewer</h3>
                <p class="text-sm text-on-surface-variant mb-4">
                    Reviewer memiliki akses ke dashboard khusus untuk mengelola tugas review, melihat proposal yang ditugaskan, dan memberikan feedback.
                </p>
                <div class="flex flex-wrap gap-3">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-primary">pending</span>
                        <span>Proposal Masuk</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-primary">rate_review</span>
                        <span>Review Proposal</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-primary">history</span>
                        <span>Riwayat Review</span>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0 bg-white rounded-xl border border-outline-variant p-4 shadow-sm">
                <div class="w-48 h-32 bg-surface-container-low rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-outline text-4xl">dashboard</span>
                </div>
                <p class="text-center text-xs text-on-surface-variant mt-2">Dashboard Reviewer</p>
            </div>
        </div>
    </div>

    <!-- FAQ Reviewer -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="border-b border-outline-variant bg-surface-container-low px-6 py-4">
            <h2 class="text-lg font-semibold text-primary flex items-center gap-2">
                <span class="material-symbols-outlined">help_center</span>
                Pertanyaan Umum (FAQ) - Reviewer
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div x-data="{ open: false }" class="border-b border-outline-variant pb-3">
                    <button @click="open = !open" class="w-full flex justify-between items-center text-left">
                        <span class="font-semibold text-sm text-on-surface">Berapa lama waktu yang diberikan untuk melakukan review?</span>
                        <span class="material-symbols-outlined text-outline transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="mt-2 text-sm text-on-surface-variant">
                        Reviewer diberikan waktu 7-14 hari kerja untuk menyelesaikan review proposal. Batas waktu akan tertera di sistem.
                    </div>
                </div>
                <div x-data="{ open: false }" class="border-b border-outline-variant pb-3">
                    <button @click="open = !open" class="w-full flex justify-between items-center text-left">
                        <span class="font-semibold text-sm text-on-surface">Apakah reviewer bisa menolak tugas review?</span>
                        <span class="material-symbols-outlined text-outline transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="mt-2 text-sm text-on-surface-variant">
                        Ya, reviewer dapat menolak tugas jika terdapat konflik kepentingan atau ketidaksesuaian bidang keahlian. Harap segera menginformasikan kepada sekretariat.
                    </div>
                </div>
                <div x-data="{ open: false }" class="border-b border-outline-variant pb-3">
                    <button @click="open = !open" class="w-full flex justify-between items-center text-left">
                        <span class="font-semibold text-sm text-on-surface">Apakah reviewer mendapatkan honorarium?</span>
                        <span class="material-symbols-outlined text-outline transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="mt-2 text-sm text-on-surface-variant">
                        Kebijakan honorarium reviewer ditentukan oleh institusi masing-masing. Informasi lebih lanjut dapat dikonfirmasi ke sekretariat Komisi Etik.
                    </div>
                </div>
                <div x-data="{ open: false }" class="border-b border-outline-variant pb-3">
                    <button @click="open = !open" class="w-full flex justify-between items-center text-left">
                        <span class="font-semibold text-sm text-on-surface">Bagaimana jika proposal yang direview memiliki conflict of interest?</span>
                        <span class="material-symbols-outlined text-outline transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="mt-2 text-sm text-on-surface-variant">
                        Reviewer wajib mengungkapkan conflict of interest (COI) dan mengembalikan tugas review ke sekretariat untuk ditugaskan ke reviewer lain.
                    </div>
                </div>
            </div>
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