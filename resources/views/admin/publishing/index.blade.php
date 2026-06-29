@extends('layouts.admin')

@section('title', 'Publishing - Admin')
@section('page-title', 'Publishing')
@section('breadcrumb', 'Kelola publikasi dokumen untuk ethical clearance')

@section('content')

{{-- ═══════════════════════════════════════════
     Page Header
═══════════════════════════════════════════ --}}
<div class="flex items-start justify-between mb-6">
    {{-- Reviewers Active --}}
    <div class="flex items-center gap-2.5 bg-white border border-slate-200 rounded-xl px-3.5 py-2 shadow-sm">
        <div class="flex items-center">
            @foreach(['HV','MS','AL'] as $i => $init)
            <div class="w-7 h-7 rounded-full bg-slate-400 {{ $i > 0 ? '-ml-2' : '' }} border-2 border-white flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0">
                {{ $init }}
            </div>
            @endforeach
            <div class="-ml-2 w-7 h-7 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-slate-600 text-[9px] font-bold flex-shrink-0">+12</div>
        </div>
        <span class="text-[12.5px] font-semibold text-slate-600">Reviewers Active</span>
    </div>
</div>

{{-- ═══════════════════════════════════════════
    Stat Cards (3 kolom)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-3 gap-6 mb-6">
    @php
    $stats = [
        ['icon'=>'fas fa-circle-check',  'iconBg'=>'bg-blue-50',   'iconColor'=>'text-blue-500',   'label'=>'TOTAL READY TO PUBLISH', 'value'=>number_format($readyToPublishCount ?? 0)],
        ['icon'=>'fas fa-calendar-check','iconBg'=>'bg-emerald-50', 'iconColor'=>'text-emerald-500','label'=>'PUBLISHED THIS MONTH',   'value'=>number_format($publishedThisMonthCount ?? 0)],
        ['icon'=>'fas fa-hourglass-half','iconBg'=>'bg-orange-50',  'iconColor'=>'text-orange-400', 'label'=>'PENDING VERIFICATION',   'value'=>number_format($pendingVerificationCount ?? 0)],
    ];
    @endphp
    @foreach($stats as $s)
    <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 {{ $s['iconBg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="{{ $s['icon'] }} {{ $s['iconColor'] }} text-lg"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">{{ $s['label'] }}</p>
            <p class="text-[28px] font-bold text-slate-900 leading-none tracking-tight">{{ $s['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════
     Bulk Actions Banner
═══════════════════════════════════════════ --}}
<div class="rounded-2xl p-5 mb-5 flex items-center justify-between" style="background: #1e3a5f;"
     x-data="{ selected: 0 }">
    <div>
        <h3 class="text-[15px] font-bold text-white mb-1">Bulk Actions & Registry Release</h3>
        <p class="text-[12.5px] text-blue-200/80 leading-relaxed max-w-lg">
            Select multiple clearance certificates from the queue to publish them to the public registry simultaneously.
        </p>
    </div>
    <div class="flex items-center gap-4 flex-shrink-0">
        <div class="text-right">
            <p class="text-[9.5px] font-bold tracking-widest uppercase text-blue-300/70 mb-0.5">Selection Mode</p>
            <p class="text-[13px] font-bold text-white"><span x-text="selected">0</span> items selected</p>
        </div>
        <button onclick="publishSelected()"
                class="inline-flex items-center gap-2 bg-emerald-400 hover:bg-emerald-300 text-slate-900 text-[13px] font-bold px-5 py-2.5 rounded-xl transition-colors cursor-pointer">
            <i class="fas fa-cloud-arrow-up text-sm"></i>
            Publish Selected Certificates
        </button>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     Certificates Ready for Publishing
═══════════════════════════════════════════ --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-5"
     x-data="bulkSelect()">

    {{-- Table Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <i class="fas fa-file-certificate text-blue-500 text-base"></i>
            <span class="text-[15px] font-bold text-slate-900">Certificates Ready for Publishing</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="featureInDevelopment('Filter')"
                    class="w-8 h-8 border border-slate-200 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-sliders text-xs"></i>
            </button>
            <button onclick="featureInDevelopment('Refresh')"
                    class="w-8 h-8 border border-slate-200 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-rotate text-xs"></i>
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px]">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 w-10">
                        <input type="checkbox" @change="toggleAll($event)"
                               class="w-4 h-4 rounded border-slate-300 text-[#1e3a5f] cursor-pointer accent-[#1e3a5f]">
                    </th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Proposal ID</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Research Title</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Researcher</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Clearance Number</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Decision Date</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Public Status</th>
                    <th class="text-right text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">Actions</th>
                </tr>
            </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($docs as $i => $doc)
            @php
                $initials = strtoupper(substr($doc->proposal?->researcher?->name ?? 'U', 0, 2));
                $date = $doc->signed_date?->format('M d, Y') ?? '-';
            @endphp
            <tr class="hover:bg-slate-50/60 transition-colors" :class="checked[{{ $i }}] ? 'bg-blue-50/40' : ''">
                <td class="px-6 py-4">
                    <input type="checkbox" x-model="checked[{{ $i }}]"
                           class="w-4 h-4 rounded border-slate-300 cursor-pointer accent-[#1e3a5f]">
                </td>
                <td class="px-4 py-4">
                    <span class="text-[13.5px] font-bold text-[#1e3a5f]">{{ $doc->document_number ?: ('EC-'.$doc->id) }}</span>
                </td>
                <td class="px-4 py-4">
                    <div class="max-w-xl text-[13px] text-slate-700 font-medium line-clamp-2">{{ $doc->proposal?->title ?? '-' }}</div>
                </td>
                <td class="px-4 py-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-slate-400 flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0">{{ $initials }}</div>
                        <span class="text-[13px] font-medium text-slate-700">{{ $doc->proposal?->researcher?->name ?? '-' }}</span>
                    </div>
                </td>
                <td class="px-4 py-4 text-[13px] text-slate-600 font-medium">{{ $doc->proposal?->nomor_ec ?? '-' }}</td>
                <td class="px-4 py-4 text-[13px] text-slate-500">{{ $date }}</td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase bg-emerald-50 text-emerald-700">READY</span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        @if($doc->file_path && Storage::disk('public')->exists($doc->file_path))
                        <a href="{{ route('admin.publishing.preview', $doc) }}" target="_blank" rel="noopener noreferrer"
                           class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer" title="Preview">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        @else
                        <button onclick="featureInDevelopment('Preview Sertifikat')"
                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer" title="Preview">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                        @endif
                        <button onclick="publishDocument({{ $doc->id }}, this)"
                                class="inline-flex items-center gap-1.5 bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-[12px] font-semibold px-3.5 py-1.5 rounded-lg transition-colors cursor-pointer">
                            Publish
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-16 text-center">
                    <i class="fas fa-inbox text-slate-300 text-4xl mb-3 block"></i>
                    <p class="text-[14px] text-slate-400 font-medium">Belum ada proposal siap dipublish</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($docs->hasPages())
    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
        <span class="text-[12.5px] text-slate-400">
            Showing {{ $docs->firstItem() }}–{{ $docs->lastItem() }} of {{ $docs->total() }}
        </span>
        <div class="flex items-center gap-1">
            @if($docs->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-300 cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i></span>
            @else
                <a href="{{ $docs->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif
            @foreach($docs->getUrlRange(1, $docs->lastPage()) as $page => $url)
                @if($page == $docs->currentPage())
                    <span class="w-8 h-8 flex items-center justify-center bg-[#1e3a5f] text-white text-[13px] font-semibold rounded-lg">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-[13px] text-slate-600 hover:bg-slate-50 transition-colors">{{ $page }}</a>
                @endif
            @endforeach
            @if($docs->hasMorePages())
                <a href="{{ $docs->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors"><i class="fas fa-chevron-right text-xs"></i></a>
            @else
                <span class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-300 cursor-not-allowed"><i class="fas fa-chevron-right text-xs"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════
    Published Certificates
═══════════════════════════════════════════ --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <i class="fas fa-book-open text-slate-700 text-base"></i>
            <span class="text-[15px] font-bold text-slate-900">Published Certificates</span>
        </div>
        <span class="text-[12px] text-slate-500">Latest published documents available for admin download</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px]">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Proposal ID</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Research Title</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Researcher</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Clearance Number</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Published Date</th>
                    <th class="text-right text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">Actions</th>
                </tr>
            </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($publishedDocs as $doc)
            @php
                $initials = strtoupper(substr($doc->proposal?->researcher?->name ?? 'U', 0, 2));
                $publishedDate = $doc->published_date?->format('M d, Y') ?? '-';
                $previewUrl = $doc->file_path ? Storage::disk('public')->url($doc->file_path) : null;
            @endphp
            <tr class="hover:bg-slate-50/60 transition-colors">
                <td class="px-4 py-4">
                    <span class="text-[13.5px] font-bold text-[#1e3a5f]">{{ $doc->document_number ?: ('EC-'.$doc->id) }}</span>
                </td>
                <td class="px-4 py-4">
                    <div class="max-w-xl text-[13px] text-slate-700 font-medium line-clamp-2">{{ $doc->proposal?->title ?? '-' }}</div>
                </td>
                <td class="px-4 py-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-slate-400 flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0">{{ $initials }}</div>
                        <span class="text-[13px] font-medium text-slate-700">{{ $doc->proposal?->researcher?->name ?? '-' }}</span>
                    </div>
                </td>
                <td class="px-4 py-4 text-[13px] text-slate-600 font-medium">{{ $doc->proposal?->nomor_ec ?? '-' }}</span></td>
                <td class="px-4 py-4 text-[13px] text-slate-500">{{ $publishedDate }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        @if($doc->file_path && Storage::disk('public')->exists($doc->file_path))
                        <a href="{{ route('admin.publishing.preview', $doc) }}" target="_blank" rel="noopener noreferrer"
                            class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition-colors"
                            title="Preview">
                            <i class="fas fa-eye text-[11px]"></i>
                        </a>
                        @endif
                        <a href="{{ route('admin.publishing.download', $doc) }}"
                            class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer"
                            title="Download PDF">
                            <i class="fas fa-download text-xs"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-16 text-center">
                    <i class="fas fa-inbox text-slate-300 text-4xl mb-3 block"></i>
                    <p class="text-[14px] text-slate-400 font-medium">Belum ada dokumen yang dipublikasi.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- Pagination for Published Certificates --}}
    @if(isset($publishedDocs) && $publishedDocs->hasPages())
    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
        <span class="text-[12.5px] text-slate-400">
            Showing {{ $publishedDocs->firstItem() }}–{{ $publishedDocs->lastItem() }} of {{ $publishedDocs->total() }}
        </span>
        <div class="flex items-center gap-1">
            @if($publishedDocs->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-300 cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i></span>
            @else
                <a href="{{ $publishedDocs->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif
            @foreach($publishedDocs->getUrlRange(1, $publishedDocs->lastPage()) as $page => $url)
                @if($page == $publishedDocs->currentPage())
                    <span class="w-8 h-8 flex items-center justify-center bg-[#1e3a5f] text-white text-[13px] font-semibold rounded-lg">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-[13px] text-slate-600 hover:bg-slate-50 transition-colors">{{ $page }}</a>
                @endif
            @endforeach
            @if($publishedDocs->hasMorePages())
                <a href="{{ $publishedDocs->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors"><i class="fas fa-chevron-right text-xs"></i></a>
            @else
                <span class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-300 cursor-not-allowed"><i class="fas fa-chevron-right text-xs"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Row removed: Publishing Guidelines and Security Verified cards were deleted --}}

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function bulkSelect() {
    return {
        checked: {},
        toggleAll(e) {
            const val = e.target.checked;
            Object.keys(this.checked).forEach(k => this.checked[k] = val);
        },
    };
}

// ── Publish Proposal ───────────────────────────────────────────────
async function publishDocument(documentId, btn) {
    const result = await Swal.fire({
        title: 'Publish dokumen?',
        text: 'Dokumen ini akan dipublish ke publik. Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, publish',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            confirmButton: 'swal2-confirm bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl px-4 py-2',
            cancelButton: 'swal2-cancel bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl px-4 py-2',
        },
    });

    if (!result.isConfirmed) {
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin text-[9px]"></i> Publishing...';

    try {
        const res = await fetch(`/admin/publishing/${documentId}/publish`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        });
        if (!res.ok) throw new Error();

        await Swal.fire({
            title: 'Berhasil!',
            text: 'Dokumen berhasil dipublish.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'swal2-confirm bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl px-4 py-2',
            },
        });

        location.reload();
    } catch {
        btn.disabled = false;
        btn.innerHTML = 'Publish';
        await Swal.fire({
            title: 'Gagal',
            text: 'Gagal mempublish dokumen.',
            icon: 'error',
            confirmButtonText: 'Tutup',
            customClass: {
                confirmButton: 'swal2-confirm bg-slate-900 hover:bg-slate-800 text-white rounded-2xl px-4 py-2',
            },
        });
    }
}

// ── Publish Selected ───────────────────────────────────────────────
async function publishSelected() {
    const bulkData = Alpine.$data(document.querySelector('[x-data]'));
    const selectedIds = Object.keys(bulkData.checked).filter(k => bulkData.checked[k]);

    if (selectedIds.length === 0) {
        await Swal.fire({
            title: 'Pilih dokumen',
            text: 'Pilih minimal satu dokumen untuk dipublish.',
            icon: 'warning',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'swal2-confirm bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl px-4 py-2',
            },
        });
        return;
    }

    const result = await Swal.fire({
        title: 'Publish dokumen terpilih?',
        text: `Anda akan mempublish ${selectedIds.length} dokumen. Tindakan ini tidak dapat dibatalkan.`, 
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, publish',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            confirmButton: 'swal2-confirm bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl px-4 py-2',
            cancelButton: 'swal2-cancel bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl px-4 py-2',
        },
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        const res = await fetch('/admin/publishing/bulk-publish', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
            body: JSON.stringify({ document_ids: selectedIds }),
        });
        if (!res.ok) throw new Error();

        await Swal.fire({
            title: 'Berhasil!',
            text: 'Dokumen terpilih berhasil dipublish.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'swal2-confirm bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl px-4 py-2',
            },
        });

        location.reload();
    } catch {
        await Swal.fire({
            title: 'Gagal',
            text: 'Gagal mempublish dokumen terpilih.',
            icon: 'error',
            confirmButtonText: 'Tutup',
            customClass: {
                confirmButton: 'swal2-confirm bg-slate-900 hover:bg-slate-800 text-white rounded-2xl px-4 py-2',
            },
        });
    }
}
</script>
@endpush