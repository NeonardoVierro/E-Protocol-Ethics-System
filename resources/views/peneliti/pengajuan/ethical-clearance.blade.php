@extends('layouts.dashboard')

@section('title', 'Ethical Clearance')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    <div class="bg-white rounded-xl border border-outline-variant p-8">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-slate-900">Ethical Clearance</h1>
            <p class="text-slate-500 mt-1">Lihat draft dokumen ethical clearance yang telah diterbitkan oleh admin untuk proposal Anda.</p>
        </div>

        @if($documents->isEmpty())
            <div class="rounded-3xl border border-slate-200 bg-surface-container-low p-10 text-center">
                <span class="material-symbols-outlined text-primary text-5xl mb-4">badge</span>
                <p class="text-slate-700 text-lg font-semibold mb-2">Belum ada draft sertifikat</p>
                <p class="text-slate-500">Draft sertifikat akan muncul di halaman ini setelah admin menyimpan assignment untuk proposal Anda.</p>
            </div>
        @else
            <div class="space-y-8">
                @foreach($documents as $document)
                    @php
                        $notes = [];
                        if (!empty($document->notes)) {
                            $decoded = json_decode($document->notes, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $notes = $decoded;
                            } else {
                                $notes = ['notes' => (string) $document->notes];
                            }
                        }
                    @endphp

                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-2">Draft Sertifikat</p>
                                <h2 class="text-xl font-semibold text-slate-900">{{ $document->proposal->title ?? 'Proposal tidak tersedia' }}</h2>
                                <p class="text-sm text-slate-500 mt-2">Nomor EC: <span class="font-medium text-slate-900">{{ $document->document_number ?? '-' }}</span></p>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $document->status_badge }}">
                                    {{ $document->status_label }}
                                </span>
                                @if($document->proposal)
                                    <a href="{{ route('pengajuan.riwayat-pengajuan.show', $document->proposal->id) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Lihat Proposal</a>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6">
                            <p class="text-sm text-slate-500 mb-4">Ini adalah draft sertifikat yang disiapkan oleh admin. Silakan periksa semua data berikut sebelum melakukan konfirmasi.</p>

                            <div class="grid gap-4 lg:grid-cols-2">
                                <div class="rounded-3xl border border-slate-200 bg-surface-container-low p-5">
                                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-3">Detail Sertifikat</p>
                                    <div class="space-y-3 text-sm text-slate-700">
                                        <div>
                                            <p class="font-semibold text-slate-900">Peneliti Utama</p>
                                            <p>{{ $notes['principal_investigator'] ?? optional($document->proposal->researcher)->name ?? $document->proposal->nama_peneliti ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">Anggota Peneliti</p>
                                            <p class="whitespace-pre-wrap">{{ $notes['members'] ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">Institusi</p>
                                            <p>{{ $notes['institution'] ?? $document->proposal->asal_instansi ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">Tempat Penelitian</p>
                                            <p>{{ $notes['research_place'] ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="rounded-3xl border border-slate-200 bg-surface-container-low p-5">
                                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-3">Catatan Admin</p>
                                    <p class="whitespace-pre-wrap text-sm text-slate-700">{{ $notes['notes'] ?? 'Tidak ada catatan tambahan dari admin.' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-1">Tanggal Draft</p>
                                <p class="text-sm text-slate-700">{{ isset($notes['assigned_at']) ? \Illuminate\Support\Carbon::parse($notes['assigned_at'])->format('d M Y H:i') : '-' }}</p>
                            </div>
                            @if($document->proposal && $document->proposal->status === \App\Models\Proposal::STATUS_WAITING_FOR_CONFIRMATION)
                                <a href="{{ route('pengajuan.ethical-clearance.confirm', $document->proposal->id) }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 transition">Tinjau dan Konfirmasi</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
