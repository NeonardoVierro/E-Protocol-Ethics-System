@extends('layouts.sekretaris')

@section('title', 'Detail Hasil Review')
@section('page-title', 'Detail Hasil Review')
@section('breadcrumb', '.')

@section('content')

<!-- Inline Tailwind + fonts for Sekretaris Hasil Review preview -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-secondary-container": "#006f66",
                        "on-primary-fixed-variant": "#00429c",
                        "tertiary-fixed-dim": "#ffb596",
                        "surface-bright": "#f8f9ff",
                        "inverse-surface": "#213145",
                        "surface-variant": "#d3e4fe",
                        "on-error-container": "#93000a",
                        "primary": "#003178",
                        "primary-container": "#0d47a1",
                        "error-container": "#ffdad6",
                        "surface-container-highest": "#d3e4fe",
                        "secondary-fixed": "#84f5e8",
                        "primary-fixed-dim": "#b0c6ff",
                        "outline-variant": "#c3c6d4",
                        "on-secondary-fixed-variant": "#005049",
                        "on-tertiary-container": "#ffa781",
                        "on-primary": "#ffffff",
                        "tertiary-fixed": "#ffdbcd",
                        "tertiary-container": "#853100",
                        "on-secondary-fixed": "#00201d",
                        "secondary-container": "#81f3e5",
                        "on-error": "#ffffff",
                        "surface-tint": "#2b5bb5",
                        "surface-container": "#e5eeff",
                        "on-primary-container": "#a1bbff",
                        "secondary-fixed-dim": "#66d9cc",
                        "on-tertiary": "#ffffff",
                        "surface-dim": "#cbdbf5",
                        "surface": "#f8f9ff",
                        "on-tertiary-fixed-variant": "#7d2d00",
                        "background": "#f8f9ff",
                        "surface-container-low": "#eff4ff",
                        "on-surface-variant": "#434652",
                        "on-primary-fixed": "#001945",
                        "on-tertiary-fixed": "#360f00",
                        "on-background": "#0b1c30",
                        "surface-container-high": "#dce9ff",
                        "primary-fixed": "#d9e2ff",
                        "inverse-primary": "#b0c6ff",
                        "on-secondary": "#ffffff",
                        "outline": "#737783",
                        "error": "#ba1a1a",
                        "inverse-on-surface": "#eaf1ff",
                        "tertiary": "#602100",
                        "surface-container-lowest": "#ffffff",
                        "secondary": "#006a62",
                        "on-surface": "#0b1c30"
                    },
                    borderRadius: { DEFAULT: '0.125rem', lg: '0.25rem', xl: '0.5rem', full: '0.75rem' },
                    spacing: { xs: '8px', sm: '12px', base: '4px', md: '16px', xl: '32px', lg: '24px', 'container-max': '1440px', gutter: '24px' },
                    fontFamily: { h2: ['Inter'], h3: ['Inter'], h1: ['Inter'], 'body-sm': ['Inter'], 'label-caps': ['Inter'], 'body-md': ['Inter'], 'body-lg': ['Inter'], button: ['Inter'] },
                    fontSize: { h2: ['24px', { lineHeight: '32px', letterSpacing: '-0.01em', fontWeight: '600' }], h3: ['20px', { lineHeight: '28px', fontWeight: '600' }], h1: ['30px', { lineHeight: '38px', letterSpacing: '-0.02em', fontWeight: '700' }], 'body-sm': ['12px', { lineHeight: '16px', fontWeight: '400' }], 'label-caps': ['12px', { lineHeight: '16px', letterSpacing: '0.05em', fontWeight: '600' }], 'body-md': ['14px', { lineHeight: '20px', fontWeight: '400' }], 'body-lg': ['16px', { lineHeight: '24px', fontWeight: '400' }], button: ['14px', { lineHeight: '20px', fontWeight: '500' }] }
                }
            }
        }
    </script>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
    body { font-family: 'Inter', sans-serif; }
</style>

