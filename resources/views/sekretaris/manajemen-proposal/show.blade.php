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
                    <div class="text-sm text-slate-600">Documents: <span class="font-semibold">{{ $proposal->files->count() }}</span></div>
                </div>

                @if($proposal->files->isNotEmpty())
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
                $discussionNotes = $reviewAssignments->filter(fn($assignment) => filled($assignment->notes));
                $documentComments = $reviewAssignments->filter(fn($assignment) => filled($assignment->comment_to_review));
            @endphp

            <section class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Discussion</h3>
                @if($discussionNotes->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($discussionNotes as $assignment)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-sm text-slate-800">{{ $assignment->notes }}</p>
                                <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                                    <span>{{ $assignment->assignedBy->name ?? 'Sekretaris' }}</span>
                                    <span>{{ optional($assignment->created_at)->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="min-h-[120px] border border-slate-100 rounded-lg p-4 text-slate-500">No discussion notes yet</div>
                @endif
            </section>

            <section class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Document Comments</h3>
                @if($documentComments->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($documentComments as $assignment)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-sm text-slate-800">{{ $assignment->comment_to_review }}</p>
                                <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                                    <span>{{ $assignment->assignedBy->name ?? 'Sekretaris' }}</span>
                                    <span>{{ optional($assignment->created_at)->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="min-h-[80px] border border-slate-100 rounded-lg p-4 text-slate-500">No document comments yet</div>
                @endif
            </section>

            @php
                $revisionFiles = $proposal->files->where('file_type', App\Models\ProposalFile::TYPE_REVISION);
            @endphp

            <section class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Revision Files</h3>
                    <span class="text-sm text-slate-500">{{ $revisionFiles->count() }} file(s)</span>
                </div>

                @if($revisionFiles->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Filename</th>
                                    <th class="px-4 py-3 font-medium">Version</th>
                                    <th class="px-4 py-3 font-medium">Uploaded</th>
                                    <th class="px-4 py-3 font-medium">Access</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revisionFiles as $r)
                                <tr class="border-t border-slate-100">
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-900">{{ $r->original_name }}</div>
                                        <div class="text-xs text-slate-400 mt-1">{{ $r->mime_type }} · {{ number_format($r->file_size / 1024, 1) }} KB</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-full bg-blue-600 text-white px-3 py-1 text-xs font-semibold">v{{ $r->version ?? 1 }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ optional($r->created_at)->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-4">
                                        <a href="{{ route('sekretaris.proposal-file.download', $r) }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                                            <i class="fas fa-download"></i>
                                            Download
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-2xl border border-slate-100 p-4 bg-slate-50 text-slate-500">Belum ada file revisi yang diunggah.</div>
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

            <section class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Processing Action</h3>

                @php
                    $reviewAssignments = $reviewAssignments ?? collect();
                    $completedReviewsCount = $proposal->reviews->where('status', App\Models\Review::STATUS_COMPLETED)->count();
                    $pendingReviewers = max(0, $reviewAssignments->count() - $completedReviewsCount);
                @endphp

                @if($proposal->status === App\Models\Proposal::STATUS_ON_REVIEW && $reviewAssignments->isNotEmpty() && $pendingReviewers > 0)
                    <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-4 text-left">
                        <div class="text-sm text-yellow-800">Menunggu reviewer menyelesaikan review. Saat ini {{ $completedReviewsCount }} dari {{ $reviewAssignments->count() }} reviewer selesai.</div>
                    </div>
                @elseif($proposal->status === App\Models\Proposal::STATUS_ON_REVIEW && $reviewAssignments->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left">
                        <div class="text-sm text-slate-700">Semua reviewer telah menyelesaikan review. Lanjutkan ke halaman keputusan untuk mengambil keputusan.</div>
                        <a href="{{ route('sekretaris.keputusan') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-blue-600 text-white px-4 py-3 font-semibold">
                            <i class="fas fa-gavel mr-2"></i> Lanjut ke Keputusan
                        </a>
                    </div>
                @else
                
                    <div class="space-y-3">
                        <button type="button" data-review-type="{{ App\Models\Proposal::REVIEW_EXEMPTED }}" data-review-label="Exempted (Auto Approve)" class="open-review-modal w-full inline-flex items-center gap-2 justify-center rounded-lg bg-emerald-600 text-white px-4 py-3 font-semibold">
                            <i class="fas fa-check-circle"></i>
                            Exempted (Auto Approve)
                        </button>

                        <button type="button" data-review-type="{{ App\Models\Proposal::REVIEW_EXPEDITED }}" data-review-label="Expedited Review" class="open-review-modal w-full inline-flex items-center gap-2 justify-center rounded-lg bg-sky-400 text-white px-4 py-3 font-semibold">
                            <i class="fas fa-globe"></i>
                            Expedited Review
                        </button>

                        <button type="button" data-review-type="{{ App\Models\Proposal::REVIEW_FULL_BOARD }}" data-review-label="Fullboard Review" class="open-review-modal w-full inline-flex items-center gap-2 justify-center rounded-lg bg-blue-600 text-white px-4 py-3 font-semibold">
                            <i class="fas fa-users"></i>
                            Fullboard Review
                        </button>
                    </div>

                    <form action="{{ route('sekretaris.keputusan.update') }}" method="POST" class="mt-4">
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
                                            <div class="text-slate-700">{{ optional($assignment->due_date)->format('d M Y') ?? '-' }}</div>
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

<div id="processing-modal" class="fixed inset-0 hidden z-[9999] items-center justify-center p-4">
    <div id="processing-modal-overlay" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-white border border-slate-200 shadow-xl">
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
            <div>
                <label class="text-sm font-medium text-slate-700">Review Type</label>
                <input type="text" name="review_label" readonly class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700" />
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700">Select Reviewer</label>
                    <select name="reviewer_id" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                        <option value="">Choose reviewer</option>
                        @if(!empty($reviewers) && $reviewers->count())
                            @foreach($reviewers as $reviewer)
                                <option value="{{ $reviewer->id }}">{{ $reviewer->name }}</option>
                            @endforeach
                        @endif
                    </select>
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

            <div class="flex flex-col gap-3 pt-3 sm:flex-row">
                <button type="button" id="processing-modal-cancel" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">Cancel</button>
                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">Submit Assignment</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
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
    form.querySelector('[name="reviewer_id"]').value = '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeProcessingModal() {
    const modal = document.getElementById('processing-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
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
    });
}

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