@extends('layouts.reviewer')

@section('title', 'Detail Tinjau Proposal')
@section('page-title', 'Detail Tinjau Proposal')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">{{ $proposal->title }}</h1>
                <p class="text-gray-600">On behalf of {{ $proposal->researcher->name ?? 'N/A' }}</p>
            </div>
            <div class="flex gap-2">
                <span class="px-4 py-2 {{ $proposal->process_badge_classes }} text-xs font-semibold rounded-full">{{ $proposal->process_label }}</span>
                <span class="px-4 py-2 {{ $proposal->progress_badge_classes }} text-xs font-semibold rounded-full">{{ $proposal->progress_label }}</span>
                <span class="px-4 py-2 {{ $proposal->round_badge_classes }} text-xs font-semibold rounded-full">{{ $proposal->round_label }}</span>
            </div>
        </div>

        <!-- User Info Card -->
        <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                {{ substr($proposal->researcher->name ?? 'U', 0, 1) }}
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900">{{ $proposal->researcher->name ?? 'Unknown' }}</p>
                <p class="text-sm text-gray-600">Submitter</p>
                <p class="text-sm text-gray-500">{{ $proposal->researcher->email ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Info Bar with Payment and Documents -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Payment</p>
                    <p class="font-semibold text-gray-900">Unpaid</p>
                </div>
                <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-md">Unpaid</span>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Documents</p>
                    <p class="font-semibold text-gray-900">{{ $proposal->files->count() }} Items</p>
                </div>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-md">{{ $proposal->files->count() }} Items</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-3 gap-6">
        <!-- Left Column (2/3 width) -->
        <div class="col-span-2 space-y-6">
            <!-- Submitted Documents Section -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Submitted Documents</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Document</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Version</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Access</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">History</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proposal->files as $file)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-file-pdf text-blue-600"></i>
                                            <span class="font-medium text-gray-900">{{ $file->original_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-md">Required</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-md">v1</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('reviewer.proposal-file.download', $file->id) }}" class="text-green-600 hover:text-green-700">
                                            <i class="fas fa-download text-lg"></i>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-history text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-100 bg-gray-50">
                                    <td colspan="5" class="px-6 py-3">
                                        <div class="text-xs text-gray-600">
                                            <p><strong>Version v1</strong> - {{ $file->created_at->format('d M Y H:i') }}</p>
                                            <p class="text-gray-500"><a href="{{ route('reviewer.proposal-file.download', $file->id) }}" class="text-blue-600 hover:underline">Download</a></p>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada dokumen yang diupload
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @php
                $discussionNotes = $proposal->assignments->filter(fn($assignment) => filled($assignment->notes));
                $documentComments = $proposal->assignments->filter(fn($assignment) => filled($assignment->comment_to_review));
            @endphp

            <!-- Discussion Section -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Discussion</h2>
                </div>
                <div class="px-6 py-4 space-y-4">
                    @if($discussionNotes->isNotEmpty())
                        @foreach($discussionNotes as $assignment)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-sm text-slate-800">{{ $assignment->notes }}</p>
                                <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                                    <span>{{ $assignment->assignedBy->name ?? 'Sekretaris' }}</span>
                                    <span>{{ optional($assignment->created_at)->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="px-6 py-8 text-center">
                            <p class="text-gray-500 text-sm">No discussion notes yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Document Comments Section -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Document Comments</h2>
                </div>
                <div class="px-6 py-4 space-y-4">
                    @if($documentComments->isNotEmpty())
                        @foreach($documentComments as $assignment)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-sm text-slate-800">{{ $assignment->comment_to_review }}</p>
                                <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                                    <span>{{ $assignment->assignedBy->name ?? 'Sekretaris' }}</span>
                                    <span>{{ optional($assignment->created_at)->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="px-6 py-8 text-center">
                            <p class="text-gray-500 text-sm">No document comments yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column (1/3 width) -->
        <div class="col-span-1 space-y-6">
            <!-- Processing Action Section -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Processing Action</h2>
                </div>
                <div class="p-6 space-y-3">
                    <form action="{{ route('reviewer.review-proposal.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">

                        <!-- Exempted Button -->
                        <button type="button" onclick="setReviewType('exempted')" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg flex items-center justify-center gap-2 transition">
                            <i class="fas fa-check-circle"></i>
                            Exempted (Auto Approve)
                        </button>

                        <!-- Expedited Review Button -->
                        <button type="button" onclick="setReviewType('expedited')" class="w-full px-4 py-3 bg-cyan-500 hover:bg-cyan-600 text-white font-semibold rounded-lg flex items-center justify-center gap-2 transition">
                            <i class="fas fa-zap"></i>
                            Expedited Review
                        </button>

                        <!-- Fullboard Review Button -->
                        <button type="button" onclick="setReviewType('full_board')" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg flex items-center justify-center gap-2 transition">
                            <i class="fas fa-users"></i>
                            Fullboard Review
                        </button>

                        <!-- Reject Button -->
                        <button type="button" onclick="setReviewType('rejected')" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg flex items-center justify-center gap-2 transition">
                            <i class="fas fa-times-circle"></i>
                            Reject
                        </button>
                    </form>

                    <!-- Hidden form for submission -->
                    <form id="review-form" action="{{ route('reviewer.review-proposal.store') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">
                        <input type="hidden" name="status" id="review-status">
                        <input type="hidden" name="feedback" id="review-feedback">
                    </form>
                </div>
            </div>

            <!-- Activity Log Section -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Activity Log</h2>
                </div>
                <div class="px-6 py-4 space-y-4 max-h-96 overflow-y-auto">
                    @if($proposal->reviews->count() > 0)
                        @foreach($proposal->reviews->sortByDesc('created_at') as $review)
                            <div class="pb-4 border-b border-gray-100 last:border-b-0">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 flex-shrink-0 mt-1">
                                        <i class="fas fa-file-check text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        @if($review->feedback)
                                            <p class="font-semibold text-gray-900 text-sm">
                                                {{ ucfirst(str_replace('_', ' ', $review->feedback->recommendation)) }}
                                            </p>
                                            <p class="text-xs text-gray-600">
                                                {{ $review->feedback->submitted_at ? $review->feedback->submitted_at->format('d M Y H:i') : $review->created_at->format('d M Y H:i') }}
                                            </p>
                                        @else
                                            <p class="font-semibold text-gray-900 text-sm">Review Assigned</p>
                                            <p class="text-xs text-gray-600">{{ $review->assigned_date->format('d M Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <!-- Initial Submission Log -->
                    <div class="pb-4 border-b border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 flex-shrink-0 mt-1">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 text-sm">Submission Processed</p>
                                <p class="text-xs text-gray-600">Submission processed and assigned to secretary.</p>
                                <p class="text-xs text-gray-500">{{ $proposal->submission_date->format('d M Y H:i') ?? '13 Apr 2026 09:05' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Created Log -->
                    <div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 flex-shrink-0 mt-1">
                                <i class="fas fa-plus text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 text-sm">Submission Created</p>
                                <p class="text-xs text-gray-600">A new submission has been created</p>
                                <p class="text-xs text-gray-500">{{ $proposal->created_at->format('d M Y H:i') ?? '13 Apr 2026 09:03' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setReviewType(type) {
    // Show modal or form to allow feedback input
    const reviewType = type === 'rejected' ? 'ditolak' : 
                      type === 'exempted' ? 'diterima' : 
                      type === 'expedited' ? 'diterima' : 'diterima';
    
    // Set the form values
    document.getElementById('review-status').value = reviewType;
    
    // If it's a simple action, submit directly
    if (type === 'exempted') {
        document.getElementById('review-feedback').value = 'Proposal exempted from full board review.';
    } else if (type === 'expedited') {
        document.getElementById('review-feedback').value = 'Proposal requires expedited review process.';
    } else if (type === 'full_board') {
        document.getElementById('review-feedback').value = 'Proposal requires full board review.';
    } else if (type === 'rejected') {
        document.getElementById('review-status').value = 'ditolak';
        document.getElementById('review-feedback').value = 'Proposal rejected after review.';
    }
    
    // Show confirmation or submit
    if (confirm(`Are you sure you want to proceed with ${type}?`)) {
        document.getElementById('review-form').submit();
    }
}
</script>

@endsection
