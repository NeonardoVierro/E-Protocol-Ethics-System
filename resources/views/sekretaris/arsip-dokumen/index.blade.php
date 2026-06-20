@extends('layouts.sekretaris')

@section('title', 'Arsip Dokumen')
@section('page-title', 'Arsip Dokumen')
@section('breadcrumb', 'Semua Dokumen Tersimpan')

@section('content')
<div>
    <div class="max-w-container-max mx-auto">
        <div class="flex flex-col gap-2 mb-6">
            <h1 class="font-h1 text-h1 text-blue-900">Arsip Dokumen</h1>
            <p class="font-body-lg text-body-lg text-slate-600 max-w-2xl">Pusat penyimpanan dokumen sertifikat dan proposal yang telah diproses dalam sistem manajemen etik.</p>
        </div>

        <!-- Filter & Search Section -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-1">
                    <label class="block font-label-caps text-label-caps text-slate-500 mb-2 uppercase">Cari Dokumen</label>
                    <div class="relative">
                        <input class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary" placeholder="ID, Judul, Peneliti..." type="text"/>
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" data-icon="search">search</span>
                    </div>
                </div>
                <div>
                    <label class="block font-label-caps text-label-caps text-slate-500 mb-2 uppercase">Tahun</label>
                    <select class="w-full py-2 px-3 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        <option>Semua Tahun</option>
                        <option>2024</option>
                        <option>2023</option>
                        <option>2022</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-caps text-label-caps text-slate-500 mb-2 uppercase">Kategori Penelitian</label>
                    <select class="w-full py-2 px-3 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        <option>Semua Kategori</option>
                        <option>Kesehatan Masyarakat</option>
                        <option>Uji Klinis</option>
                        <option>Ilmu Sosial</option>
                        <option>Genetika</option>
                    </select>
                </div>
                <div>
                    <label class="block font-label-caps text-label-caps text-slate-500 mb-2 uppercase">Status</label>
                    <select class="w-full py-2 px-3 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        <option>Semua Status</option>
                        <option>APPROVED</option>
                        <option>REJECTED</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Documents Table Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 font-label-caps text-label-caps text-slate-600 uppercase">ID Dokumen</th>
                            <th class="px-6 py-4 font-label-caps text-label-caps text-slate-600 uppercase">Judul Penelitian</th>
                            <th class="px-6 py-4 font-label-caps text-label-caps text-slate-600 uppercase">Peneliti</th>
                            <th class="px-6 py-4 font-label-caps text-label-caps text-slate-600 uppercase">Tanggal Terbit</th>
                            <th class="px-6 py-4 font-label-caps text-label-caps text-slate-600 uppercase">Status</th>
                            <th class="px-6 py-4 font-label-caps text-label-caps text-slate-600 uppercase text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($documents ?? [] as $d)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 font-body-sm text-blue-700 font-semibold">{{ $d->document_number ?: ('EC-'.str_pad($d->id,4,'0',STR_PAD_LEFT)) }}</td>
                            <td class="px-6 py-4">
                                <p class="font-body-md font-semibold text-slate-900">{{ Str::limit($d->proposal?->title ?? $d->original_name ?? '-', 120) }}</p>
                                <p class="text-xs text-slate-500">{{ $d->proposal?->category ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 font-body-md text-slate-700">{{ $d->proposal?->nama_peneliti ?? $d->proposal?->researcher?->name ?? '-' }}</td>
                            <td class="px-6 py-4 font-body-sm text-slate-600">{{ $d->created_at?->format('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($d->status === \App\Models\EthicsDocument::STATUS_DRAFT)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 uppercase tracking-tight">DRAFT</span>
                                @elseif($d->status === \App\Models\EthicsDocument::STATUS_PUBLISHED)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 uppercase tracking-tight">APPROVED</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 uppercase tracking-tight">{{ strtoupper($d->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="#" class="p-2 text-slate-400 hover:text-blue-700 hover:bg-blue-50 rounded transition-all" title="Download Sertifikat">
                                        <span class="material-symbols-outlined" data-icon="verified">verified</span>
                                    </a>
                                    <a href="#" class="p-2 text-slate-400 hover:text-blue-700 hover:bg-blue-50 rounded transition-all" title="Download Proposal">
                                        <span class="material-symbols-outlined" data-icon="download">download</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada dokumen arsip.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination Footer -->
            @if(isset($documents) && $documents->total() > 0)
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col md:flex-row items-center justify-between gap-3">
                <p class="text-sm text-slate-500">Showing <span class="font-semibold text-slate-900">{{ $documents->firstItem() ?? 0 }}</span> to <span class="font-semibold text-slate-900">{{ $documents->lastItem() ?? 0 }}</span> of <span class="font-semibold text-slate-900">{{ $documents->total() ?? 0 }}</span> results</p>
                @if($documents->total() > 10)
                <div>
                    {{ $documents->links() }}
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Visual Graphic / Insight Card (Bento Style) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="md:col-span-2 relative h-48 rounded-xl overflow-hidden shadow-sm group">
                <img alt="Modern office architecture" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC506TzQAA_qpUmu2Rbj8ZDTP0lNX-VyDnY-6Srd_DWMaJvB9KK3slVWRh-UmOOJvo6KcPfJxmikafs6oZiqMIpONw7lP2S8ZaqZS3JJ933JJpeCRUjKONmfCEJkNHa7MruPnLMcJlm8H1_OkS8zcXndoOR_ipDh403Ypo803oJ9qt76gaAJaPz3Yc13Bd3mfi2wLCeRaMPxTEOukK-RRwvIfCg5de9dG479LmLbtGFaFaNMBpnrOmBhGYCx7qxdfx0kMfYu1D-YXg9"/>
                <div class="absolute inset-0 bg-gradient-to-r from-blue-900/80 to-transparent flex flex-col justify-center px-8 text-white">
                    <h3 class="text-xl font-bold mb-2">Penyimpanan Terpusat</h3>
                    <p class="max-w-md text-sm text-blue-100 opacity-90 leading-relaxed">Seluruh arsip terintegrasi dengan tanda tangan digital tersertifikasi untuk keamanan data penelitian tingkat tinggi.</p>
                </div>
            </div>
            <div class="bg-blue-900 rounded-xl p-6 shadow-sm flex flex-col justify-between text-white overflow-hidden relative">
                <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-9xl text-white/10 rotate-12">history_edu</span>
                <div>
                    <p class="text-blue-300 text-xs font-bold uppercase tracking-widest mb-1">Total Dokumen</p>
                    <h4 class="text-4xl font-black">{{ $documents->count() ?? 0 }}</h4>
                </div>
                <div class="flex items-center gap-2 text-green-400">
                    <span class="material-symbols-outlined text-sm">trending_up</span>
                    <span class="text-xs font-bold">+12% dari bulan lalu</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection