@extends('layouts.sekretaris')

@section('title', 'Manajemen Proposal')
@section('page-title', 'Manajemen Proposal')
@section('breadcrumb', 'Cek Kelengkapan Dokumen')

@section('content')
<div class="mb-6">
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-100">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-1">Verifikasi Kelengkapan Dokumen</h2>
                <p class="text-sm text-gray-600">Periksa dan kelola status kelengkapan dokumen proposal dari peneliti</p>
            </div>
            <div class="text-3xl text-blue-400">
                <i class="fas fa-file-check"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    @if($proposals->isNotEmpty())
        <!-- Table Header -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID Proposal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Judul Proposal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status Dokumen</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal Pengajuan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($proposals as $proposal)
                    <tr class="hover:bg-blue-50 transition-colors duration-150">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700">
                                #{{ $proposal->id }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 max-w-xs">
                            <span title="{{ $proposal->title }}">{{ strlen($proposal->title) > 40 ? substr($proposal->title, 0, 40) . '...' : $proposal->title }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($proposal->document_status == 'lengkap')
                                <div class="inline-flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
                                        <i class="fas fa-check-circle text-xs"></i>
                                        Lengkap
                                    </span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                                        <i class="fas fa-exclamation-circle text-xs"></i>
                                        Kurang
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($proposal->submission_date)
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-calendar text-gray-400 text-xs"></i>
                                    {{ $proposal->submission_date->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button type="button" onclick="toggleDocuments(this, {{ $proposal->id }})" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-200 transition-colors duration-150">
                                    <i class="fas fa-files text-xs"></i>
                                    <span>{{ $proposal->files_count }} File{{ $proposal->files_count !== 1 ? 's' : '' }}</span>
                                </button>
                                <button onclick="featureInDevelopment('Approve proposal {{ $proposal->id }}')" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700 transition-colors duration-150">
                                    <i class="fas fa-arrow-right text-xs"></i>
                                    Lanjut Review
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Document Details Row -->
                    <tr id="doc-details-{{ $proposal->id }}" class="hidden bg-blue-50 border-t border-blue-100">
                        <td colspan="6" class="px-6 py-4">
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 mb-3">
                                    <i class="fas fa-folder-open text-blue-600"></i>
                                    <h4 class="font-semibold text-gray-800">Dokumen Terupload</h4>
                                </div>
                                @if($proposal->files_count > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($proposal->files as $file)
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 bg-white rounded-lg border border-blue-200">
                                                <div class="flex items-start gap-2 min-w-0">
                                                    <i class="fas fa-file-pdf text-red-500 mt-1 text-lg"></i>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-800 truncate" title="{{ $file->original_name }}">
                                                            {{ $file->original_name }}
                                                        </p>
                                                        <p class="text-xs text-gray-600">
                                                            {{ number_format($file->file_size / 1024, 1) }} KB · 
                                                            <span class="text-gray-500">v{{ $file->version }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ $file->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium hover:bg-slate-200 transition-colors duration-150">
                                                        <i class="fas fa-eye text-xs"></i>
                                                        Lihat
                                                    </a>
                                                    <a href="{{ $file->url }}" download class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700 transition-colors duration-150">
                                                        <i class="fas fa-download text-xs"></i>
                                                        Unduh
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-3 bg-white rounded-lg border border-red-200">
                                        <p class="text-sm text-red-700">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Tidak ada dokumen yang terupload
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <!-- Empty State -->
        <div class="py-12 px-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <i class="fas fa-inbox text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak Ada Proposal</h3>
            <p class="text-sm text-gray-600 mb-4">Belum ada proposal yang diajukan oleh peneliti.</p>
            <p class="text-xs text-gray-500">Proposal akan muncul di sini setelah peneliti mengajukannya melalui sistem.</p>
        </div>
    @endif
</div>

<!-- Info Box -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
    <div class="flex gap-3">
        <div class="flex-shrink-0 mt-0.5">
            <i class="fas fa-info-circle text-blue-600"></i>
        </div>
        <div class="text-sm text-blue-800">
            <p class="font-medium mb-1">Catatan Kelengkapan Dokumen:</p>
            <ul class="list-disc list-inside space-y-0.5 text-blue-700">
                <li><strong>Lengkap:</strong> Semua berkas proposal telah diupload oleh peneliti</li>
                <li><strong>Kurang:</strong> Masih ada berkas proposal yang belum diupload</li>
                <li>Klik tombol <strong>Files</strong> untuk melihat detail dokumen yang terupload</li>
            </ul>
        </div>
    </div>
</div>

<script>
function toggleDocuments(button, proposalId) {
    const detailsRow = document.getElementById('doc-details-' + proposalId);
    if (detailsRow.classList.contains('hidden')) {
        detailsRow.classList.remove('hidden');
        button.classList.add('bg-blue-200');
    } else {
        detailsRow.classList.add('hidden');
        button.classList.remove('bg-blue-200');
    }
}
</script>

@endsection