<main class="p-lg flex-1 overflow-auto">
    <div class="mb-lg">
        <nav class="flex text-body-sm font-body-sm text-slate-500 mb-xs">
            <a href="{{ route('sekretaris.dashboard') }}" class="hover:text-primary">Dashboard</a>
            <span class="mx-2">/</span>
            <a href="{{ route('sekretaris.hasil-review') }}" class="hover:text-primary">Hasil Review</a>
            <span class="mx-2">/</span>
            <span class="text-on-background font-medium">Detail</span>
        </nav>
    </div>

    <div class="grid grid-cols-12 gap-lg h-full">
        <!-- Left Column: Reviewer Summary -->
        <div class="col-span-12 lg:col-span-4 flex flex-col gap-md">
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                @php
                    use App\Models\ProposalAssignment;
                    $assignments = $proposal->assignments()->where('role', ProposalAssignment::ROLE_REVIEWER)->with('assignedTo')->get();
                    $assignedCount = $assignments->count();
                    // Count all submitted feedback entries (including multiple rounds)
                    $submittedCount = $feedbacks->count();
                    $allSubmitted = $assignedCount > 0 && $submittedCount >= $assignedCount;
                @endphp

                <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Daftar Reviewer</p>
                            <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ $assignedCount }} Reviewer</h3>
                        </div>
                        <div class="text-right">
                            <div class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Terkirim</div>
                            <div class="mt-1 text-xl font-semibold {{ $allSubmitted ? 'text-emerald-600' : 'text-amber-600' }}">{{ $submittedCount }} / {{ $assignedCount ?: 0 }}</div>
                        </div>
                    </div>
                </div>

                    <div class="divide-y divide-slate-200">
                    @foreach($feedbacks as $fbItem)
                        @php
                            $reviewer = optional($fbItem->review->reviewer);
                            $statusLabel = $fbItem ? $fbItem->getRecommendationLabelAttribute() : 'Menunggu';
                            $statusClasses = $fbItem
                                ? ($fbItem->recommendation === 'approved' ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : ($fbItem->recommendation === 'revision' ? 'bg-amber-100 text-amber-700 border border-amber-300' : 'bg-red-100 text-red-700 border border-red-300'))
                                : 'bg-slate-100 text-slate-600 border border-slate-300';
                            $avatarBg = $fbItem 
                                ? ($fbItem->recommendation === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($fbItem->recommendation === 'revision' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700'))
                                : 'bg-slate-100 text-slate-700';
                        @endphp

                        <div class="px-6 py-4 hover:bg-blue-50 transition-all duration-200 cursor-pointer border-l-4 {{ $fbItem ? ($fbItem->recommendation === 'approved' ? 'border-l-emerald-500' : ($fbItem->recommendation === 'revision' ? 'border-l-amber-500' : 'border-l-red-500')) : 'border-l-slate-300' }}">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-11 h-11 rounded-full {{ $avatarBg }} flex items-center justify-center font-semibold shadow-sm">{{ strtoupper(substr($reviewer->name ?? 'RV', 0, 1)) }}</div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold text-slate-900 truncate">{{ $reviewer->name ?? 'Reviewer' }}</div>
                                        <div class="text-xs text-slate-500 truncate">{{ $reviewer->email ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Dikirim</div>
                                    <div class="mt-1 text-sm font-medium text-emerald-600">{{ optional($fbItem->submitted_at)->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-4">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $statusClasses }}">{{ $statusLabel }}</span>
                                <span class="text-xs text-slate-500">{{ $fbItem->getReviewTypeLabel() ?? 'Tidak diketahui' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column: Detail View -->
        <div class="col-span-12 lg:col-span-8 space-y-lg">
            <div class="bg-white border border-slate-200 rounded-3xl p-lg shadow-sm">
                <div class="flex flex-col gap-4">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">ID PROPOSAL: {{ $proposal->nomor_ec ?? ('#' . $proposal->id) }}</span>
                        <h3 class="mt-3 text-2xl font-bold text-slate-900">{{ $proposal->title }}</h3>
                        <p class="mt-2 text-sm text-slate-500">Dikirim: {{ optional($proposal->submission_date)->format('d M Y') ?? '-' }}</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <div class="rounded-3xl bg-gradient-to-br from-blue-50 to-blue-100 p-4 border border-blue-200 shadow-sm">
                            <div class="text-xs uppercase tracking-[0.18em] text-blue-600 font-semibold">Peneliti</div>
                            <div class="mt-3 font-semibold text-slate-900">{{ optional($proposal->researcher)->name ?? 'Peneliti' }}</div>
                            <div class="mt-1 text-sm text-slate-600">{{ optional($proposal->researcher)->email ?? optional($proposal)->asal_instansi ?? '-' }}</div>
                        </div>
                        <div class="rounded-3xl bg-gradient-to-br from-purple-50 to-purple-100 p-4 border border-purple-200 shadow-sm">
                            <div class="text-xs uppercase tracking-[0.18em] text-purple-600 font-semibold">Reviewer Ditugaskan</div>
                            <div class="mt-3 text-lg font-semibold text-purple-900">{{ $assignedCount }}</div>
                            <div class="mt-1 text-sm text-slate-600">Total reviewer terdaftar</div>
                        </div>
                        <div class="rounded-3xl bg-gradient-to-br from-emerald-50 to-emerald-100 p-4 border border-emerald-200 shadow-sm">
                            <div class="text-xs uppercase tracking-[0.18em] text-emerald-600 font-semibold">Progress Review</div>
                            <div class="mt-3 text-lg font-semibold {{ $allSubmitted ? 'text-emerald-700' : 'text-amber-700' }}">{{ $submittedCount }} / {{ $assignedCount ?: 0 }}</div>
                            <div class="mt-1 text-sm text-slate-600">Umpan balik diterima</div>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Status Reviewer</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($feedbacks as $fbItem)
                            @php
                                $reviewer = optional($fbItem->review->reviewer);
                                $statusBadge = $fbItem ? ($fbItem->recommendation === 'approved' ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : ($fbItem->recommendation === 'revision' ? 'bg-amber-100 text-amber-700 border border-amber-300' : 'bg-red-100 text-red-700 border border-red-300')) : 'bg-slate-100 text-slate-600 border border-slate-300';
                                $statusText = $fbItem ? $fbItem->getRecommendationLabelAttribute() : 'Menunggu Review';
                                $borderColor = $fbItem ? ($fbItem->recommendation === 'approved' ? 'border-l-emerald-500' : ($fbItem->recommendation === 'revision' ? 'border-l-amber-500' : 'border-l-red-500')) : 'border-l-slate-300';
                                $hoverBg = $fbItem ? ($fbItem->recommendation === 'approved' ? 'hover:bg-emerald-50' : ($fbItem->recommendation === 'revision' ? 'hover:bg-amber-50' : 'hover:bg-red-50')) : 'hover:bg-slate-50';
                            @endphp

                            <div class="rounded-3xl bg-slate-50 border border-slate-200 border-l-4 {{ $borderColor }} p-4 shadow-sm {{ $hoverBg }} transition-all duration-200">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 rounded-full {{ $fbItem ? ($fbItem->recommendation === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($fbItem->recommendation === 'revision' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')) : 'bg-slate-200 text-slate-700' }} flex items-center justify-center font-semibold shadow-sm">{{ strtoupper(substr($reviewer->name ?? 'RV', 0, 1)) }}</div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900 truncate">{{ $reviewer->name ?? 'Reviewer' }}</div>
                                        <div class="mt-1 text-xs text-slate-500 truncate">{{ $reviewer->email ?? '-' }}</div>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center justify-between gap-3">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $statusBadge }}">{{ $statusText }}</span>
                                    <span class="timestamp" data-timestamp="{{ $fbItem->submitted_at->toIso8601String() }}">{{ optional($fbItem->submitted_at)->format('d M Y') }}</span>
                                </div>

                                <p class="mt-3 text-sm text-slate-600">{{ $fbItem->review_type_label ?? 'Reviewer Awal' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-md">
                <h4 class="text-h3 font-h3 text-slate-900">Umpan Balik Reviewer</h4>
                @foreach($feedbacks as $fb)
                    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                        <div class="p-lg bg-slate-50 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600">{{ strtoupper(substr(optional($fb->review->reviewer)->name ?? 'R',0,1)) }}</div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ optional($fb->review->reviewer)->name ?? 'Reviewer' }}</p>
                                    <p class="text-sm text-slate-500">{{ optional($fb->review->reviewer)->roles()->pluck('name')->first() ?? 'Reviewer' }} • {{ optional($fb->submitted_at)->format('d M Y') }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full px-4 py-1.5 text-xs font-bold uppercase {{ $fb->recommendation === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($fb->recommendation === 'revision' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">{{ $fb->getRecommendationLabelAttribute() }}</span>
                        </div>
                        <div class="p-lg">
                            @php
                                $pf = $fb->parsed_feedback ?? null;
                                $renderField = function ($pf, $candidates) {
                                    if (! $pf) return '-';
                                    foreach ($candidates as $key) {
                                        if (is_array($pf) && array_key_exists($key, $pf) && !empty($pf[$key])) {
                                            $val = $pf[$key];
                                            if (is_array($val)) return implode("\n", array_map('strval', $val));
                                            return (string) $val;
                                        }
                                    }
                                    if (is_array($pf)) {
                                        return collect($pf)->map(function($v, $k){
                                            if (is_array($v)) $v = implode(', ', $v);
                                            return trim($k . ': ' . $v);
                                        })->values()->implode("\n");
                                    }
                                    return is_string($pf) ? $pf : '-';
                                };

                                $fields = [
                                    ['label' => 'Autonomy', 'keys' => ['autonomy', 'autonomi', 'autonomy_feedback']],
                                    ['label' => 'Beneficence', 'keys' => ['beneficence', 'benefit', 'beneficence_feedback']],
                                    ['label' => 'Justice', 'keys' => ['justice', 'fairness', 'justice_feedback']],
                                    ['label' => 'General Comments', 'keys' => ['general_comments', 'comments', 'general_comment', 'catatan', 'comments_general']],
                                ];
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                                @foreach($fields as $f)
                                    <div class="bg-slate-50 border border-slate-200 rounded-3xl p-md">
                                        <p class="text-label-caps font-label-caps text-slate-500 uppercase mb-2">{{ $f['label'] }}</p>
                                        <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap break-words">{{ $renderField($pf, $f['keys']) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if($fb->file_path)
                            <div class="px-lg pb-lg">
                                <p class="text-[10px] uppercase tracking-[0.18em] text-slate-400 mb-2">Lampiran Reviewer</p>
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2 rounded-3xl text-sm text-slate-600">
                                        <span class="material-symbols-outlined text-sm">description</span>
                                        {{ $fb->original_name ?? 'Lampiran' }}
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('sekretaris.review-feedback.file.view', $fb) }}" target="_blank" class="inline-flex items-center gap-2 rounded-3xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                            Lihat
                                        </a>
                                        <a href="{{ route('sekretaris.review-feedback.file.download', $fb) }}" class="inline-flex items-center gap-2 rounded-3xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition">
                                            <span class="material-symbols-outlined text-sm">download</span>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl p-lg shadow-sm">
                <h4 class="text-h3 font-h3 text-slate-900 mb-md">Revisi Peneliti</h4>
                @if($proposal->revisions && $proposal->revisions->count())
                    @foreach($proposal->revisions as $rev)
                        <div class="border-t border-slate-200 p-md flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex-1">
                                <div class="font-semibold text-slate-900">Revisi #{{ $rev->revision_number ?? $loop->iteration }} <span class="text-sm text-slate-500">• {{ $rev->getStatusLabelAttribute() }}</span></div>
                                <div class="text-sm text-slate-500">{{ optional($rev->submitted_date)->format('d M Y') ?? '-' }}</div>
                                @if($rev->revision_note)
                                    <div class="mt-3 text-sm text-slate-700 whitespace-pre-wrap">{{ $rev->revision_note }}</div>
                                @endif
                            </div>
                            @if($rev->file)
                                <div class="flex flex-col items-start gap-2 sm:items-end">
                                    <div class="text-sm text-slate-600">{{ $rev->file->original_name }}</div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('sekretaris.proposal-file.view', $rev->file) }}" target="_blank" class="inline-flex items-center gap-2 rounded-3xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">Lihat</a>
                                        <a href="{{ route('sekretaris.proposal-file.download', $rev->file) }}" class="inline-flex items-center gap-2 rounded-3xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition">Download</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="p-md text-slate-500">Belum ada revisi yang dikirim peneliti.</div>
                @endif
            </div>

            <div class="bg-blue-900 text-white rounded-3xl p-xl shadow-lg mt-xl">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-start gap-5">
                        <div class="rounded-3xl bg-blue-800 p-4">
                            <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">summarize</span>
                        </div>
                        <div>
                            <h5 class="text-2xl font-bold">Ringkasan Reviewer</h5>
                            <div class="mt-4 flex flex-wrap gap-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                                    <span class="text-sm">{{ $summary['approved'] }} Reviewer menyarankan <strong>APPROVE</strong></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                                    <span class="text-sm">{{ $summary['revision'] }} Reviewer menyarankan <strong>REVISE</strong></span>
                                </div>
                            </div>
                            <p class="mt-3 text-sm text-blue-200 italic">Gunakan ringkasan ini sebagai referensi untuk pengambilan keputusan.</p>
                        </div>
                    </div>
                    <a id="btn-lanjut-ke-keputusan" href="{{ route('sekretaris.keputusan') }}?proposal_id={{ $proposal->id }}" data-submitted-count="{{ $submittedCount }}" data-required-count="3" class="inline-flex items-center gap-3 rounded-3xl bg-white px-6 py-3 text-sm font-bold text-blue-900 shadow-lg hover:bg-slate-100 transition">
                        Lanjut ke Keputusan
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var button = document.getElementById('btn-lanjut-ke-keputusan');
        if (!button) return;

        var submittedCount = Number(button.dataset.submittedCount || 0);
        var requiredCount = Number(button.dataset.requiredCount || 3);

        button.addEventListener('click', function (event) {
            if (submittedCount < requiredCount) {
                event.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'Belum semua reviewer mengirim',
                    text: 'Tunggu hingga semua 3 reviewer menyerahkan hasil review sebelum melanjutkan ke keputusan.',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#2563eb'
                });
            }
        });
    });

    // Real-time timestamp update for STATUS REVIEWER section
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
        
        return `${day} ${month} ${year}`;
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
@endsection
