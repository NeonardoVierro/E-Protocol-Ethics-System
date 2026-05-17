@extends('layouts.dashboard')

@section('title', 'Upload Berkas')

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
                                    <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full font-semibold text-sm">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 h-1 bg-blue-600 mx-3"></div>
                                </div>
                                <!-- Step 2 -->
                                <div class="flex items-center flex-1">
                                    <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-semibold text-sm">2</div>
                                    <div class="flex-1 h-1 bg-slate-300 mx-3"></div>
                                </div>
                                <!-- Step 3 -->
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 bg-slate-300 text-slate-600 rounded-full font-semibold text-sm">3</div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <p class="text-xs font-medium text-slate-600">Informasi Dasar</p>
                                <p class="text-xs font-medium text-blue-600">Upload Berkas</p>
                                <p class="text-xs font-medium text-slate-500">Review & Submit</p>
                            </div>
                        </div>

                        <!-- Form Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-8 border border-slate-200">
                            <h1 class="text-3xl font-bold text-slate-900 mb-2">Upload Dokumen Penelitian</h1>
                            <p class="text-slate-600 mb-8">Unggah dokumen-dokumen sesuai dengan template yang telah disiapkan</p>

                            <form action="{{ route('pengajuan.submit-berkas') ?? '#' }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                                @csrf

                                @if($templates->count() > 0)
                                    @foreach($templates as $template)
                                    <!-- Template: {{ $template->nama_dokumen }} -->
                                    <div class="border border-slate-200 rounded-lg p-6 hover:border-blue-300 hover:bg-blue-50/30 transition-all">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-slate-900 mb-2">
                                                    {{ $template->nama_dokumen }}
                                                    <span class="text-red-500 ml-1">*</span>
                                                </h3>
                                                <p class="text-sm text-slate-600 mb-1">Versi: {{ $template->versi }} | Kategori: {{ $template->kategori }}</p>
                                                <p class="text-sm text-slate-600 mb-4">Format: PDF | Ukuran maksimal: 5MB</p>
                                                
                                                <div class="mt-4">
                                                    <label id="template_{{ $template->id }}_zone" for="template_{{ $template->id }}_file" class="flex items-center justify-center w-full px-4 py-6 border-2 border-dashed border-slate-300 rounded-lg cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all">
                                                        <div class="flex flex-col items-center justify-center">
                                                            <svg class="w-8 h-8 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                            <p class="text-sm font-semibold text-slate-700">Klik untuk pilih atau drag & drop</p>
                                                            <p class="text-xs text-slate-500 mt-1">PDF, max 5MB</p>
                                                        </div>
                                                        <input id="template_{{ $template->id }}_file" type="file" name="template_{{ $template->id }}" accept=".pdf" class="hidden template-file-input" data-template-id="{{ $template->id }}" @if(empty($uploadedFiles['template_' . $template->id])) required @endif>
                                                    </label>
                                                    <div id="template_{{ $template->id }}_display" class="mt-3 hidden items-center justify-between gap-3 px-4 py-3 border border-slate-200 rounded-lg bg-slate-50">
                                                        <div class="flex items-center gap-3 flex-1">
                                                            <div id="template_{{ $template->id }}_icon" class="flex-shrink-0"></div>
                                                            <div id="template_{{ $template->id }}_name" class="text-sm text-slate-700"></div>
                                                        </div>
                                                        <button type="button" id="template_{{ $template->id }}_clear" class="h-8 w-8 rounded-full bg-slate-200 text-slate-500 hover:bg-red-100 hover:text-red-600 transition-colors inline-flex items-center justify-center flex-shrink-0" aria-label="Hapus file">×</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                <!-- Information Box -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <p class="text-sm text-blue-900">
                                        <span class="font-semibold">💡 Catatan:</span> Semua dokumen harus dalam format PDF dengan ukuran maksimal 5MB per file. Pastikan nama dan isi dokumen sesuai dengan template yang diminta.
                                    </p>
                                </div>

                                @else
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-8 text-center">
                                    <svg class="w-16 h-16 text-amber-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-amber-900 font-semibold mb-2">Tidak Ada Template Dokumen</p>
                                    <p class="text-amber-800 text-sm">Saat ini belum ada template dokumen yang tersedia. Silakan hubungi sekretariat untuk informasi lebih lanjut.</p>
                                </div>
                                @endif

                                <!-- Form Actions -->
                                @if($templates->count() > 0)
                                <div class="pt-8 border-t border-slate-200 flex items-center gap-4">
                                    <a href="{{ route('pengajuan.upload-proposal') }}" class="px-6 py-3 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-colors">
                                        Kembali
                                    </a>
                                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                                        Lanjut ke Review & Submit
                                    </button>
                                </div>
                                @endif
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
                                Persyaratan Dokumen
                            </h3>

                            <div class="space-y-4 text-sm text-slate-600">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold">1</div>
                                    <div>
                                        <p class="font-medium text-slate-900">Format PDF</p>
                                        <p class="text-xs mt-1">Semua dokumen harus berformat PDF</p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold">2</div>
                                    <div>
                                        <p class="font-medium text-slate-900">Ukuran Max 5MB</p>
                                        <p class="text-xs mt-1">Setiap file tidak boleh melebihi 5MB</p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold">3</div>
                                    <div>
                                        <p class="font-medium text-slate-900">Semua Template Wajib</p>
                                        <p class="text-xs mt-1">Semua template dokumen harus diupload</p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold">4</div>
                                    <div>
                                        <p class="font-medium text-slate-900">Kualitas Jelas</p>
                                        <p class="text-xs mt-1">Pastikan teks dalam dokumen mudah dibaca</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-slate-200">
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <p class="text-xs font-semibold text-blue-900 mb-2">📊 Jumlah Template</p>
                                    <p class="text-lg font-bold text-blue-900">{{ $templates->count() }} dokumen</p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button class="w-full px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-colors">
                                    📖 Lihat Panduan Dokumen
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->status === 'pending')
                <!-- Konten untuk user yang statusnya pending -->
                <div class="bg-amber-50 rounded-xl border border-amber-200 p-8 text-center max-w-md mx-auto">
                    <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-amber-700 mb-2">Akun Belum Diaktivasi</h2>
                    <p class="text-amber-600 mb-4">Akun Anda sedang menunggu aktivasi oleh sekretariat Komisi Etik Penelitian.</p>
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
                <p class="text-slate-600 mb-6">Silakan login untuk melanjutkan upload dokumen penelitian Anda.</p>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                        Login Sekarang
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-3 border-2 border-blue-600 text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                        Daftar Akun Baru
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>

