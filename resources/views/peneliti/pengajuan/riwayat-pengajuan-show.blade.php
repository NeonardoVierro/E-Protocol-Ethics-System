@extends('layouts.dashboard')

@section('title', 'Detail Feedback Reviewer')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    <div class="bg-white rounded-xl border border-outline-variant p-8">
        <a href="{{ route('pengajuan.riwayat-pengajuan') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 mb-6">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke Riwayat Pengajuan
        </a>

        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-slate-900">Detail Feedback Reviewer</h1>
            <p class="text-slate-500 mt-1">Lihat komentar reviewer dan rekomendasi revisi untuk proposal Anda.</p>
        </div>

        @if(isset($proposal->pendingRevisionRequest) && $proposal->pendingRevisionRequest)
            <div class="mb-6">
                <a href="{{ route('pengajuan.riwayat-pengajuan.revision', $proposal->id) }}" class="inline-flex items-center gap-2 rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 transition">
                    <span class="material-symbols-outlined">edit_square</span>
                    Revisi
                </a>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3 mb-8">
            <div class="rounded-2xl bg-surface-container-low p-6 border border-slate-200">
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-2">Judul Proposal</p>
                <p class="text-base font-semibold text-slate-900">{{ $proposal->title }}</p>
            </div>
            <div class="rounded-2xl bg-surface-container-low p-6 border border-slate-200">
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-2">Tanggal Pengajuan</p>
                <p class="text-base font-semibold text-slate-900">{{ optional($proposal->submission_date)->format('d M Y') ?? '-' }}</p>
            </div>
            @php
                $proposalStatusLabel = $proposal->status === \App\Models\Proposal::STATUS_IN_PROCESS ? 'On Review' : $proposal->status_label;
                $proposalStatusBadge = $proposal->status === \App\Models\Proposal::STATUS_IN_PROCESS ? 'bg-yellow-100 text-yellow-800' : $proposal->status_badge;
            @endphp
            <div class="rounded-2xl bg-surface-container-low p-6 border border-slate-200">
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-2">Status</p>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $proposalStatusBadge }}">
                    {{ $proposalStatusLabel }}
                </span>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3 mb-10">
            <div class="rounded-2xl bg-surface-container-low p-6 border border-slate-200">
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-2">Reviewer Approved</p>
                <p class="text-2xl font-semibold text-slate-900">{{ $summary['approved'] }}</p>
            </div>
            <div class="rounded-2xl bg-surface-container-low p-6 border border-slate-200">
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-2">Reviewer Revisi</p>
                <p class="text-2xl font-semibold text-slate-900">{{ $summary['revision'] }}</p>
            </div>
            <div class="rounded-2xl bg-surface-container-low p-6 border border-slate-200">
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-2">Reviewer Ditolak</p>
                <p class="text-2xl font-semibold text-slate-900">{{ $summary['rejected'] }}</p>
            </div>
        </div>

        {{-- Catatan dari Sekretaris --}}
        @if($secretaryNotes->isNotEmpty())
            <div class="mb-10">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Catatan dari Sekretaris</h3>
                <div class="space-y-4">
                    @foreach($secretaryNotes as $note)
                        <div class="rounded-3xl border border-slate-200 bg-blue-50 shadow-sm overflow-hidden">
                            <div class="flex flex-col gap-2 p-6 sm:flex-row sm:items-center sm:justify-between bg-blue-100 border-b border-blue-200">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.16em] text-blue-600 mb-1">Dari Sekretaris</p>
                                    <p class="text-sm text-blue-700">{{ optional($note->user)->name ?? 'Sekretaris' }}</p>
                                </div>
                                <div class="inline-flex items-center gap-2 text-xs text-blue-600">
                                    <span class="material-symbols-outlined text-sm">schedule</span>
                                    {{ $note->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            <div class="p-6">
                                <p class="whitespace-pre-wrap text-sm text-slate-700">{{ $note->content }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($feedbacks->isEmpty())
            <div class="rounded-2xl border border-slate-200 bg-surface-container-low p-10 text-center">
                <p class="text-slate-600">Belum ada feedback reviewer yang tersedia untuk proposal ini.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($feedbacks as $fb)
                    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between bg-surface-container-low border-b border-slate-200">
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-2">Reviewer</p>
                                <p class="text-lg font-semibold text-slate-900">{{ optional($fb->review->reviewer)->name ?? 'Reviewer' }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-sm text-slate-500">{{ optional($fb->review->reviewer)->email ?? '' }}</p>
                                    <span class="text-[10px] px-2 py-0.5 rounded font-bold {{ $fb->getReviewTypeBadgeClasses() }}">{{ $fb->getReviewTypeLabel() }}</span>
                                </div>
                            </div>
                            <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700">
                                <span class="h-2.5 w-2.5 rounded-full {{ $fb->recommendation === 'approved' ? 'bg-emerald-500' : ($fb->recommendation === 'revision' ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                                {{ $fb->getRecommendationLabelAttribute() }}
                            </div>
                        </div>
                        <div class="grid gap-4 p-6 sm:grid-cols-2">
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

                            @foreach($fields as $field)
                                <div class="rounded-2xl border border-slate-200 bg-surface-container-low p-5 min-h-[160px]">
                                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-3">{{ $field['label'] }}</p>
                                    <p class="whitespace-pre-wrap text-sm text-slate-700">{{ $renderField($fb->parsed_feedback, $field['keys']) }}</p>
                                </div>
                            @endforeach
                        </div>

                        @if($fb->file_path)
                            <div class="border-t border-slate-200 bg-surface-container-low p-6">
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-3">Lampiran Reviewer</p>
                                <div class="flex flex-wrap gap-3 items-center">
                                    <div class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                                        <span class="material-symbols-outlined text-base">description</span>
                                        {{ $fb->original_name ?? 'Lampiran Reviewer' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection