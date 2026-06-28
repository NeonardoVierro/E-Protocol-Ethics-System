@extends('layouts.reviewer')

@section('title', 'Detail Tinjau Proposal')
@section('page-title', 'Detail Tinjau Proposal')

@section('content')
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: { extend: {} }
    }
</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8f9ff; }
    .document-canvas { background: #525659; box-shadow: inset 0 0 10px rgba(0,0,0,0.2); display:flex; align-items:center; justify-content:center; padding:8px; }
    .page-shadow { box-shadow: 0 8px 20px rgba(2,6,23,0.08); }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .pill-icon { font-size: 18px; color: #2563eb; }
</style>
@php
    $downloadFile = $proposal->files->first();
    $previewFile = $proposal->files->firstWhere('mime_type', 'application/pdf') ?? $downloadFile;
@endphp

<div class="space-y-6 pb-32">
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-800 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <!-- Left: Proposal info -->
        <div class="flex-1 min-w-0">
          <div class="flex flex-col gap-3">
            <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900 truncate">
              {{ $proposal->title }}
            </h1>

            <div class="flex items-center gap-2">
              <span class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-3 py-1 text-blue-700 text-[12px] font-semibold">
                <span class="material-symbols-outlined">verified</span>
                {{ $proposal->status_label }}
              </span>

              <span class="inline-flex items-center gap-2 rounded-md bg-slate-50 px-3 py-1 text-slate-700 text-[12px] font-medium">
                <span class="material-symbols-outlined">badge</span>
                {{ $proposal->proposal_code ?? $proposal->id }}
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
                            </div>

                            <div class="mt-3 text-xs text-slate-400">
                                <span class="inline-flex items-center gap-2"><span class="material-symbols-outlined">calendar_month</span> Submitted {{ optional($proposal->submission_date)->format('d M Y') ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
        </div>

        <!-- Right: Reviewer widgets -->
        <div class="w-full lg:w-72 flex-shrink-0">
          <div class="grid grid-cols-1 gap-3">
            <!-- Review Deadline Card -->
            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm hover:shadow-md transition">
              <div class="flex items-start justify-between">
                <div class="flex items-start gap-3">
                  <div class="rounded-lg bg-blue-50 p-2.5 text-blue-600 flex-shrink-0">
                    <span class="material-symbols-outlined text-lg">event</span>
                  </div>
                  <div class="min-w-0">
                    <div class="text-xs text-slate-500 font-medium">REVIEW DEADLINE</div>
                                        <div class="text-sm font-semibold text-slate-900 mt-1">
                                            @php
                                                $deadline = null;
                                                if(isset($proposal->assignments)) {
                                                        $deadline = $proposal->assignments->where('role', \App\Models\ProposalAssignment::ROLE_REVIEWER)->pluck('due_date')->filter()->min();
                                                }
                                                if(!$deadline) {
                                                        $deadline = optional($proposal->review_deadline) ? optional($proposal->review_deadline)->toDateString() : null;
                                                }
                                            @endphp
                                            {{ $deadline ? \Carbon\Carbon::createFromFormat('Y-m-d', \Carbon\Carbon::parse($deadline)->toDateString())->format('d M Y') : (optional($proposal->created_at)->addDays(7)->format('d M Y') ?? '—') }}
                                        </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Assigned By Card -->
            @php
              $assignmentUser = optional($proposal->assignments()->where('role', 'reviewer')->first())->assignedBy;
            @endphp
            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm hover:shadow-md transition">
              <div class="flex items-start gap-3">
                <div class="rounded-lg bg-slate-50 p-2.5 text-slate-600 flex-shrink-0">
                  <span class="material-symbols-outlined text-lg">person_add</span>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="text-xs text-slate-500 font-medium">ASSIGNED BY</div>
                  <div class="text-sm font-semibold text-slate-900 mt-1 truncate">
                    {{ optional($assignmentUser)->name ?? 'Sekretaris' }}
                  </div>
                  <div class="text-xs text-slate-400 truncate">{{ optional($assignmentUser)->email ?? '' }}</div>
                </div>
              </div>
            </div>

            <!-- Progress Card -->
            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm hover:shadow-md transition">
              <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                  <div class="text-xs text-slate-500 font-medium">REVIEW PROGRESS</div>
                  <div id="progressText" class="text-xs text-slate-500 mt-1">0/5 items completed</div>
                  <div class="mt-3 w-full">
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                      <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%;"></div>
                    </div>
                  </div>
                </div>
                <div id="progressPercent" class="text-sm font-semibold text-slate-900 flex-shrink-0">0%</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[7fr_3fr]">
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900 mb-3">Submitted Documents</h3>
                        <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-slate-500 uppercase">
                                <th class="px-3 py-2">Document</th>
                                <th class="px-3 py-2">Type</th>
                                <th class="px-3 py-2">Version</th>
                                <th class="px-3 py-2">Download</th>
                                <th class="px-3 py-2">Preview</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposal->files as $f)
                                @php
                                    $typeLabel = $f->file_type ?? 'Optional';
                                    $isRequired = strtolower($typeLabel) === 'required' || strtolower($typeLabel) === 'proposal_document';
                                    $badgeClass = $isRequired ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700';
                                    $version = $f->version ?? 1;
                                @endphp
                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-slate-100 text-blue-600">
                                                @if(str_contains($f->mime_type, 'pdf')) <i class="fas fa-file-pdf"></i> @elseif(str_contains($f->mime_type, 'word') || str_contains($f->mime_type, 'msword') || str_ends_with(strtolower($f->original_name), '.docx') || str_ends_with(strtolower($f->original_name), '.doc')) <i class="fas fa-file-word"></i> @else <i class="fas fa-file"></i> @endif
                                            </span>
                                            <div>
                                                <div class="font-medium text-slate-900">{{ $f->original_name }}</div>
                                                <div class="text-xs text-slate-500">Uploaded {{ optional($f->created_at)->format('M d, Y') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="px-2 py-1 rounded-md text-xs font-semibold {{ $badgeClass }}">{{ $isRequired ? 'Required' : 'Optional' }}</span>
                                    </td>
                                    <td class="px-3 py-3">v{{ $version }}</td>
                                    <td class="px-3 py-3">
                                        <a href="{{ route('reviewer.proposal-file.download', ['file' => $f->id]) }}" class="text-green-600 hover:text-green-700" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                    <td class="px-3 py-3">
                                        <button type="button" onclick="togglePreviewRow({{ $f->id }}, '{{ $f->mime_type }}')" class="text-slate-600 hover:text-blue-600" title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="preview-row hidden bg-slate-50" data-file-id="{{ $f->id }}">
                                    <td colspan="5" class="px-3 py-4">
                                        <div id="preview-container-{{ $f->id }}" class="rounded-lg border border-slate-200 bg-white p-4">
                                            <!-- iframe or fallback will be injected by JS -->
                                            <div class="text-sm text-slate-500">Loading preview...</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($hasRevisions && $proposal->revisions->count() > 0)
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm mt-6">
                <div class="flex items-center gap-2 mb-4">
                    <span style="display: inline-flex; align-items: center; padding: 0.22rem 0.6rem; border-radius: 20px; font-size: 0.68rem; font-weight: 700; letter-spacing: .03em; background: #fce7f3; color: #be185d; gap: 0.3rem;">
                        ✎ REVISED DOCUMENTS
                    </span>
                </div>
                <p class="text-sm text-slate-600 mb-4">Peneliti telah mengirimkan revisi. Bandingkan dengan dokumen asli di bawah ini.</p>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-slate-500 uppercase">
                                <th class="px-3 py-2">Document</th>
                                <th class="px-3 py-2">Revision #</th>
                                <th class="px-3 py-2">Submitted</th>
                                <th class="px-3 py-2">Download</th>
                                <th class="px-3 py-2">Preview</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposal->revisions as $revision)
                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-slate-100 text-blue-600">
                                                <i class="fas fa-file-pdf"></i>
                                            </span>
                                            <div>
                                                <div class="font-medium text-slate-900">{{ $revision->file->original_name ?? ('Revisi #' . $revision->revision_number) }}</div>
                                                <div class="text-xs text-slate-500">{{ $revision->file ? 'Revision Document' : $revision->getStatusLabelAttribute() }}</div>
                                                @if(!$revision->file && $revision->revision_note)
                                                    <div class="text-xs text-slate-500 mt-1">Catatan: {{ $revision->revision_note }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="px-2 py-1 rounded-md text-xs font-semibold bg-pink-100 text-pink-700">v{{ $revision->file?->version ?? ($revision->revision_number + 1) }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-xs text-slate-600">{{ optional($revision->submitted_date ?? $revision->requested_date)->format('M d, Y') }}</td>
                                    <td class="px-3 py-3">
                                        @if($revision->file)
                                            <a href="{{ route('reviewer.proposal-file.download', ['file' => $revision->file->id]) }}" class="text-green-600 hover:text-green-700" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        @if($revision->file)
                                            <button type="button" onclick="togglePreviewRow({{ $revision->file->id }}, '{{ $revision->file->mime_type }}')" class="text-slate-600 hover:text-blue-600" title="Preview">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-400">No file</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($revision->file)
                                <tr class="preview-row hidden bg-slate-50" data-file-id="{{ $revision->file->id }}">
                                    <td colspan="5" class="px-3 py-4">
                                        <div id="preview-container-{{ $revision->file->id }}" class="rounded-lg border border-slate-200 bg-white p-4">
                                            <div class="text-sm text-slate-500">Loading preview...</div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Ethical Pillars Feedback</h2>
                <p class="mt-1 text-sm text-slate-500">Isi ringkasan review dan rekomendasi akhir.</p>
            </div>

            <form id="review-workspace-form" action="{{ route('reviewer.review-proposal.store') }}" method="POST" class="space-y-5 p-6">
                @csrf
                <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">
                <input type="hidden" name="save_mode" id="save_mode" value="draft">

                @php
                    $feedbackData = $draftFeedback ? json_decode($draftFeedback->feedback_text, true) : [];
                @endphp
                <div class="space-y-3">
                    <label for="autonomy" class="text-sm font-semibold text-slate-900 flex items-center gap-2"><span class="material-symbols-outlined pill-icon">shield</span>Autonomy</label>
                    <textarea id="autonomy" name="autonomy" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-300 placeholder:italic focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" placeholder="Review the informed consent process and participant freedom...">{{ old('autonomy') ?? ($feedbackData['autonomy'] ?? '') }}</textarea>
                    @error('autonomy') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-3">
                    <label for="beneficence" class="text-sm font-semibold text-slate-900 flex items-center gap-2"><span class="material-symbols-outlined pill-icon">favorite</span>Beneficence</label>
                    <textarea id="beneficence" name="beneficence" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-300 placeholder:italic focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" placeholder="Review the potential benefits and risks of the research...">{{ old('beneficence') ?? ($feedbackData['beneficence'] ?? '') }}</textarea>
                    @error('beneficence') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-3">
                    <label for="justice" class="text-sm font-semibold text-slate-900 flex items-center gap-2"><span class="material-symbols-outlined pill-icon">gavel</span>Justice</label>
                    <textarea id="justice" name="justice" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-300 placeholder:italic focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" placeholder="Review the fairness and equity of the research design...">{{ old('justice') ?? ($feedbackData['justice'] ?? '') }}</textarea>
                    @error('justice') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-3">
                    <label for="general_comments" class="block text-sm font-semibold text-slate-900">General Comments</label>
                    <textarea id="general_comments" name="general_comments" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-300 placeholder:italic focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100" placeholder="Provide overall remarks, strengths, and weaknesses of the proposal...">{{ old('general_comments') ?? ($feedbackData['general_comments'] ?? '') }}</textarea>
                    @error('general_comments') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-4">
                    <p class="mb-3 text-sm font-semibold text-slate-900">
                        Final Recommendation
                    </p>

                    <div class="grid grid-cols-2 gap-3">

                        <!-- Approve -->
                        <label class="cursor-pointer rounded-xl border border-slate-200 p-4 transition-all hover:border-emerald-400 peer-has-[:checked]:border-emerald-500 peer-has-[:checked]:bg-emerald-50">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="recommendation"
                                    value="approved"
                                    class="h-5 w-5 accent-emerald-600"
                                    {{ old('recommendation') === 'approved' || $draftFeedback?->recommendation === 'approved' ? 'checked' : '' }}>

                                <span class="text-xs font-medium text-slate-800">
                                    Approve
                                </span>
                            </div>
                        </label>

                        <!-- Revision (merged minor/major) -->
                        <label class="cursor-pointer rounded-xl border border-slate-200 p-4 transition-all hover:border-blue-400">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="recommendation"
                                    value="revision"
                                    class="h-5 w-5 accent-blue-600"
                                    {{ old('recommendation') === 'revision' || $draftFeedback?->recommendation === 'revision' ? 'checked' : '' }}>

                                <span class="text-xs font-medium text-slate-800">
                                    Revision
                                </span>
                            </div>
                        </label>

                        <!-- Reject -->
                        <label class="cursor-pointer rounded-xl border border-slate-200 p-4 transition-all hover:border-red-400">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="recommendation"
                                    value="rejected"
                                    class="h-5 w-5 accent-red-600"
                                    {{ old('recommendation') === 'rejected' || $draftFeedback?->recommendation === 'rejected' ? 'checked' : '' }}>

                                <span class="text-xs font-medium text-slate-800">
                                    Reject
                                </span>
                            </div>
                        </label>

                    </div>
                    @error('recommendation') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </form>
        </div>
    </div>
</div>

<div class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white/95 px-4 py-4 shadow-xl backdrop-blur-sm">
    <div class="mx-auto flex max-w-7xl flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
        <button type="button" onclick="submitReviewForm('submit')" class="w-full rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 sm:w-auto">
            Submit Review
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function submitReviewForm(mode) {
        document.getElementById('save_mode').value = mode;
        const form = document.getElementById('review-workspace-form');

        if (mode === 'draft') {
            Swal.fire({
                title: 'Simpan Draft?',
                text: 'Perubahan Anda akan disimpan sebagai draft. Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Simpan Draft',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'swal2-confirm bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl px-4 py-2',
                    cancelButton: 'swal2-cancel bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl px-4 py-2'
                },
                didOpen(popup) {
                    // ensure SweetAlert overlays above fixed bottom bar
                    popup.parentElement.style.zIndex = '99999';
                    const backdrop = document.querySelector('.swal2-container');
                    if (backdrop) backdrop.style.zIndex = '99999';
                }
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return;
        }

        if (mode === 'submit') {
            Swal.fire({
                title: 'Submit Review?',
                text: 'Anda akan mengirimkan review final. Pastikan semua bidang sudah terisi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'swal2-confirm bg-blue-600 hover:bg-blue-700 text-white rounded-2xl px-4 py-2',
                    cancelButton: 'swal2-cancel bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl px-4 py-2'
                },
                didOpen(popup) {
                    popup.parentElement.style.zIndex = '99999';
                    const backdrop = document.querySelector('.swal2-container');
                    if (backdrop) backdrop.style.zIndex = '99999';
                }
            }).then(result => {
                if (result.isConfirmed) {
                    // optionally show a loading toast then submit
                    Swal.fire({
                        title: 'Mengirim...',
                        allowOutsideClick: false,
                        didOpen() { Swal.showLoading(); }
                    });
                    form.submit();
                }
            });
            return;
        }

        // fallback
        form.submit();
    }

    function selectFile(fileId, mime, name) {
        const previewUrl = "{{ url('reviewer/proposal-file') }}" + '/' + fileId + '/preview';
        const downloadUrl = "{{ url('reviewer/proposal-file') }}" + '/' + fileId + '/download';

        // update preview name
        const nameEl = document.getElementById('currentPreviewName');
        if (nameEl) nameEl.textContent = name;

        // show appropriate viewer
        const iframe = document.getElementById('previewIframe');
        const docFallback = document.getElementById('docFallback');
        const noDoc = document.getElementById('noDoc');
        const downloadLink = document.getElementById('docDownloadLink');

        if (mime && mime.includes('pdf')) {
            if (iframe) {
                iframe.src = previewUrl + '?t=' + Date.now();
                iframe.style.display = '';
            }
            if (docFallback) docFallback.style.display = 'none';
            if (noDoc) noDoc.style.display = 'none';
            if (downloadLink) downloadLink.setAttribute('href', downloadUrl);
        } else {
            if (iframe) iframe.style.display = 'none';
            if (docFallback) {
                docFallback.style.display = '';
                if (downloadLink) downloadLink.setAttribute('href', downloadUrl);
            }
            if (noDoc) noDoc.style.display = 'none';
        }

        setSelectedCard(fileId);
        // scroll to viewer
        const viewer = document.getElementById('documentViewer') || document.getElementById('docFallback') || document.getElementById('noDoc');
        if (viewer) viewer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function setSelectedCard(fileId) {
        document.querySelectorAll('.file-card').forEach(el => {
            el.classList.remove('ring-2', 'ring-blue-100');
        });
        const sel = document.querySelector('.file-card[data-file-id="' + fileId + '"]');
        if (sel) sel.classList.add('ring-2', 'ring-blue-100');
    }

    // initialize selection from server previewFile
    document.addEventListener('DOMContentLoaded', function() {
        const initial = {{ $previewFile?->id ?? 'null' }};
        if (initial) {
            setSelectedCard(initial);
        }
    });

    function togglePreviewRow(fileId, mime) {
        // hide other preview rows
        document.querySelectorAll('.preview-row').forEach(r => {
            if (r.getAttribute('data-file-id') != String(fileId)) r.classList.add('hidden');
        });

        const row = document.querySelector('.preview-row[data-file-id="' + fileId + '"]');
        if (!row) return;

        const container = document.getElementById('preview-container-' + fileId);
        // toggle
        if (!row.classList.contains('hidden')) {
            row.classList.add('hidden');
            return;
        }

        // prepare content safely
        const baseUrl = "{{ url('reviewer/proposal-file') }}";
        container.innerHTML = '';
        if (mime && mime.includes('pdf')) {
            const iframe = document.createElement('iframe');
            iframe.src = baseUrl + '/' + fileId + '/preview?t=' + Date.now();
            iframe.className = 'w-full h-[560px] border-0';
            iframe.setAttribute('frameborder', '0');
            container.appendChild(iframe);
        } else {
            const downloadUrl = baseUrl + '/' + fileId + '/download';
            const wrap = document.createElement('div');
            wrap.className = 'flex flex-col items-center gap-3 py-8';
            wrap.innerHTML = '<div class="text-lg font-semibold">Preview tidak tersedia</div><div class="text-sm text-slate-500">Silakan unduh dokumen untuk melihatnya.</div>';
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.className = 'mt-3 inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-2 text-sm text-white';
            a.textContent = 'Download';
            wrap.appendChild(a);
            container.appendChild(wrap);
        }

        row.classList.remove('hidden');
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Track review progress
    function updateReviewProgress() {
        const fields = ['autonomy', 'beneficence', 'justice', 'general_comments'];
        let completed = 0;
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && field.value.trim().length > 0) {
                completed++;
            }
        });
        // Check if any radio button is checked
        const recommendationChecked = document.querySelector('input[name="recommendation"]:checked');
        if (recommendationChecked) {
            completed++;
        }
        const progress = Math.round((completed / 5) * 100);
        document.getElementById('progressBar').style.width = progress + '%';
        document.getElementById('progressPercent').textContent = progress + '%';
        document.getElementById('progressText').textContent = completed + '/5 items completed';
    }

    // Initialize progress on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateReviewProgress();
        
        // Listen for changes in form fields
        const fields = ['autonomy', 'beneficence', 'justice', 'general_comments'];
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', updateReviewProgress);
                field.addEventListener('change', updateReviewProgress);
            }
        });

        // Setup radio button toggle behavior
        const radioButtons = document.querySelectorAll('input[name="recommendation"]');
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                updateReviewProgress();
                // Store the selected value
                document.body.setAttribute('data-selected-recommendation', this.value);
            });
            
            // Handle click to allow deselection
            radio.addEventListener('click', function(e) {
                const currentSelected = document.body.getAttribute('data-selected-recommendation');
                if (currentSelected === this.value) {
                    // If clicking the same option, deselect it
                    this.checked = false;
                    document.body.removeAttribute('data-selected-recommendation');
                    updateReviewProgress();
                    e.preventDefault();
                } else {
                    // If clicking a different option, select it (normal radio behavior)
                    document.body.setAttribute('data-selected-recommendation', this.value);
                }
            });
        });

        const initial = {{ $previewFile?->id ?? 'null' }};
        if (initial) {
            setSelectedCard(initial);
        }
    });
</script>
@endsection