<script>
    // Data file yang sudah diupload (dari server)
    const uploadedFiles = @json($uploadedFiles ?? []);

    function getFileTypeIcon(fileName) {
        const ext = fileName.split('.').pop().toLowerCase();
        if (ext === 'pdf') {
            return '<div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center text-red-600 font-bold text-xs">PDF</div>';
        } else if (ext === 'docx' || ext === 'doc') {
            return '<div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 font-bold text-xs">DOCX</div>';
        }
        return '<div class="h-10 w-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-600 font-bold text-xs">FILE</div>';
    }

    function showSelectedFile(input) {
        const templateId = input.dataset.templateId;
        const fileName = input.files[0]?.name || '';
        const displayContainer = document.getElementById('template_' + templateId + '_display');
        const displayElement = document.getElementById('template_' + templateId + '_name');
        const iconElement = document.getElementById('template_' + templateId + '_icon');
        const clearButton = document.getElementById('template_' + templateId + '_clear');
        const dropZone = document.getElementById('template_' + templateId + '_zone');

        if (!displayContainer || !displayElement || !iconElement || !clearButton || !dropZone) {
            return;
        }

        if (fileName) {
            iconElement.innerHTML = getFileTypeIcon(fileName);
            displayElement.textContent = fileName;
            displayContainer.classList.remove('hidden');
            displayContainer.classList.add('flex');
            dropZone.classList.add('hidden');
        }
    }

    function restoreUploadedFile(templateId, fileData) {
        const displayContainer = document.getElementById('template_' + templateId + '_display');
        const displayElement = document.getElementById('template_' + templateId + '_name');
        const iconElement = document.getElementById('template_' + templateId + '_icon');
        const dropZone = document.getElementById('template_' + templateId + '_zone');

        if (!displayContainer || !displayElement || !iconElement || !dropZone) {
            return;
        }

        iconElement.innerHTML = getFileTypeIcon(fileData.original_name);
        displayElement.textContent = fileData.original_name;
        displayContainer.classList.remove('hidden');
        displayContainer.classList.add('flex');
        dropZone.classList.add('hidden');
    }

    function clearSelectedFile(templateId) {
        const input = document.getElementById('template_' + templateId + '_file');
        const displayContainer = document.getElementById('template_' + templateId + '_display');
        const displayElement = document.getElementById('template_' + templateId + '_name');
        const iconElement = document.getElementById('template_' + templateId + '_icon');
        const dropZone = document.getElementById('template_' + templateId + '_zone');

        if (!input || !displayContainer || !displayElement || !iconElement || !dropZone) {
            return;
        }

        input.value = '';
        displayElement.textContent = '';
        iconElement.innerHTML = '';
        displayContainer.classList.add('hidden');
        displayContainer.classList.remove('flex');
        dropZone.classList.remove('hidden');
    }

    // Initialize file inputs
    document.querySelectorAll('.template-file-input').forEach(input => {
        const templateId = input.dataset.templateId;
        const fieldName = 'template_' + templateId;
        
        // If file was previously uploaded from session, restore it
        if (uploadedFiles[fieldName]) {
            restoreUploadedFile(templateId, uploadedFiles[fieldName]);
        }

        input.addEventListener('change', function() {
            if (this.files.length > 0) {
                showSelectedFile(this);
            }
        });
    });

    document.querySelectorAll('button[id$="_clear"]').forEach(button => {
        button.addEventListener('click', function() {
            const templateId = this.id.replace('template_', '').replace('_clear', '');
            clearSelectedFile(templateId);
        });
    });

    document.querySelectorAll('label[for^="template_"]').forEach(label => {
        label.addEventListener('dragover', (e) => {
            e.preventDefault();
            label.classList.add('border-blue-400', 'bg-blue-50');
        });

        label.addEventListener('dragleave', () => {
            label.classList.remove('border-blue-400', 'bg-blue-50');
        });

        label.addEventListener('drop', (e) => {
            e.preventDefault();
            label.classList.remove('border-blue-400', 'bg-blue-50');
            const input = document.getElementById(label.getAttribute('for'));
            if (input) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });
</script>
@endsection
