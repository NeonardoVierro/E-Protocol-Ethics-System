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

        @php
            $canUpload = $canUploadRevision;
            $statusMessage = null;

            if (isset($revisionUploadState)) {
                if ($revisionUploadState === 'submitted') {
                    $statusMessage = 'Revisi Anda telah dikirim dan sedang menunggu review sekretaris. Silakan tunggu pemberitahuan selanjutnya.';
                } elseif ($revisionUploadState === 'requested') {
                    $statusMessage = 'Sekretaris telah meminta revisi. Silakan unggah dokumen revisi Anda di bawah.';
                } else {
                    $statusMessage = 'Belum ada permintaan revisi dari sekretaris. Tunggu pemberitahuan jika sekretaris meminta revisi lebih lanjut.';
                }
            }
        @endphp

        <div class="space-y-6">
            @if($statusMessage)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 mb-4">
                    <p class="text-sm text-amber-700">
                        {{ $statusMessage }}
                    </p>
                </div>
            @endif

            @if($originalFiles->isNotEmpty())
                <form action="{{ route('pengajuan.riwayat-pengajuan.submit-revision', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <h3 class="text-sm font-semibold text-slate-700 mb-4">File Proposal Awal</h3>
                        <div class="space-y-3">
                            @foreach($originalFiles as $target)
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $target->group_name ?? $target->original_name }}</p>
                                        <p class="text-xs text-slate-500">v{{ $target->version }} @if($target->file_type === \App\Models\ProposalFile::TYPE_PROPOSAL)<span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 px-2 py-0.5 text-[10px] font-semibold ml-2">Original</span>@endif</p>
                                    </div>
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a href="{{ route('pengajuan.riwayat-pengajuan.revision-file.view', ['proposal' => $proposal->id, 'file' => $target->id]) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                                <span class="material-symbols-outlined text-base">visibility</span>
                                                Lihat
                                            </a>
                                            <a href="{{ route('pengajuan.riwayat-pengajuan.revision-file.download', ['proposal' => $proposal->id, 'file' => $target->id]) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                                <span class="material-symbols-outlined text-base">download</span>
                                                Download
                                            </a>
                                        </div>
                                        @if($canUpload)
                                            <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Unggah Revisi</label>
                                            <input type="file" name="revision_files[{{ $target->id }}]" accept="application/pdf" class="text-sm border border-slate-300 rounded px-2 py-1" />
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($canUpload)
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-md text-sm font-medium hover:bg-amber-700 transition">Upload Revisi</button>
                            </div>
                        @endif
                    </div>
                </form>
            @elseif(!$statusMessage)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-slate-600">
                    Anda hanya dapat mengunggah revisi setelah sekretaris mengirim permintaan revisi. Silakan kembali nanti.
                </div>
            @endif

            @if($revisionFiles->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <h3 class="text-sm font-semibold text-slate-700 mb-3">File Revisi Terkirim</h3>
                    <div class="space-y-3">
                        @foreach($revisionFiles as $target)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $target->group_name ?? $target->original_name }}</p>
                                    <p class="text-xs text-slate-500">v{{ $target->version }} - {{ $target->getTypeLabelAttribute() }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('pengajuan.riwayat-pengajuan.revision-file.view', ['proposal' => $proposal->id, 'file' => $target->id]) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                        <span class="material-symbols-outlined text-base">visibility</span>
                                        Lihat
                                    </a>
                                    <a href="{{ route('pengajuan.riwayat-pengajuan.revision-file.download', ['proposal' => $proposal->id, 'file' => $target->id]) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                        <span class="material-symbols-outlined text-base">download</span>
                                        Download
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($revisions->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <h3 class="text-sm font-semibold text-slate-700 mb-3">Riwayat Revisi</h3>
                    <div class="text-sm text-slate-500">Revisi sudah dikirim tetapi file revisi belum tersedia.</div>
                </div>
            @endif

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
                            ['label' => 'Komentar Umum', 'keys' => ['general_comments', 'comments', 'general_comment', 'catatan', 'comments_general', 'summary']],
                        ];

                        $renderField = function ($parsed, $keys) {
                            if (is_string($parsed) && trim($parsed) !== '') {
                                return $parsed;
                            }

                            if (is_array($parsed)) {
                                foreach ($keys as $key) {
                                    if (array_key_exists($key, $parsed) && !empty($parsed[$key])) {
                                        $value = $parsed[$key];
                                        if (is_array($value)) {
                                            return implode("\n", array_map('strval', $value));
                                        }
                                        return (string) $value;
                                    }
                                }

                                if (array_key_exists('summary', $parsed) && trim((string) $parsed['summary']) !== '') {
                                    return (string) $parsed['summary'];
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
                                        <span class="timestamp" data-timestamp="{{ $note->created_at->toIso8601String() }}">{{ $note->created_at->format('d M Y H:i') }}</span>
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

@push('scripts')
<script>
    function formatRelativeTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        if (seconds < 60) {
            return 'baru saja';
        }
        
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) {
            return minutes === 1 ? '1 menit yang lalu' : minutes + ' menit yang lalu';
        }
        
        const hours = Math.floor(minutes / 60);
        if (hours < 24) {
            return hours === 1 ? '1 jam yang lalu' : hours + ' jam yang lalu';
        }
        
        const days = Math.floor(hours / 24);
        if (days < 7) {
            return days === 1 ? '1 hari yang lalu' : days + ' hari yang lalu';
        }
        
        // For older dates, show the full date format
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const day = date.getDate();
        const month = monthNames[date.getMonth()];
        const year = date.getFullYear();
        const hours24 = String(date.getHours()).padStart(2, '0');
        const mins = String(date.getMinutes()).padStart(2, '0');
        
        return `${day} ${month} ${year} ${hours24}:${mins}`;
    }
    
    function updateTimestamps() {
        document.querySelectorAll('.timestamp').forEach(el => {
            const timestamp = el.getAttribute('data-timestamp');
            if (timestamp) {
                el.textContent = formatRelativeTime(timestamp);
            }
        });
    }
    
    // Update timestamps immediately on page load
    document.addEventListener('DOMContentLoaded', updateTimestamps);
    
    // Update timestamps every minute
    setInterval(updateTimestamps, 60000);
</script>
@endpush
