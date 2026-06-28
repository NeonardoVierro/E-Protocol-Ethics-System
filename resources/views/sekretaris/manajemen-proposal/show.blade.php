@extends('layouts.sekretaris')

@section('title', 'Tinjau Proposal')
@section('page-title', 'Tinjau Proposal')
@section('breadcrumb', 'Detail Proposal')

@section('content')
<div class="mx-auto w-full max-w-7xl space-y-6 px-4 pb-8 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex-1">
            <h2 class="text-2xl font-semibold text-slate-900">{{ $proposal->title }}</h2>
            <p class="text-sm text-slate-600 mt-1">On behalf of {{ $proposal->researcher->name ?? 'Peneliti' }} &middot; {{ $proposal->asal_instansi ?? '-' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('sekretaris.manajemen-proposal') }}"
                class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors duration-150 hover:bg-slate-50">
                <i class="fas fa-arrow-left text-sm"></i>
                Back
            </a>
            <div class="space-x-2 hidden sm:inline-flex">
                <span class="inline-flex items-center rounded-full {{ $proposal->process_badge_classes }} px-3 py-1 text-xs font-semibold">{{ $proposal->process_label }}</span>
                <span class="inline-flex items-center rounded-full {{ $proposal->progress_badge_classes }} px-3 py-1 text-xs font-semibold">{{ $proposal->progress_label }}</span>
                <span class="inline-flex items-center rounded-full {{ $proposal->round_badge_classes }} px-3 py-1 text-xs font-semibold">{{ $proposal->round_label }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-3xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_420px] items-start">
        <div class="space-y-6">
            <section class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Submitted Documents</h3>
                        <p class="text-sm text-gray-500">Daftar dokumen yang diunggah oleh peneliti.</p>
                    </div>
                    <div class="text-sm text-slate-600">Documents: <span class="font-semibold">{{ $proposal->files->count() + ($proposal->revisions ? $proposal->revisions->count() : 0) }}</span></div>
                </div>

                @if($proposal->files->isNotEmpty() || ($proposal->revisions && $proposal->revisions->count()))
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Document</th>
                                    <th class="px-4 py-3 font-medium">Type</th>
                                    <th class="px-4 py-3 font-medium">Version</th>
                                    <th class="px-4 py-3 font-medium">Access</th>
                                    <th class="px-4 py-3 font-medium">History</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proposal->files as $file)
                                <tr class="border-t border-slate-100">
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-900">{{ $file->original_name }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-slate-700">
                                        @php $type = $file->file_type === App\Models\ProposalFile::TYPE_PROPOSAL ? 'Required' : 'Optional'; @endphp
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $type === 'Required' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700' }}">{{ $type }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-full bg-blue-600 text-white px-3 py-1 text-xs font-semibold">v{{ $file->version ?? 1 }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <a href="{{ route('sekretaris.proposal-file.download', $file) }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                                            <i class="fas fa-download"></i>
                                            Download
                                        </a>
                                    </td>
                                    <td class="px-4 py-4">
                                        <button type="button" 
                                                class="inline-flex items-center justify-center rounded-full border border-slate-200 w-9 h-9 text-slate-600 btn-history"
                                                data-version="{{ $file->version ?? 1 }}"
                                                data-date="{{ $file->created_at ? $file->created_at->format('d M Y H:i') : ($proposal->submission_date ? $proposal->submission_date->format('d M Y') : '-') }}"
                                                data-author="{{ $proposal->researcher->name ?? '-' }}">
                                            <i class="fas fa-clock"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach

                                @if($proposal->revisions && $proposal->revisions->count())
                                    @foreach($proposal->revisions as $rev)
                                    <tr class="border-t border-slate-100 bg-amber-50">
                                        <td class="px-4 py-4">
                                            <div class="font-semibold text-slate-900">{{ $rev->file?->original_name ?? 'Revision File' }}</div>
                                            @if($rev->revision_note)
                                            <div class="text-xs text-slate-600 mt-1">{{ $rev->revision_note }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-slate-700">
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-amber-100 text-amber-700">Revisi #{{ $rev->revision_number }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center rounded-full bg-amber-600 text-white px-3 py-1 text-xs font-semibold">v{{ $rev->file?->version ?? $rev->revision_number + 1 }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            @if($rev->file)
                                            <a href="{{ route('sekretaris.proposal-file.download', $rev->file) }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                                                <i class="fas fa-download"></i>
                                                Download
                                            </a>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            @if($rev->file)
                                            <button type="button" 
                                                    class="inline-flex items-center justify-center rounded-full border border-slate-200 w-9 h-9 text-slate-600 btn-history"
                                                    data-version="{{ $rev->file?->version ?? $rev->revision_number + 1 }}"
                                                    data-date="{{ $rev->submitted_date ? $rev->submitted_date->format('d M Y') : '-' }}"
                                                    data-author="{{ $proposal->researcher->name ?? '-' }}">
                                                <i class="fas fa-clock"></i>
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-2xl border border-red-200 p-4 bg-red-50">
                        <p class="text-sm text-red-700">Belum ada dokumen yang diunggah oleh peneliti.</p>
                    </div>
                @endif
            </section>

            @php
                $reviewFeedbacks = $proposal->reviews
                    ->filter(fn($review) => $review->feedback && $review->feedback->is_submitted && filled($review->feedback->feedback_text));
            @endphp

            <section class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Saran dari Reviewer</h3>
                @if($reviewFeedbacks->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($reviewFeedbacks as $review)
                            @php
                                $feedbackData = [];
                                if (is_string($review->feedback->feedback_text)) {
                                    $decoded = json_decode($review->feedback->feedback_text, true);
                                    $feedbackData = is_array($decoded) ? $decoded : [];
                                }
                            @endphp
                            
                            @if(count($feedbackData) > 0)
                                @if(isset($feedbackData['autonomy']) && filled($feedbackData['autonomy']))
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <p class="font-semibold text-slate-900">{{ $review->reviewer->name ?? 'Reviewer' }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ $review->reviewer->email ?? '-' }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 ml-3">
                                            <span class="text-xs font-semibold text-slate-600 bg-slate-200 px-2 py-1 rounded">Autonomy</span>
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $review->feedback->recommendation_badge }}">
                                                {{ $review->feedback->recommendation_label }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-slate-800">{{ $feedbackData['autonomy'] }}</p>
                                    <div class="mt-3 text-xs text-slate-500">
                                        {{ optional($review->feedback->submitted_at)->format('d M Y H:i') ?? 'Submitted' }}
                                    </div>
                                </div>
                                @endif
                                
                                @if(isset($feedbackData['beneficence']) && filled($feedbackData['beneficence']))
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <p class="font-semibold text-slate-900">{{ $review->reviewer->name ?? 'Reviewer' }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ $review->reviewer->email ?? '-' }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 ml-3">
                                            <span class="text-xs font-semibold text-slate-600 bg-slate-200 px-2 py-1 rounded">Beneficence</span>
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $review->feedback->recommendation_badge }}">
                                                {{ $review->feedback->recommendation_label }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-slate-800">{{ $feedbackData['beneficence'] }}</p>
                                    <div class="mt-3 text-xs text-slate-500">
                                        {{ optional($review->feedback->submitted_at)->format('d M Y H:i') ?? 'Submitted' }}
                                    </div>
                                </div>
                                @endif
                                
                                @if(isset($feedbackData['justice']) && filled($feedbackData['justice']))
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <p class="font-semibold text-slate-900">{{ $review->reviewer->name ?? 'Reviewer' }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ $review->reviewer->email ?? '-' }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 ml-3">
                                            <span class="text-xs font-semibold text-slate-600 bg-slate-200 px-2 py-1 rounded">Justice</span>
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $review->feedback->recommendation_badge }}">
                                                {{ $review->feedback->recommendation_label }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-slate-800">{{ $feedbackData['justice'] }}</p>
                                    <div class="mt-3 text-xs text-slate-500">
                                        {{ optional($review->feedback->submitted_at)->format('d M Y H:i') ?? 'Submitted' }}
                                    </div>
                                </div>
                                @endif
                                
                                @if(isset($feedbackData['general_comments']) && filled($feedbackData['general_comments']))
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <p class="font-semibold text-slate-900">{{ $review->reviewer->name ?? 'Reviewer' }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ $review->reviewer->email ?? '-' }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 ml-3">
                                            <span class="text-xs font-semibold text-slate-600 bg-slate-200 px-2 py-1 rounded">Komentar Umum</span>
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $review->feedback->recommendation_badge }}">
                                                {{ $review->feedback->recommendation_label }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-slate-800">{{ $feedbackData['general_comments'] }}</p>
                                    <div class="mt-3 text-xs text-slate-500">
                                        {{ optional($review->feedback->submitted_at)->format('d M Y H:i') ?? 'Submitted' }}
                                    </div>
                                </div>
                                @endif
                            @else
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $review->reviewer->name ?? 'Reviewer' }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ $review->reviewer->email ?? '-' }}</p>
                                        </div>
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $review->feedback->recommendation_badge }}">
                                            {{ $review->feedback->recommendation_label }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-800 whitespace-pre-wrap">{{ $review->feedback->feedback_text }}</p>
                                    <div class="mt-3 text-xs text-slate-500">
                                        {{ optional($review->feedback->submitted_at)->format('d M Y H:i') ?? 'Submitted' }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="min-h-[120px] border border-slate-100 rounded-lg p-4 text-slate-500">Belum ada saran dari reviewer.</div>
                @endif
            </section>
        </div>

        <aside class="space-y-6">
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 text-center">
                <div class="w-20 h-20 rounded-full bg-blue-50 mx-auto flex items-center justify-center text-xl font-bold text-blue-600">{{ strtoupper(substr($proposal->researcher->name ?? 'U',0,1)) }}</div>
                <h4 class="mt-4 text-lg font-semibold text-slate-900">{{ $proposal->researcher->name ?? 'Peneliti' }}</h4>
                <p class="text-sm text-slate-500">{{ $proposal->researcher->email ?? '-' }}</p>
                <p class="text-xs text-slate-400 mt-2">Submitter</p>
            </div>

            @php
                $hasReviewerSent = $proposal->assignments()->where('role', App\Models\ProposalAssignment::ROLE_REVIEWER)->whereNotNull('sent_at')->exists();
                $hasSubmittedRevision = $proposal->revisions()->where('status', App\Models\ProposalRevision::STATUS_SUBMITTED)->exists();
            @endphp

            @if(! ($processingActionHidden ?? false))
            <section class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Processing Action</h3>

                @if($proposal->status === App\Models\Proposal::STATUS_ON_REVIEW && $hasReviewerSent && ! $hasSubmittedRevision)
                    <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-4 text-left">
                        <div class="text-sm text-yellow-800">Waiting for reviewers to complete their review.</div>
                    </div>

                    <form action="{{ route('sekretaris.keputusan.update') }}" method="POST" class="mt-4 reject-action-form">
                        @csrf
                        <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="w-full inline-flex items-center gap-2 justify-center rounded-lg bg-red-600 text-white px-4 py-3 font-semibold"> 
                            <i class="fas fa-times-circle"></i>
                            Reject
                        </button>
                    </form>

                @else

                    @if(isset($previousReviewers) && $previousReviewers->isNotEmpty())
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 mb-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Reviewer sebelum revisi terakhir</p>
                                    <p class="text-xs text-slate-500">Reviewers yang memeriksa proposal ini sebelum peneliti mengirim revisi terakhir.</p>
                                </div>
                                <span class="text-xs text-slate-500">{{ $previousReviewers->count() }} reviewer</span>
                            </div>
                            <div class="mt-3 grid gap-2">
                                @foreach($previousReviewers as $prevReviewer)
                                    <div class="flex items-center justify-between rounded-2xl bg-white border border-slate-200 px-3 py-2 text-sm text-slate-700">
                                        <div>
                                            <div class="font-semibold">{{ $prevReviewer['name'] }}</div>
                                            <div class="text-xs text-slate-500">{{ $prevReviewer['email'] }}</div>
                                        </div>
                                        <div class="text-xs text-slate-500">{{ $prevReviewer['completed_at'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <button type="button" data-review-type="{{ App\Models\Proposal::REVIEW_EXPEDITED }}" data-review-label="Expedited Review" class="open-review-modal w-full inline-flex items-center gap-2 justify-center rounded-lg bg-sky-400 text-white px-4 py-3 font-semibold">
                            <i class="fas fa-globe"></i>
                            Expedited Review
                        </button>
                    </div>

                    <form action="{{ route('sekretaris.keputusan.update') }}" method="POST" class="mt-4 reject-action-form">
                        @csrf
                        <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="w-full inline-flex items-center gap-2 justify-center rounded-lg bg-red-600 text-white px-4 py-3 font-semibold"> 
                            <i class="fas fa-times-circle"></i>
                            Reject
                        </button>
                    </form>
                @endif
            </section>
            @else
            @endif

            @if(isset($reviewAssignments) && $reviewAssignments->isNotEmpty())
                <section class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Assigned Reviewers</h3>
                        <span class="text-sm text-slate-500">{{ $reviewAssignments->count() }} assigned</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-slate-700">
                            <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wide">
                                <tr>
                                    <th class="px-3 py-3">Reviewer</th>
                                    <th class="px-3 py-3">Due Date</th>
                                    <th class="px-3 py-3">Completed</th>
                                    <th class="px-3 py-3">Recommended</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($reviewAssignments as $assignment)
                                    @php
                                        $review = $proposal->reviews->firstWhere('reviewer_id', $assignment->assigned_to);
                                        $completed = $review && $review->isCompleted() ? 'Completed' : 'Pending';
                                        $recommendation = $review?->feedback?->recommendationLabel ?? '-';
                                    @endphp
                                    <tr class="bg-white">
                                        <td class="px-3 py-3 align-top">
                                            <div class="font-semibold text-slate-900">{{ $assignment->assignedTo->name ?? 'Reviewer' }}</div>
                                            <div class="text-xs text-slate-500">{{ $assignment->assignedTo->email ?? '-' }}</div>
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <div class="text-slate-700">{{ $assignment->due_date ? \Carbon\Carbon::createFromFormat('Y-m-d', $assignment->due_date->toDateString())->format('d M Y') : '-' }}</div>
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-[11px] font-semibold {{ $review && $review->isCompleted() ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                {{ $completed }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <span class="text-slate-700">{{ $recommendation }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            <section class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Log</h3>
                <div id="activity-log" data-url="{{ route('sekretaris.proposal.activity-logs', $proposal) }}">
                    @include('sekretaris.manajemen-proposal._activity_logs', ['proposal' => $proposal, 'logs' => $logs])
                </div>
            </section>
        </aside>
     </div>
</div>

<div id="processing-modal" class="fixed inset-0 hidden z-50 items-center justify-center p-4">
    <div id="processing-modal-overlay" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-3xl bg-white border border-slate-200 shadow-xl">
        <div class="flex items-start justify-between gap-3 border-b border-slate-200 px-6 py-5">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Processing Action</h3>
                <p class="text-sm text-slate-500">Assign review type with reviewer, due date, and comment.</p>
            </div>
            <button type="button" id="processing-modal-close" class="rounded-full p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="processing-modal-form" action="{{ route('sekretaris.proposal.send-to-reviewer', $proposal) }}" method="POST" class="space-y-4 px-6 py-5">
            @csrf
            <input type="hidden" name="review_type" value="">
            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                <div>
                    <label class="text-sm font-medium text-slate-700">Review Type</label>
                    <input type="text" name="review_label" readonly class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Select Reviewers</label>
                    <p class="text-xs text-slate-500 mt-1 mb-2">Pilih maix 3 reviewer. Info assigned count membantu memilih reviewer yang lebih ringan.</p>
                    @if(isset($previousReviewers) && $previousReviewers->isNotEmpty())
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 mb-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Reviewer sebelum revisi terakhir</p>
                                    <p class="text-xs text-slate-500">Reviewer yang sudah memeriksa proposal ini sebelum peneliti mengirim revisi.</p>
                                </div>
                                <span class="text-xs text-slate-500">{{ $previousReviewers->count() }} reviewer</span>
                            </div>
                            <div class="mt-3 grid gap-2">
                                @foreach($previousReviewers as $prevReviewer)
                                    <div class="flex items-center justify-between rounded-2xl bg-white border border-slate-200 px-3 py-2 text-sm text-slate-700">
                                        <div>
                                            <div class="font-semibold">{{ $prevReviewer['name'] }}</div>
                                            <div class="text-xs text-slate-500">{{ $prevReviewer['email'] }}</div>
                                        </div>
                                        <div class="text-xs text-slate-500">{{ $prevReviewer['completed_at'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="relative">
                        <button type="button" id="reviewer-select-button" class="inline-flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm hover:border-slate-300">
                            <span id="reviewer-select-placeholder">Choose reviewers</span>
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </button>

                        <div id="reviewer-select-panel" class="hidden absolute z-30 mt-2 w-full overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-lg max-h-72">
                            @if(!empty($reviewers) && $reviewers->count())
                                @foreach($reviewers as $reviewer)
                                    <label class="reviewer-option group flex cursor-pointer items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 text-sm text-slate-700 transition hover:bg-slate-50">
                                        <div>
                                            <div class="reviewer-option-text font-medium">{{ $reviewer->name }}</div>
                                            <div class="text-xs text-slate-500 mt-1">{{ $reviewer->review_assignments_count }} assigned</div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="reviewer-option-check hidden h-5 w-5 items-center justify-center rounded-full border border-slate-300 text-blue-600 transition">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            <input type="checkbox" class="hidden reviewer-checkbox" name="reviewer_id[]" value="{{ $reviewer->id }}">
                                        </div>
                                    </label>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">Reviewer dipilih: <span id="reviewer-selected-names" class="font-medium">-</span></p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Due Date</label>
                    <input type="date" name="due_date" min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ now()->addDay()->format('Y-m-d') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Additional Note</label>
                    <textarea name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700" placeholder="Optional note for reviewer or committee"></textarea>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Comment to Review</label>
                    <textarea name="comment_to_review" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700" placeholder="Add a comment to the review process"></textarea>
                </div>
            </div>

            <div class="flex flex-col gap-3 pt-3 sm:flex-row">
                <button type="button" id="processing-modal-cancel" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">Cancel</button>
                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">Submit Assignment</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showKonfirmasiModal(pesan, onKonfirmasi) {
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center';
    overlay.innerHTML = `
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-3xl shadow-xl w-full max-w-lg mx-4 overflow-hidden border border-slate-200">
            <div class="px-6 py-6 text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-paper-plane text-blue-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Konfirmasi Pengiriman</h3>
                <div class="text-sm text-slate-600 leading-relaxed">${pesan}</div>
            </div>
            <div class="flex flex-col gap-3 px-6 pb-6 sm:flex-row">
                <button id="konfirmasi-batal" type="button"
                        class="w-full sm:w-auto flex-1 py-3 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button id="konfirmasi-ok" type="button"
                        class="w-full sm:w-auto flex-1 py-3 bg-slate-900 text-white rounded-2xl text-sm font-semibold hover:bg-slate-800 transition">
                    Ya, Kirim
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    overlay.querySelector('#konfirmasi-batal').addEventListener('click', () => overlay.remove());
    overlay.querySelector('.absolute').addEventListener('click', () => overlay.remove());
    overlay.querySelector('#konfirmasi-ok').addEventListener('click', () => {
        overlay.remove();
        onKonfirmasi();
    });
}

function openProcessingModal(reviewType, label) {
    const modal = document.getElementById('processing-modal');
    if (!modal) return;

    const form = modal.querySelector('#processing-modal-form');
    form.querySelector('[name="review_type"]').value = reviewType || '';
    form.querySelector('[name="review_label"]').value = label || '';

    const reviewerCheckboxes = Array.from(form.querySelectorAll('.reviewer-checkbox'));
    reviewerCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
        const option = checkbox.closest('.reviewer-option');
        option?.classList.remove('bg-blue-50', 'border', 'border-blue-200');
        const checkIcon = option?.querySelector('.reviewer-option-check');
        checkIcon?.classList.add('hidden');
        checkIcon?.classList.remove('inline-flex');
    });

    refreshReviewerSelection();

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeProcessingModal() {
    const modal = document.getElementById('processing-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function toggleReviewerPanel(open) {
    const panel = document.querySelector('#reviewer-select-panel');
    if (!panel) return;
    panel.classList.toggle('hidden', !open);
}

function refreshReviewerSelection() {
    const form = document.querySelector('#processing-modal-form');
    if (!form) return;

    const checkboxes = Array.from(form.querySelectorAll('.reviewer-checkbox'));
    const selectedLabels = [];

    checkboxes.forEach(checkbox => {
        const option = checkbox.closest('.reviewer-option');
        const checkIcon = option?.querySelector('.reviewer-option-check');

        if (checkbox.checked) {
            selectedLabels.push(option?.querySelector('.reviewer-option-text')?.textContent.trim() || 'Reviewer');
            option?.classList.add('bg-blue-50', 'border', 'border-blue-200');
            checkIcon?.classList.remove('hidden');
            checkIcon?.classList.add('inline-flex');
        } else {
            option?.classList.remove('bg-blue-50', 'border', 'border-blue-200');
            checkIcon?.classList.add('hidden');
            checkIcon?.classList.remove('inline-flex');
        }
    });

    const placeholder = document.querySelector('#reviewer-select-placeholder');
    if (!placeholder) return;

    if (selectedLabels.length === 0) {
        placeholder.textContent = 'Choose reviewers';
    } else if (selectedLabels.length === 1) {
        placeholder.textContent = selectedLabels[0];
    } else {
        placeholder.textContent = `${selectedLabels.length} selected`;
    }

    // Also update the small helper text showing selected reviewer names
    const selectedNamesEl = document.getElementById('reviewer-selected-names');
    if (selectedNamesEl) {
        selectedNamesEl.textContent = selectedLabels.length ? selectedLabels.join(', ') : '-';
    }
}

function initReviewerSelect() {
    const form = document.querySelector('#processing-modal-form');
    if (!form) return;

    const button = form.querySelector('#reviewer-select-button');
    const panel = form.querySelector('#reviewer-select-panel');
    const checkboxes = Array.from(form.querySelectorAll('.reviewer-checkbox'));

    button?.addEventListener('click', function (e) {
        e.stopPropagation();
        if (!panel) return;
        panel.classList.toggle('hidden');
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const checkedCount = form.querySelectorAll('.reviewer-checkbox:checked').length;
            if (checkedCount > 3) {
                this.checked = false;
                Swal.fire({
                    title: 'Maksimal 3 Reviewer',
                    text: 'Anda hanya dapat memilih maksimal 3 reviewer untuk penugasan ini.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'swal2-confirm bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl px-4 py-2'
                    }
                });
                return;
            }
            refreshReviewerSelection();
        });
    });

    document.addEventListener('click', function (e) {
        if (!form.contains(e.target)) {
            panel?.classList.add('hidden');
        }
    });
}

// History modal for file version details
function showHistoryModal(data) {
    const { version, date, author, name } = data;
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center';
    overlay.innerHTML = `
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-3xl shadow-xl w-full max-w-md mx-4 overflow-hidden border border-slate-200">
            <div class="px-6 py-6 text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-blue-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">File History</h3>
                <div class="text-sm text-slate-600 leading-relaxed text-left">
                    <div class="mb-2"><strong>Document:</strong> ${name || ''}</div>
                    <div class="mb-2"><strong>Version:</strong> ${version}</div>
                    <div class="mb-2"><strong>Submitted:</strong> ${date}</div>
                    <div class="mb-2"><strong>Author:</strong> ${author}</div>
                </div>
            </div>
            <div class="px-6 pb-6">
                <button id="history-close" type="button" class="w-full py-3 bg-slate-900 text-white rounded-2xl">Close</button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);
    overlay.querySelector('#history-close').addEventListener('click', () => overlay.remove());
    overlay.querySelector('.absolute').addEventListener('click', () => overlay.remove());
}

document.addEventListener('click', function (e) {
    const openBtn = e.target.closest('.open-review-modal');
    if (openBtn) {
        openProcessingModal(openBtn.dataset.reviewType, openBtn.dataset.reviewLabel);
        return;
    }

    const btn = e.target.closest('.btn-history');
    if (!btn) return;
    const version = btn.getAttribute('data-version') || '1';
    const date = btn.getAttribute('data-date') || '-';
    const author = btn.getAttribute('data-author') || '-';
    const name = btn.closest('tr')?.querySelector('td')?.innerText?.trim() || '';
    showHistoryModal({ version, date, author, name });
});

const processingModal = document.getElementById('processing-modal');
if (processingModal) {
    processingModal.querySelector('#processing-modal-close')?.addEventListener('click', closeProcessingModal);
    processingModal.querySelector('#processing-modal-cancel')?.addEventListener('click', closeProcessingModal);
    processingModal.querySelector('#processing-modal-overlay')?.addEventListener('click', closeProcessingModal);
}

// Optimistic UI: show waiting card immediately when Sekretaris submits assignment
const processingForm = document.getElementById('processing-modal-form');
if (processingForm) {
    processingForm.addEventListener('submit', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Submit Assignment?',
            text: 'Pastikan reviewer dan tanggal sudah benar sebelum mengirim.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, kirim',
            cancelButtonText: 'Batalkan',
            customClass: {
                confirmButton: 'swal2-confirm bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl px-4 py-2',
                cancelButton: 'swal2-cancel bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl px-4 py-2'
            },
            didOpen: function (popup) {
                popup.parentElement.style.zIndex = '99999';
                popup.style.zIndex = '99999';
                const backdrop = document.querySelector('.swal2-container');
                if (backdrop) backdrop.style.zIndex = '99999';
            }
        }).then(result => {
            if (!result.isConfirmed) return;

            const asideSection = document.querySelector('aside');
            const processingSection = asideSection?.querySelector('section');
            if (processingSection) {
                processingSection.innerHTML = `
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Processing Action</h3>
                    <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-4 text-left">
                        <div class="text-sm text-yellow-800">Waiting for reviewers to complete their review.</div>
                    </div>
                `;
            }

            e.target.submit();
        });
    });
}

function initRejectConfirmations() {
    const rejectForms = document.querySelectorAll('.reject-action-form');
    rejectForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reject Proposal?',
                text: 'Proposal akan ditolak dan tidak dikirim ke reviewer. Mohon isi alasan penolakan.',
                icon: 'warning',
                input: 'textarea',
                inputPlaceholder: 'Masukkan alasan penolakan (wajib)',
                inputAttributes: {
                    'aria-label': 'Alasan penolakan'
                },
                inputValidator: (value) => {
                    if (!value || !value.trim()) {
                        return 'Alasan penolakan wajib diisi.';
                    }
                    return null;
                },
                showCancelButton: true,
                confirmButtonText: 'Ya, tolak',
                cancelButtonText: 'Batalkan',
                customClass: {
                    confirmButton: 'swal2-confirm bg-red-600 hover:bg-red-700 text-white rounded-2xl px-4 py-2',
                    cancelButton: 'swal2-cancel bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl px-4 py-2'
                },
                didOpen: function (popup) {
                    popup.parentElement.style.zIndex = '99999';
                    popup.style.zIndex = '99999';
                    const backdrop = document.querySelector('.swal2-container');
                    if (backdrop) backdrop.style.zIndex = '99999';
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const reason = result.value;
                    // attach rejection_reason input to form
                    let input = form.querySelector('input[name="rejection_reason"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'rejection_reason';
                        form.appendChild(input);
                    }
                    input.value = reason;
                    form.submit();
                }
            });
        });
    });
}

initReviewerSelect();
initRejectConfirmations();

// Poll activity logs every 5 seconds and update the activity log container
(function pollActivityLogs() {
    const proposalId = '{{ $proposal->id }}';
    const container = document.getElementById('activity-log');
    if (!container) return;
    const url = container.dataset.url || '';

    const fetchLogs = () => {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                if (html && container.innerHTML.trim() !== html.trim()) {
                    container.innerHTML = html;
                }
            })
            .catch(() => {});
    };

    // Initial fetch and then interval
    fetchLogs();
    setInterval(fetchLogs, 5000);
})();
</script>
@endpush
@endsection