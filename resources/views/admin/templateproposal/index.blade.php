@extends('layouts.admin')

@section('title', 'Template Proposal - Admin')
@section('page-title', 'Template Proposal')
@section('breadcrumb', 'Kelola template dokumen untuk ethical clearance')

@section('content')

{{-- ═══════════════════════════════════════════
     Page Header
═══════════════════════════════════════════ --}}
<div class="flex items-start justify-between mb-6">
    <button onclick="featureInDevelopment('Upload Template Baru')"
            class="inline-flex items-center gap-2 bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors cursor-pointer">
        <i class="fas fa-file-arrow-up text-xs"></i>
        + Upload Template Baru
    </button>
</div>

{{-- ═══════════════════════════════════════════
     Stat Cards (3 kolom)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-3 gap-5 mb-6">
    @php
    $stats = [
        ['icon'=>'fas fa-file-lines', 'iconBg'=>'bg-slate-100',   'iconColor'=>'text-slate-500',  'label'=>'TOTAL TEMPLATE',      'value'=>'24'],
        ['icon'=>'fas fa-circle-check','iconBg'=>'bg-emerald-50',  'iconColor'=>'text-emerald-500','label'=>'TEMPLATE AKTIF',      'value'=>'18'],
        ['icon'=>'fas fa-clock-rotate-left','iconBg'=>'bg-orange-50','iconColor'=>'text-orange-400','label'=>'TERAKHIR DIPERBARUI','value'=>'12 Okt 2023'],
    ];
    @endphp
    @foreach($stats as $s)
    <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 {{ $s['iconBg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="{{ $s['icon'] }} {{ $s['iconColor'] }} text-lg"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">{{ $s['label'] }}</p>
            <p class="text-[22px] font-bold text-slate-900 leading-none tracking-tight">{{ $s['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════
     Daftar Dokumen
═══════════════════════════════════════════ --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-6 overflow-hidden">

    {{-- Table Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <span class="text-[15px] font-bold text-slate-900">Daftar Dokumen</span>
        <div class="flex items-center gap-2">
            <button onclick="featureInDevelopment('Filter')"
                    class="w-8 h-8 border border-slate-200 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-sliders text-xs"></i>
            </button>
            <button onclick="featureInDevelopment('More Options')"
                    class="w-8 h-8 border border-slate-200 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-ellipsis-vertical text-xs"></i>
            </button>
        </div>
    </div>

    {{-- Table --}}
    <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">Nama Dokumen</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Versi</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Kategori</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Tanggal Update</th>
                <th class="text-right text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @php
            $templates = [
                ['icon'=>'fa-file-pdf',  'iconColor'=>'text-red-500',  'name'=>'Formulir Protokol Etik',   'versi'=>'v2.1', 'kategori'=>'BIOMEDIS', 'katColor'=>'bg-blue-50 text-blue-600',    'updated'=>'10 Okt 2023'],
                ['icon'=>'fa-file-word', 'iconColor'=>'text-blue-500', 'name'=>'Informed Consent Template','versi'=>'v1.8', 'kategori'=>'SOSIAL',   'katColor'=>'bg-emerald-50 text-emerald-600','updated'=>'05 Okt 2023'],
                ['icon'=>'fa-file-pdf',  'iconColor'=>'text-red-500',  'name'=>'Suplemen Data Peneliti',   'versi'=>'v3.0', 'kategori'=>'UMUM',     'katColor'=>'bg-purple-50 text-purple-600', 'updated'=>'22 Sep 2023'],
            ];
            @endphp
            @foreach($templates as $t)
            <tr class="hover:bg-slate-50/60 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <i class="fas {{ $t['icon'] }} {{ $t['iconColor'] }} text-lg flex-shrink-0"></i>
                        <span class="text-[13.5px] font-semibold text-slate-800">{{ $t['name'] }}</span>
                    </div>
                </td>
                <td class="px-4 py-4 text-[13px] text-slate-600 font-medium">{{ $t['versi'] }}</td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase {{ $t['katColor'] }}">{{ $t['kategori'] }}</span>
                </td>
                <td class="px-4 py-4 text-[13px] text-slate-500">{{ $t['updated'] }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="featureInDevelopment('Edit Template')"
                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer"
                                title="Edit">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        <button onclick="featureInDevelopment('Download Template')"
                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer"
                                title="Download">
                            <i class="fas fa-download text-xs"></i>
                        </button>
                        <button onclick="featureInDevelopment('Hapus Template')"
                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors cursor-pointer"
                                title="Hapus">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
        <span class="text-[12.5px] text-slate-400">Showing 3 of 24 templates</span>
        <div class="flex items-center gap-1">
            <button onclick="featureInDevelopment('Previous')"
                    class="px-3 py-1.5 text-[12.5px] font-medium text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer">
                Previous
            </button>
            <button class="px-3 py-1.5 text-[12.5px] font-medium text-white bg-[#1e3a5f] border border-[#1e3a5f] rounded-lg cursor-pointer">1</button>
            <button onclick="featureInDevelopment('Page 2')"
                    class="px-3 py-1.5 text-[12.5px] font-medium text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer">2</button>
            <button onclick="featureInDevelopment('Next')"
                    class="px-3 py-1.5 text-[12.5px] font-medium text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer">
                Next
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     ROW BAWAH: Upload Area (kiri) + Form Detail (kanan)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-2 gap-5">

    {{-- Upload Area --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8 flex flex-col items-center justify-center text-center"
         id="drop-zone"
         ondragover="event.preventDefault(); this.classList.add('border-blue-400','bg-blue-50')"
         ondragleave="this.classList.remove('border-blue-400','bg-blue-50')"
         ondrop="handleDrop(event)">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
            <i class="fas fa-cloud-arrow-up text-slate-400 text-2xl"></i>
        </div>
        <h3 class="text-[15px] font-bold text-slate-800 mb-1.5">Upload File Baru</h3>
        <p class="text-[13px] text-slate-400 mb-5 leading-relaxed">
            Tarik dan lepas file PDF atau DOCX Anda di sini<br>untuk memulai proses update template.
        </p>
        <button onclick="featureInDevelopment('Pilih File')"
                class="px-6 py-2 border-2 border-slate-300 rounded-xl text-[13px] font-semibold text-slate-600 hover:border-slate-400 hover:bg-slate-50 transition-colors cursor-pointer">
            Pilih File dari Komputer
        </button>
    </div>

    {{-- Form Detail Template Baru --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h3 class="text-[15px] font-bold text-slate-900 mb-5 pb-4 border-b border-slate-100">Detail Template Baru</h3>

        <div class="space-y-4">
            {{-- Nama Dokumen --}}
            <div>
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nama Dokumen</label>
                <input type="text" placeholder="Masukkan nama template..."
                       class="w-full px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 placeholder-slate-400">
            </div>

            {{-- Versi + Kategori --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Versi</label>
                    <input type="text" placeholder="e.g. v2.5"
                           class="w-full px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 placeholder-slate-400">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Kategori</label>
                    <div class="relative">
                        <select class="w-full px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 appearance-none bg-white cursor-pointer">
                            <option>Biomedis</option>
                            <option>Sosial</option>
                            <option>Umum</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-3 pt-2">
                <button onclick="featureInDevelopment('Simpan & Aktifkan')"
                        class="flex-1 py-2.5 bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-[13.5px] font-semibold rounded-xl transition-colors cursor-pointer">
                    Simpan & Aktifkan
                </button>
                <button onclick="featureInDevelopment('Batal')"
                        class="px-5 py-2.5 text-[13.5px] font-semibold text-slate-600 hover:bg-slate-50 border border-slate-200 rounded-xl transition-colors cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function handleDrop(e) {
    e.preventDefault();
    const zone = document.getElementById('drop-zone');
    zone.classList.remove('border-blue-400', 'bg-blue-50');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        featureInDevelopment('Upload: ' + files[0].name);
    }
}
</script>
@endpush