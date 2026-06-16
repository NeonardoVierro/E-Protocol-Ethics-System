@extends('layouts.dashboard')

@section('title', 'Unggah Revisi')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    <div class="mb-6">
        <a href="{{ route('pengajuan.riwayat-pengajuan') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
            <span class="material-symbols-outlined text-base" data-icon="arrow_back">arrow_back</span>
            Kembali ke Riwayat Pengajuan
        </a>
    </div>
    <div class="bg-white rounded-xl border border-outline-variant p-8">
        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-slate-900">Unggah Revisi: {{ $proposal->title }}</h2>
        </div>

        @php $canUpload = $canUploadRevision; @endphp
        <div class="space-y-6">
            @if(!$canUpload)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 mb-4">
                    <p class="text-sm text-amber-700">
                        <span class="font-semibold">Menunggu review sekretaris:</span> Revisi Anda telah dikirim. Tunggu sekretaris untuk melakukan review. Jika diperlukan revisi lebih lanjut, sekretaris akan memberikan pemberitahuan.
                    </p>
                </div>
            @endif
            <form action="{{ route('pengajuan.riwayat-pengajuan.submit-revision', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-slate-700">Dokumen Proposal</h3>
                        @unless($canUpload)
                            <span class="text-xs font-medium uppercase tracking-[0.16em] text-amber-700">Upload dinonaktifkan</span>
                        @endunless
                    </div>
                    @foreach($files as $originalName => $fileGroup)
                        @php $latest = $fileGroup->first(); @endphp
                        <div class="mb-4 pb-4 border-b last:border-b-0">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $originalName }}</p>
                                    <p class="text-xs text-slate-500 mt-1">v{{ $latest->version }}
                                        @if($latest->file_type === 'revision')
                                            <span class="ml-2 inline-block px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-[10px] font-semibold">Revisi</span>
                                        @else
                                            <span class="ml-2 inline-block px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-semibold">Original</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('pengajuan.riwayat-pengajuan.revision-file.view', ['proposal' => $proposal->id, 'file' => $latest->id]) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                        <span class="material-symbols-outlined text-base">visibility</span>
                                        Lihat
                                    </a>
                                    <a href="{{ route('pengajuan.riwayat-pengajuan.revision-file.download', ['proposal' => $proposal->id, 'file' => $latest->id]) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                        <span class="material-symbols-outlined text-base">download</span>
                                        Download
                                    </a>
                                    @if($canUpload)
                                        <input type="file" name="revision_files[{{ $latest->id }}]" accept="application/pdf" class="text-sm border border-slate-300 rounded px-2 py-1" />
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-md text-sm font-medium hover:bg-amber-700 transition" @disabled(!$canUpload)>Upload Revisi</button>
                    </div>
                </div>
            </form>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Riwayat Revisi</h3>
                @if($revisions->isEmpty())
                    <div class="text-sm text-slate-500">Belum ada revisi yang dikirim.</div>
                @else
                    <ul class="space-y-2 text-sm">
                        @foreach($revisions as $r)
                            <li class="border p-3 rounded">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $r->file?->original_name ?? 'Revision File' }} - v{{ $r->file?->version ?? $r->revision_number + 1 }}</div>
                                    </div>
                                    <div class="text-xs text-slate-500">{{ $r->submitted_date?->toDateString() }}</div>
                                </div>
                                @if($r->file)
                                    <div class="mt-2 flex flex-wrap gap-2 items-center">
                                        <a href="{{ route('pengajuan.riwayat-pengajuan.revision-file.view', ['proposal' => $proposal->id, 'file' => $r->file->id]) }}" target="_blank" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200" title="Lihat dokumen revisi">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('pengajuan.riwayat-pengajuan.revision-file.download', ['proposal' => $proposal->id, 'file' => $r->file->id]) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200" title="Download dokumen revisi">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 10l5 5 5-5" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15V3" />
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Komentar Reviewer</h3>
                @if($feedbacks->isEmpty())
                    <div class="text-sm text-slate-500">Belum ada komentar reviewer.</div>
                @else
                    @php
                        $fields = [
                            ['label' => 'Autonomy', 'keys' => ['autonomy', 'autonomi', 'autonomy_feedback']],
                            ['label' => 'Beneficence', 'keys' => ['beneficence', 'benefit', 'beneficence_feedback']],
                            ['label' => 'Justice', 'keys' => ['justice', 'fairness', 'justice_feedback']],
                            ['label' => 'Komentar Umum', 'keys' => ['general_comments', 'comments', 'general_comment', 'catatan', 'comments_general']],
                        ];

                        $renderField = function ($parsed, $keys) {
                            foreach ($keys as $key) {
                                if (is_array($parsed) && array_key_exists($key, $parsed) && !empty($parsed[$key])) {
                                    $value = $parsed[$key];
                                    if (is_array($value)) {
                                        return implode("\n", array_map('strval', $value));
                                    }
                                    return (string) $value;
                                }
                            }
                            return '-';
                        };
                    @endphp

                    @foreach($feedbacks as $fb)
                        <div class="mb-3 border p-3 rounded bg-surface-container-low">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-semibold">Reviewer: {{ optional($fb->review->reviewer)->name ?? 'Reviewer' }}</div>
                                <div class="text-xs text-slate-500">{{ $fb->getRecommendationLabelAttribute() }}</div>
                            </div>

                            <div class="grid gap-3 lg:grid-cols-2 mt-4">
                                @foreach($fields as $field)
                                    <div class="rounded-2xl bg-white border border-slate-200 p-4 min-h-[80px]">
                                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-2">{{ $field['label'] }}</p>
                                        <p class="whitespace-pre-wrap text-sm text-slate-700">{{ $renderField($fb->parsed_feedback ?? [], $field['keys']) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Catatan Sekretaris untuk Revisi --}}
            @if($secretaryRevisionNotes->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <h3 class="text-sm font-semibold text-slate-700 mb-3">Catatan Sekretaris untuk Revisi</h3>
                    <div class="space-y-4">
                        @foreach($secretaryRevisionNotes as $note)
                            <div class="rounded-3xl border border-slate-200 bg-blue-50 shadow-sm overflow-hidden">
                                <div class="flex flex-col gap-2 p-4 sm:flex-row sm:items-center sm:justify-between bg-blue-100 border-b border-blue-200">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.16em] text-blue-600 mb-1">Catatan Sekretaris</p>
                                        <p class="text-sm text-blue-700">{{ optional($note->user)->name ?? 'Sekretaris' }}</p>
                                    </div>
                                    <div class="inline-flex items-center gap-2 text-xs text-blue-600">
                                        <span class="material-symbols-outlined text-sm">schedule</span>
                                        {{ $note->created_at->format('d M Y H:i') }}
                                    </div>
                                </div>
                                <div class="p-4">
                                    <p class="whitespace-pre-wrap text-sm text-slate-700">{{ $note->content }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
