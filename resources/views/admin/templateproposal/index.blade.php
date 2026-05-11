@extends('layouts.admin')

@section('title', 'Template Proposal - Admin')
@section('page-title', 'Template Proposal')
@section('breadcrumb', 'Kelola template dokumen untuk ethical clearance')

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl mb-5 text-[13.5px] font-medium shadow-sm">
    <i class="fas fa-circle-check text-emerald-500"></i>
    {{ session('success') }}
    <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600 cursor-pointer">
        <i class="fas fa-xmark text-sm"></i>
    </button>
</div>
@endif
 
@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl mb-5 text-[13.5px] font-medium shadow-sm">
    <i class="fas fa-circle-xmark text-red-500"></i>
    {{ session('error') }}
    <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600 cursor-pointer">
        <i class="fas fa-xmark text-sm"></i>
    </button>
</div>
@endif
 
{{-- ═══════════════════════════════════════════
     Stat Cards
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-3 gap-5 mb-6">
    <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-file-lines text-slate-500 text-lg"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Total Template</p>
            <p class="text-[22px] font-bold text-slate-900 leading-none tracking-tight">{{ $stats['total'] }}</p>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-circle-check text-emerald-500 text-lg"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Template Aktif</p>
            <p class="text-[22px] font-bold text-slate-900 leading-none tracking-tight">{{ $stats['aktif'] }}</p>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-clock-rotate-left text-orange-400 text-lg"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Terakhir Diperbarui</p>
            <p class="text-[18px] font-bold text-slate-900 leading-none tracking-tight">
                {{ $stats['terakhir_update'] ? \Carbon\Carbon::parse($stats['terakhir_update'])->format('d M Y') : '-' }}
            </p>
        </div>
    </div>
</div>
 
{{-- ═══════════════════════════════════════════
     Daftar Dokumen
═══════════════════════════════════════════ --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-6 overflow-hidden">
 
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <span class="text-[15px] font-bold text-slate-900">Daftar Dokumen</span>
        <span class="text-[12.5px] text-slate-400">{{ $templates->total() }} template</span>
    </div>
 
    @if($templates->isEmpty())
    <div class="py-16 text-center">
        <i class="fas fa-folder-open text-slate-300 text-4xl mb-3 block"></i>
        <p class="text-[14px] text-slate-400 font-medium">Belum ada template. Upload template pertama kamu!</p>
    </div>
    @else
    <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">Nama Dokumen</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Versi</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Kategori</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Ukuran</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Tanggal Update</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Status</th>
                <th class="text-right text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach($templates as $t)
            <tr class="hover:bg-slate-50/60 transition-colors {{ !$t->is_active ? 'opacity-60' : '' }}">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <i class="fas {{ $t->file_type === 'pdf' ? 'fa-file-pdf text-red-500' : 'fa-file-word text-blue-500' }} text-lg flex-shrink-0"></i>
                        <div>
                            <div class="text-[13.5px] font-semibold text-slate-800">{{ $t->nama_dokumen }}</div>
                            <div class="text-[11px] text-slate-400">{{ $t->file_name }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 text-[13px] text-slate-600 font-medium">{{ $t->versi }}</td>
                <td class="px-4 py-4">
                    @php
                    $katClass = match($t->kategori) {
                        'Biomedis' => 'bg-blue-50 text-blue-600',
                        'Sosial'   => 'bg-emerald-50 text-emerald-600',
                        default    => 'bg-purple-50 text-purple-600',
                    };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase {{ $katClass }}">
                        {{ $t->kategori }}
                    </span>
                </td>
                <td class="px-4 py-4 text-[13px] text-slate-500">{{ $t->file_size_human }}</td>
                <td class="px-4 py-4 text-[13px] text-slate-500">
                    {{ \Carbon\Carbon::parse($t->updated_at)->format('d M Y') }}
                </td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center gap-1.5 text-[12px] font-medium {{ $t->is_active ? 'text-emerald-600' : 'text-slate-400' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $t->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                        {{ $t->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-1.5">
                        {{-- Edit --}}
                        <button onclick="openEditModal({{ $t->id }}, '{{ addslashes($t->nama_dokumen) }}', '{{ $t->versi }}', '{{ $t->kategori }}')"
                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer" title="Edit">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        {{-- Download --}}
                        <a href="{{ route('admin.templates.download', $t->id) }}"
                           class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer" title="Download">
                            <i class="fas fa-download text-xs"></i>
                        </a>
                        {{-- Toggle Aktif --}}
                        <form action="{{ route('admin.templates.toggle', $t->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="w-7 h-7 rounded-md flex items-center justify-center transition-colors cursor-pointer {{ $t->is_active ? 'text-slate-400 hover:bg-orange-50 hover:text-orange-400' : 'text-slate-400 hover:bg-emerald-50 hover:text-emerald-500' }}"
                                    title="{{ $t->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i class="fas {{ $t->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} text-sm"></i>
                            </button>
                        </form>
                        {{-- Hapus --}}
                        <form action="{{ route('admin.templates.destroy', $t->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus template {{ addslashes($t->nama_dokumen) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors cursor-pointer" title="Hapus">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
 
    {{-- Pagination --}}
    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
        <span class="text-[12.5px] text-slate-400">
            Showing {{ $templates->firstItem() }}–{{ $templates->lastItem() }} of {{ $templates->total() }} templates
        </span>
        <div class="flex items-center gap-1">
            {{-- Previous --}}
            @if($templates->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-300 cursor-not-allowed">
                    <i class="fas fa-chevron-left text-xs"></i>
                </span>
            @else
                <a href="{{ $templates->previousPageUrl() }}"
                   class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
            @endif
 
            {{-- Page numbers --}}
            @foreach($templates->getUrlRange(1, $templates->lastPage()) as $page => $url)
                @if($page == $templates->currentPage())
                    <span class="w-8 h-8 flex items-center justify-center bg-[#1e3a5f] text-white text-[13px] font-semibold rounded-lg">{{ $page }}</span>
                @else
                    <a href="{{ $url }}"
                       class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-[13px] text-slate-600 hover:bg-slate-50 transition-colors">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
 
            {{-- Next --}}
            @if($templates->hasMorePages())
                <a href="{{ $templates->nextPageUrl() }}"
                   class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            @else
                <span class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-300 cursor-not-allowed">
                    <i class="fas fa-chevron-right text-xs"></i>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>
 
{{-- ═══════════════════════════════════════════
     ROW BAWAH: Upload + Form (disambung ke backend)
═══════════════════════════════════════════ --}}
<div id="form-upload" class="grid grid-cols-2 gap-5">
 
    {{-- Upload Area --}}
    <div class="bg-white border-2 border-dashed border-slate-200 rounded-2xl shadow-sm p-8 flex flex-col items-center justify-center text-center transition-colors"
         id="drop-zone"
         ondragover="event.preventDefault(); this.classList.add('border-blue-400','bg-blue-50/40')"
         ondragleave="this.classList.remove('border-blue-400','bg-blue-50/40')"
         ondrop="handleFileDrop(event)">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
            <i class="fas fa-cloud-arrow-up text-slate-400 text-2xl"></i>
        </div>
        <h3 class="text-[15px] font-bold text-slate-800 mb-1.5">Upload File Baru</h3>
        <p class="text-[13px] text-slate-400 mb-2 leading-relaxed">
            Tarik dan lepas file PDF atau DOCX di sini<br>atau klik tombol di bawah.
        </p>
        <p id="drop-file-name" class="text-[12px] text-blue-500 font-semibold mb-3 hidden"></p>
        <button type="button" onclick="document.getElementById('file-input-hidden').click()"
                class="px-6 py-2 border-2 border-slate-300 rounded-xl text-[13px] font-semibold text-slate-600 hover:border-slate-400 hover:bg-slate-50 transition-colors cursor-pointer">
            Pilih File dari Komputer
        </button>
    </div>
 
    {{-- Form Detail (disambung ke route store) --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h3 class="text-[15px] font-bold text-slate-900 mb-5 pb-4 border-b border-slate-100">Detail Template Baru</h3>
 
        <form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data" id="form-tambah-template">
            @csrf
 
            {{-- File input hidden (trigger dari drop zone & tombol pilih) --}}
            <input type="file" id="file-input-hidden" name="file" accept=".pdf,.docx,.doc"
                   class="hidden" onchange="handleFileInput(this)">
 
            {{-- Validation errors --}}
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="text-[12.5px] text-red-600 flex items-start gap-2">
                        <i class="fas fa-circle-exclamation text-red-400 mt-0.5 flex-shrink-0"></i>
                        {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
 
            <div class="space-y-4">
                {{-- Nama Dokumen --}}
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">
                        Nama Dokumen <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="nama_dokumen" value="{{ old('nama_dokumen') }}"
                           placeholder="Masukkan nama template..."
                           class="w-full px-3.5 py-2.5 text-[13.5px] border {{ $errors->has('nama_dokumen') ? 'border-red-300' : 'border-slate-200' }} rounded-xl outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 placeholder-slate-400">
                    @error('nama_dokumen')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
 
                {{-- Versi + Kategori --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">
                            Versi <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="versi" value="{{ old('versi') }}" placeholder="e.g. v2.5"
                               class="w-full px-3.5 py-2.5 text-[13.5px] border {{ $errors->has('versi') ? 'border-red-300' : 'border-slate-200' }} rounded-xl outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 placeholder-slate-400">
                        @error('versi')
                            <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">
                            Kategori <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <select name="kategori"
                                    class="w-full px-3.5 py-2.5 text-[13.5px] border {{ $errors->has('kategori') ? 'border-red-300' : 'border-slate-200' }} rounded-xl outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 appearance-none bg-white cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <option value="Biomedis" {{ old('kategori') === 'Biomedis' ? 'selected' : '' }}>Biomedis</option>
                                <option value="Sosial"   {{ old('kategori') === 'Sosial'   ? 'selected' : '' }}>Sosial</option>
                                <option value="Umum"     {{ old('kategori') === 'Umum'     ? 'selected' : '' }}>Umum</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                        @error('kategori')
                            <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
 
                {{-- File indicator --}}
                <div id="file-indicator" class="hidden">
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">File Dipilih</label>
                    <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5">
                        <i class="fas fa-file-lines text-blue-500 text-base flex-shrink-0"></i>
                        <span id="file-indicator-name" class="text-[13px] font-medium text-slate-700 flex-1 truncate"></span>
                        <button type="button" onclick="clearFile()"
                                class="text-slate-400 hover:text-red-400 transition-colors cursor-pointer">
                            <i class="fas fa-xmark text-sm"></i>
                        </button>
                    </div>
                </div>
                @error('file')
                    <p class="text-[11px] text-red-500 -mt-2">{{ $message }}</p>
                @enderror
 
                {{-- Buttons --}}
                <div class="flex items-center gap-3 pt-1">
                    <button type="submit"
                            class="flex-1 py-2.5 bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-[13.5px] font-semibold rounded-xl transition-colors cursor-pointer">
                        Simpan & Aktifkan
                    </button>
                    <button type="reset" onclick="clearFile()"
                            class="px-5 py-2.5 text-[13.5px] font-semibold text-slate-600 hover:bg-slate-50 border border-slate-200 rounded-xl transition-colors cursor-pointer">
                        Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
 
{{-- ═══════════════════════════════════════════
     MODAL: Edit Template
═══════════════════════════════════════════ --}}
<div id="modal-edit" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
 
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="text-[16px] font-bold text-slate-900">Edit Template</h3>
            <button onclick="closeEditModal()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 cursor-pointer">
                <i class="fas fa-xmark text-sm"></i>
            </button>
        </div>
 
        <form id="form-edit" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nama Dokumen <span class="text-red-400">*</span></label>
                    <input type="text" name="nama_dokumen" id="edit-nama"
                           class="w-full px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Versi <span class="text-red-400">*</span></label>
                        <input type="text" name="versi" id="edit-versi"
                               class="w-full px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Kategori <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <select name="kategori" id="edit-kategori"
                                    class="w-full appearance-none px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 cursor-pointer">
                                <option value="Biomedis">Biomedis</option>
                                <option value="Sosial">Sosial</option>
                                <option value="Umum">Umum</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Ganti File <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="file" name="file" accept=".pdf,.docx,.doc"
                           class="w-full text-[13px] text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 file:font-semibold file:cursor-pointer hover:file:bg-slate-200 transition-all">
                </div>
            </div>
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/60">
                <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2.5 text-[13.5px] font-semibold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-[13.5px] font-semibold px-6 py-2.5 rounded-xl transition-colors cursor-pointer">
                    <i class="fas fa-floppy-disk text-xs"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
 
@endsection
 
@push('scripts')
<script>
// ── File drag & drop ──────────────────────────────────────────────
function handleFileDrop(e) {
    e.preventDefault();
    const zone = document.getElementById('drop-zone');
    zone.classList.remove('border-blue-400', 'bg-blue-50/40');
    const file = e.dataTransfer.files[0];
    if (file) setFile(file);
}
 
function handleFileInput(input) {
    if (input.files[0]) setFile(input.files[0]);
}
 
function setFile(file) {
    // Validasi tipe
    const allowed = ['application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword'];
    if (!allowed.includes(file.type) && !file.name.match(/\.(pdf|docx|doc)$/i)) {
        alert('Format file harus PDF atau DOCX.');
        return;
    }
 
    // Transfer ke input form
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('file-input-hidden').files = dt.files;
 
    // Tampilkan nama file
    const ind  = document.getElementById('file-indicator');
    const name = document.getElementById('file-indicator-name');
    ind.classList.remove('hidden');
    name.textContent = file.name;
 
    // Update drop zone
    const dropName = document.getElementById('drop-file-name');
    dropName.textContent = '✓ ' + file.name;
    dropName.classList.remove('hidden');
}
 
function clearFile() {
    document.getElementById('file-input-hidden').value = '';
    document.getElementById('file-indicator').classList.add('hidden');
    document.getElementById('drop-file-name').classList.add('hidden');
}
 
// ── Modal Edit ────────────────────────────────────────────────────
function openEditModal(id, nama, versi, kategori) {
    document.getElementById('edit-nama').value     = nama;
    document.getElementById('edit-versi').value    = versi;
    document.getElementById('edit-kategori').value = kategori;
    document.getElementById('form-edit').action    = `/admin/templates/${id}`;
 
    const modal = document.getElementById('modal-edit');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
 
function closeEditModal() {
    const modal = document.getElementById('modal-edit');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
 
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeEditModal();
});
</script>
@endpush