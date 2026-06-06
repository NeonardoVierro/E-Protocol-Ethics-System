@extends('layouts.reviewer')

@section('title', 'Detail Riwayat Review')
@section('page-title', 'Detail Riwayat Review')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8f9ff; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>

@php
    $proposal = $review->proposal;
    $feedback = $review->feedback;
    $feedbackData = $feedback ? json_decode($feedback->feedback_text, true) : [];
    
    $recommendationLabel = [
        'approved' => 'Approved',
        'revision' => 'Revision',
        'rejected' => 'Rejected'
    ][$feedback?->recommendation ?? 'unknown'] ?? 'Unknown';
    
    $recommendationBadge = [
        'approved' => 'bg-emerald-100 text-emerald-700',
        'revision' => 'bg-blue-100 text-blue-700',
        'rejected' => 'bg-red-100 text-red-700'
    ][$feedback?->recommendation ?? 'unknown'] ?? 'bg-slate-100 text-slate-700';
    
    $recommendationIcon = [
        'approved' => 'check_circle',
        'revision' => 'edit',
        'rejected' => 'cancel'
    ][$feedback?->recommendation ?? 'unknown'] ?? 'help';
@endphp

<div class="space-y-6">
    <div class="flex items-center gap-2">
        <a href="{{ route('reviewer.riwayat-review') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke Riwayat
        </a>
    </div>

    <!-- Header Section -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <!-- Left: Proposal Info -->
            <div class="flex-1 min-w-0">
                <div class="flex flex-col gap-3">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900 truncate">
                        {{ $proposal->title }}
                    </h1>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-md bg-slate-100 px-3 py-1 text-slate-700 text-[12px] font-semibold">
                            <span class="material-symbols-outlined">badge</span>
                            {{ $proposal->proposal_code ?? 'ID: ' . $proposal->id }}
                        </span>

                        <span class="inline-flex items-center gap-2 rounded-md {{ $recommendationBadge }} px-3 py-1 text-[12px] font-semibold">
                            <span class="material-symbols-outlined">{{ $recommendationIcon }}</span>
                            {{ $recommendationLabel }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 text-sm text-slate-600">
                    <div class="rounded-2xl bg-slate-50 p-4 grid grid-cols-1 gap-2">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">Peneliti</p>
                            <p class="mt-1 font-medium text-slate-900 leading-tight">{{ $proposal->researcher->name ?? '-' }}</p>
                            <p class="text-xs text-slate-500">{{ $proposal->researcher->email ?? '-' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-2">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">Asal Instansi</p>
                                <p class="mt-1 font-medium text-slate-900">{{ $proposal->asal_instansi ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">Jenis Penelitian</p>
                                <p class="mt-1 font-medium text-slate-900">{{ $proposal->jenis_penelitian ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">Bidang Ilmu</p>
                                <p class="mt-1 font-medium text-slate-900">{{ $proposal->bidang_ilmu ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">Lokasi Penelitian</p>
                                <p class="mt-1 font-medium text-slate-900">{{ $proposal->lokasi_penelitian ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-3 text-xs text-slate-400">
                            <span class="inline-flex items-center gap-2"><span class="material-symbols-outlined">calendar_month</span> Submitted {{ optional($proposal->submission_date)->format('d M Y') ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Review Info -->
            <div class="w-full lg:w-72 flex-shrink-0">
                <div class="grid grid-cols-1 gap-3">
                    <!-- Review Date -->
                    <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="rounded-lg bg-blue-50 p-2.5 text-blue-600 flex-shrink-0">
                                <span class="material-symbols-outlined text-lg">calendar_month</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs text-slate-500 font-medium">REVIEW DATE</div>
                                <div class="text-sm font-semibold text-slate-900 mt-1">
                                    {{ optional($review->completed_date)->format('d M Y') ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-400">{{ optional($review->completed_date)->format('H:i') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Review Status -->
                    <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="rounded-lg bg-emerald-50 p-2.5 text-emerald-600 flex-shrink-0">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs text-slate-500 font-medium">STATUS</div>
                                <div class="text-sm font-semibold text-slate-900 mt-1">Submitted</div>
                                <div class="text-xs text-slate-400">Review Completed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Section -->
    <div class="grid gap-6 lg:grid-cols-[1fr]">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Ethical Pillars Feedback</h2>
                <p class="mt-1 text-sm text-slate-500">Review yang sudah disubmit oleh reviewer.</p>
            </div>

            <div class="space-y-6 p-6">
                <!-- Autonomy -->
                <div class="space-y-3">
                    <label class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined" style="color: #2563eb;">shield</span>
                        Autonomy
                    </label>
                    <div class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 whitespace-pre-wrap">
                        {{ $feedbackData['autonomy'] ?? '-' }}
                    </div>
                </div>

                <!-- Beneficence -->
                <div class="space-y-3">
                    <label class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined" style="color: #2563eb;">favorite</span>
                        Beneficence
                    </label>
                    <div class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 whitespace-pre-wrap">
                        {{ $feedbackData['beneficence'] ?? '-' }}
                    </div>
                </div>

                <!-- Justice -->
                <div class="space-y-3">
                    <label class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined" style="color: #2563eb;">gavel</span>
                        Justice
                    </label>
                    <div class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 whitespace-pre-wrap">
                        {{ $feedbackData['justice'] ?? '-' }}
                    </div>
                </div>

                <!-- General Comments -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-slate-900">General Comments</label>
                    <div class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 whitespace-pre-wrap">
                        {{ $feedbackData['general_comments'] ?? '-' }}
                    </div>
                </div>

                <!-- Recommendation -->
                <div class="pt-4 border-t border-slate-200">
                    <p class="mb-3 text-sm font-semibold text-slate-900">Final Recommendation</p>
                    <div class="inline-flex items-center gap-2 rounded-lg {{ $recommendationBadge }} px-4 py-2.5 text-sm font-semibold">
                        <span class="material-symbols-outlined">{{ $recommendationIcon }}</span>
                        {{ $recommendationLabel }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
