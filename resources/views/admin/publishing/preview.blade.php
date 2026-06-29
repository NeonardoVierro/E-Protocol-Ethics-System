@extends('layouts.admin')

@section('title', 'Preview - Publishing')
@section('page-title', 'Preview Dokumen Ethical Clearance')
@section('breadcrumb', 'Pratinjau dokumen ethical clearance yang akan dipublikasi')

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Preview Dokumen Ethical Clearance</h1>
            <p class="text-slate-500 mt-1">Pratinjau dokumen sebelum dipublikasi ke peneliti.</p>
        </div>
        <a href="{{ route('admin.publishing.index') }}" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50 transition-colors">
            <i class="fas fa-arrow-left text-xs"></i>
            Kembali
        </a>
    </div>

    {{-- Document Info --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 mb-6 shadow-sm">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-bold tracking-wider uppercase text-slate-400 mb-1">Nomor Sertifikat</p>
                <p class="text-lg font-bold text-slate-900">{{ $data['document_number'] ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold tracking-wider uppercase text-slate-400 mb-1">Status Dokumen</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-{{ $document->status === 'signed' ? 'blue' : 'emerald' }}-50 text-{{ $document->status === 'signed' ? 'blue' : 'emerald' }}-700">
                    {{ ucfirst($document->status) }}
                </span>
            </div>
            <div>
                <p class="text-xs font-bold tracking-wider uppercase text-slate-400 mb-1">Nama Peneliti</p>
                <p class="text-slate-700">{{ $data['principal_investigator'] ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold tracking-wider uppercase text-slate-400 mb-1">Tanggal Pratinjau</p>
                <p class="text-slate-700">{{ $data['previewDate'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Preview Container --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        {{-- Preview Header --}}
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="font-semibold text-slate-900">Dokumen Sertifikat</h3>
            <a href="{{ route('admin.publishing.download', $document) }}" class="inline-flex items-center gap-2 bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-download text-xs"></i>
                Download PDF
            </a>
        </div>

        {{-- Preview Content --}}
        <div class="p-8 bg-gradient-to-b from-slate-50 to-white">
            <div class="max-w-3xl mx-auto bg-white border border-slate-200 rounded-lg p-12 shadow-sm">
                {{-- Document Header --}}
                <div class="text-center mb-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-2">ETHICAL CLEARANCE CERTIFICATE</h2>
                    <p class="text-sm text-slate-500">Sertifikat Kelayakan Etik</p>
                </div>

                <div class="space-y-6 text-sm">
                    {{-- Certificate Number --}}
                    <div class="flex">
                        <span class="font-semibold text-slate-700 w-40 flex-shrink-0">Nomor Sertifikat:</span>
                        <span class="text-slate-600">{{ $data['document_number'] ?? '-' }}</span>
                    </div>

                    {{-- Research Title --}}
                    <div class="flex">
                        <span class="font-semibold text-slate-700 w-40 flex-shrink-0">Judul Penelitian:</span>
                        <span class="text-slate-600">{{ $data['title'] ?? '-' }}</span>
                    </div>

                    {{-- Principal Investigator --}}
                    <div class="flex">
                        <span class="font-semibold text-slate-700 w-40 flex-shrink-0">Peneliti Utama:</span>
                        <span class="text-slate-600">{{ $data['principal_investigator'] ?? '-' }}</span>
                    </div>

                    {{-- Research Members --}}
                    <div class="flex">
                        <span class="font-semibold text-slate-700 w-40 flex-shrink-0">Anggota Peneliti:</span>
                        <span class="text-slate-600">{{ $data['members'] ?? '-' }}</span>
                    </div>

                    {{-- Institution --}}
                    <div class="flex">
                        <span class="font-semibold text-slate-700 w-40 flex-shrink-0">Institusi:</span>
                        <span class="text-slate-600">{{ $data['institution'] ?? '-' }}</span>
                    </div>

                    {{-- Research Place --}}
                    <div class="flex">
                        <span class="font-semibold text-slate-700 w-40 flex-shrink-0">Lokasi Penelitian:</span>
                        <span class="text-slate-600">{{ $data['research_place'] ?? '-' }}</span>
                    </div>
                </div>

                {{-- Document Footer --}}
                <div class="mt-12 pt-8 border-t border-slate-200 text-center">
                    <p class="text-xs text-slate-500">Dokumen ini adalah pratinjau dari Sertifikat Ethical Clearance resmi.</p>
                    <p class="text-xs text-slate-500 mt-1">Tanggal Pratinjau: {{ $data['previewDate'] ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="mt-6 flex items-center justify-center gap-3">
        <a href="{{ route('admin.publishing.index') }}" class="inline-flex items-center gap-2 border border-slate-200 bg-white text-slate-700 px-6 py-2.5 rounded-lg hover:bg-slate-50 transition-colors font-semibold">
            <i class="fas fa-arrow-left text-sm"></i>
            Kembali ke Daftar
        </a>
        @if($document->status === 'signed')
        <button onclick="publishDocument({{ $document->id }}, this)" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2.5 rounded-lg transition-colors font-semibold">
            <i class="fas fa-cloud-arrow-up text-sm"></i>
            Publikasikan Sekarang
        </button>
        @endif
    </div>
</div>

@push('scripts')
<script>
function publishDocument(docId, button) {
    if (confirm('Apakah Anda yakin ingin mempublikasikan dokumen ini?')) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i> Memproses...';

        fetch(`/admin/publishing/${docId}/publish`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Dokumen berhasil dipublikasikan!');
                window.location.href = '{{ route('admin.publishing.index') }}';
            } else {
                alert('Gagal mempublikasikan dokumen: ' + (data.error || 'Unknown error'));
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-cloud-arrow-up text-sm"></i> Publikasikan Sekarang';
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-cloud-arrow-up text-sm"></i> Publikasikan Sekarang';
        });
    }
}
</script>
@endpush

@endsection
