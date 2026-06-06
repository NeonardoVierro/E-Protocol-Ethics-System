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
        <!-- Left Column: Proposal List (simple list of feedbacks) -->
        <div class="col-span-4 flex flex-col gap-md">
            <div class="bg-white border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col max-h-[72vh] overflow-y-auto">
                @php
                    use App\Models\ProposalAssignment;
                    $assignments = $proposal->assignments()->where('role', ProposalAssignment::ROLE_REVIEWER)->with('assignedTo')->get();
                    $assignedCount = $assignments->count();
                    $submittedCount = $feedbacks->count();
                    $allSubmitted = $assignedCount > 0 && $submittedCount >= $assignedCount;
                @endphp

                <div class="p-md border-b border-outline-variant bg-surface-container-low flex justify-between items-center">
                    <div>
                        <span class="text-label-caps font-label-caps uppercase text-slate-500">Daftar Reviewer</span>
                        <div class="text-body-sm text-slate-600">{{ $proposal->title }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-[12px] px-2 py-1 rounded font-bold {{ $allSubmitted ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ $submittedCount }} / {{ $assignedCount ?: 0 }}</span>
                        @if($allSubmitted)
                            <span class="material-symbols-outlined text-green-700" title="All submitted">check_circle</span>
                        @else
                            <span class="material-symbols-outlined text-amber-700" title="Some missing">hourglass_top</span>
                        @endif
                    </div>
                </div>

                <div class="divide-y divide-outline-variant">
                    @foreach($assignments as $assignment)
                        @php
                            $fb = $feedbacks->first(function($v) use ($assignment) {
                                return optional($v->review->reviewer)->id == $assignment->assigned_to;
                            });
                        @endphp
                        <div class="p-md hover:bg-slate-50 cursor-pointer transition-colors">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-xs font-bold text-slate-500">{{ optional($assignment->assignedTo)->name ?? 'Reviewer' }}</span>
                                <span class="text-xs text-slate-500">{{ $fb ? optional($fb->submitted_at)->diffForHumans() : 'Menunggu' }}</span>
                            </div>
                            <h4 class="text-body-md font-medium text-on-background line-clamp-2">{{ \Illuminate\Support\Str::limit(optional($assignment->assignedTo)->name . ' — ' . ($fb->recommendation ?? ($fb ? $fb->recommendation : '')), 80) }}</h4>
                            <div class="flex gap-2 mt-md">
                                @if($fb)
                                    <span class="text-[10px] {{ $fb->recommendation === 'approved' ? 'bg-green-100 text-green-700' : ($fb->recommendation === 'revision' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }} px-2 py-0.5 rounded font-bold uppercase">{{ $fb->getRecommendationLabelAttribute() }}</span>
                                @else
                                    <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-bold uppercase">Menunggu</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column: Detail View -->
        <div class="col-span-8 space-y-lg">
            <div class="bg-white border border-outline-variant rounded-xl p-lg shadow-sm">
                <div class="flex justify-between items-start mb-md gap-4">
                    <div class="flex-1">
                        <span class="text-label-caps font-label-caps text-primary uppercase mb-1 block">ID PROPOSAL: {{ $proposal->nomor_ec ?? ('#' . $proposal->id) }}</span>
                        <h3 class="text-h2 font-h2 text-on-background">{{ $proposal->title }}</h3>
                        <p class="text-sm text-slate-500 mt-1">Dikirim: {{ optional($proposal->submission_date)->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-xl pt-lg border-t border-outline-variant">
                    <p class="text-label-caps font-label-caps text-slate-500 mb-md uppercase">Status Reviewer</p>
                    <div class="flex gap-4 overflow-x-auto pb-2 items-stretch">
                        @foreach($assignments as $assignment)
                            @php
                                $fb = $feedbacks->first(function($v) use ($assignment) {
                                    return optional($v->review->reviewer)->id == $assignment->assigned_to;
                                });
                            @endphp
                            <div class="min-w-[170px] bg-surface-container-low rounded-lg p-3 flex flex-col items-start gap-3 shadow-sm">
                                <div class="flex items-center gap-3 w-full">
                                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center border border-slate-200 text-slate-700 font-semibold">{{ strtoupper(substr(optional($assignment->assignedTo)->name ?? 'RV', 0, 1)) }}</div>
                                    <div class="flex-1">
                                        <div class="font-semibold text-sm">{{ optional($assignment->assignedTo)->name ?? 'Reviewer' }}</div>
                                        <div class="text-xs text-slate-500">{{ optional($assignment->assignedTo)->email ?? '' }}</div>
                                    </div>
                                    <div>
                                        @if($fb)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Done</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">On Review</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="w-full text-sm text-slate-700">{{ $fb ? \Illuminate\Support\Str::limit($fb->getRecommendationLabelAttribute() . ' • ' . optional($fb->submitted_at)->format('d M Y'), 80) : 'Menunggu pengiriman review' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-md">
                <h4 class="text-h3 font-h3 text-on-background">Umpan Balik Reviewer</h4>
                @foreach($feedbacks as $fb)
                    <div class="bg-white border border-outline-variant rounded-xl overflow-hidden">
                        <div class="p-lg bg-surface-container-low flex justify-between items-center border-b border-outline-variant">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-slate-200 flex items-center justify-center font-bold text-slate-600">{{ strtoupper(substr(optional($fb->review->reviewer)->name ?? 'R',0,1)) }}</div>
                                <div>
                                    <p class="font-bold text-on-background font-body-lg">{{ optional($fb->review->reviewer)->name ?? 'Reviewer' }}</p>
                                    <p class="text-body-sm text-slate-500">{{ optional($fb->review->reviewer)->roles()->pluck('name')->first() ?? 'Reviewer' }} • {{ optional($fb->submitted_at)->format('d M Y') }}</p>
                                </div>
                            </div>
                            <span class="{{ $fb->recommendation === 'approved' ? 'bg-green-100 text-green-700' : ($fb->recommendation === 'revision' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }} px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">{{ $fb->getRecommendationLabelAttribute() }}</span>
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
                                            <div class="bg-surface-container-low border border-outline-variant rounded-lg p-md">
                                                <p class="text-label-caps font-label-caps text-primary uppercase mb-2">{{ $f['label'] }}</p>
                                                <div class="text-body-md text-on-background leading-relaxed whitespace-pre-wrap break-words">{{ $renderField($pf, $f['keys']) }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                        @if($fb->file_path)
                        <div class="px-lg pb-lg">
                            <p class="text-label-caps font-label-caps text-slate-400 uppercase mb-xs text-[10px]">Lampiran Reviewer</p>
                            <div class="flex gap-md">
                                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded text-sm text-slate-600"> 
                                    <span class="material-symbols-outlined text-sm" data-icon="description">description</span>
                                    {{ $fb->original_name ?? 'Lampiran' }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="bg-blue-900 text-white rounded-xl p-xl shadow-lg mt-xl">
                <div class="flex flex-col md:flex-row justify-between items-center gap-lg">
                    <div class="flex items-start gap-md">
                        <div class="bg-blue-800 p-md rounded-lg">
                            <span class="material-symbols-outlined text-4xl" data-icon="summarize" style="font-variation-settings: 'FILL' 1;">summarize</span>
                        </div>
                        <div>
                            <h5 class="text-h3 font-h3 mb-1">Ringkasan Reviewer</h5>
                            <div class="flex flex-wrap gap-md mt-sm">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                                    <span class="text-body-md">{{ $summary['approved'] }} Reviewer menyarankan <strong>APPROVE</strong></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 bg-amber-400 rounded-full"></span>
                                    <span class="text-body-md">{{ $summary['revision'] }} Reviewer menyarankan <strong>REVISE</strong></span>
                                </div>
                            </div>
                            <p class="text-body-sm text-blue-200 mt-md italic">Catatan: Gunakan ringkasan ini sebagai referensi untuk pengambilan keputusan.</p>
                        </div>
                    </div>
                    <a href="{{ route('sekretaris.keputusan') }}" class="bg-white text-blue-900 font-bold px-lg py-md rounded-lg text-button font-button hover:bg-blue-50 transition-all flex items-center gap-3 shadow-md active:scale-95">
                        Lanjut ke Keputusan
                        <span class="material-symbols-outlined" data-icon="arrow_forward">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
