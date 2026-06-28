<?php

namespace App\Http\Controllers\Peneliti;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GenerateStyledPdfTrait;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\ProposalFile;
use App\Models\ReviewFeedback;
use App\Models\TemplateProposal;
use App\Models\Notification;
use App\Models\User;
use App\Models\EthicsDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    use GenerateStyledPdfTrait;

    /**
     * Helper untuk cek akses menu pengajuan
     * @return string|null - 'guest', 'pending', atau null untuk aktif
     */
    private function checkAccess()
    {
        if (!Auth::check()) {
            return 'guest';
        }

        if (Auth::user()->status !== 'active') {
            return 'pending';
        }

        return null; // aktif, bisa akses
    }

    /**
     * Halaman Upload Proposal
     */
    public function uploadProposal()
    {
        $access = $this->checkAccess();

        if ($access === 'guest') {
            return view('peneliti.pengajuan.guest-message', [
                'title' => 'Upload Proposal',
                'message' => 'Silakan login terlebih dahulu untuk mengunggah proposal.',
                'icon' => 'upload_file'
            ]);
        }

        if ($access === 'pending') {
            return view('peneliti.pengajuan.pending-message', [
                'title' => 'Upload Proposal',
                'message' => 'Akun Anda sedang menunggu aktivasi oleh sekretariat. Setelah diaktivasi, Anda dapat mengunggah proposal.',
                'icon' => 'pending'
            ]);
        }

        // User aktif - tampilkan konten sebenarnya
        $templates = TemplateProposal::where('is_active', true)
            ->orderBy('kategori')
            ->orderBy('nama_dokumen')
            ->get();

        $proposalData = session('proposal_step1', []);

        return view('peneliti.pengajuan.upload-proposal', compact('templates', 'proposalData'));
    }

    /**
     * Halaman Download Template
     */
    public function downloadTemplate()
    {
        $access = $this->checkAccess();

        if ($access === 'guest') {
            return view('peneliti.pengajuan.guest-message', [
                'title' => 'Download Template',
                'message' => 'Silakan login terlebih dahulu untuk mendownload template.',
                'icon' => 'download'
            ]);
        }

        if ($access === 'pending') {
            return view('peneliti.pengajuan.pending-message', [
                'title' => 'Download Template',
                'message' => 'Akun Anda sedang menunggu aktivasi oleh sekretariat. Setelah diaktivasi, Anda dapat mendownload template.',
                'icon' => 'pending'
            ]);
        }

        // User aktif - tampilkan konten sebenarnya
        $templates = TemplateProposal::where('is_active', true)
            ->orderBy('kategori')
            ->orderBy('nama_dokumen')
            ->get()
            ->groupBy('kategori');

        return view('peneliti.pengajuan.download-template', compact('templates'));
    }

    /**
     * Halaman Riwayat Pengajuan dengan tracking proposal
     */
    public function riwayatPengajuan()
    {
        $access = $this->checkAccess();

        if ($access === 'guest') {
            return view('peneliti.pengajuan.guest-message', [
                'title' => 'Riwayat Pengajuan',
                'message' => 'Silakan login terlebih dahulu untuk melihat riwayat pengajuan.',
                'icon' => 'history'
            ]);
        }

        if ($access === 'pending') {
            return view('peneliti.pengajuan.pending-message', [
                'title' => 'Riwayat Pengajuan',
                'message' => 'Akun Anda sedang menunggu aktivasi oleh sekretariat. Setelah diaktivasi, Anda dapat melihat riwayat pengajuan.',
                'icon' => 'pending'
            ]);
        }

        // User aktif - tampilkan konten sebenarnya
        $proposals = Proposal::with([
            'reviewFeedbacks' => function ($query) {
                $query->where('is_submitted', true)
                    ->with(['review.reviewer']);
            },
            'revisions' => function ($query) {
                $query->where('status', \App\Models\ProposalRevision::STATUS_REQUESTED)
                    ->orderByDesc('revision_number');
            },
        ])
            ->where('user_id', Auth::id())
            ->orderByDesc('submission_date')
            ->orderByDesc('created_at')
            ->get();

        $proposals->each(function ($proposal) {
            $proposal->pendingRevisionRequest = $proposal->revisions->isNotEmpty();
            $proposal->reviewFeedbacks->transform(function ($fb) {
                if (is_array($fb->feedback_text)) {
                    $parsed = $fb->feedback_text;
                } elseif (is_string($fb->feedback_text)) {
                    $decoded = json_decode($fb->feedback_text, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $parsed = $decoded;
                    } else {
                        $parsed = ['summary' => $fb->feedback_text];
                    }
                } else {
                    $parsed = ['summary' => (string) $fb->feedback_text];
                }

                $fb->parsed_feedback = $parsed;
                return $fb;
            });
        });

        return view('peneliti.pengajuan.riwayat-pengajuan', compact('proposals'));
    }

    /**
     * Detail feedback reviewer untuk proposal tertentu
     */
    public function showProposalFeedback(Proposal $proposal)
    {
        $access = $this->checkAccess();

        if ($access === 'guest') {
            return view('peneliti.pengajuan.guest-message', [
                'title' => 'Detail Feedback Proposal',
                'message' => 'Silakan login terlebih dahulu untuk melihat detail feedback proposal Anda.',
                'icon' => 'history'
            ]);
        }

        if ($access === 'pending') {
            return view('peneliti.pengajuan.pending-message', [
                'title' => 'Detail Feedback Proposal',
                'message' => 'Akun Anda sedang menunggu aktivasi oleh sekretariat. Setelah diaktivasi, Anda dapat melihat detail feedback proposal Anda.',
                'icon' => 'pending'
            ]);
        }

        if ($proposal->user_id !== Auth::id()) {
            abort(403);
        }

        $proposal->load(['files' => function ($q) {
            $q->where('is_active', true);
        }]);

        $feedbacks = ReviewFeedback::with(['review.reviewer'])
            ->where('proposal_id', $proposal->id)
            ->where('is_submitted', true)
            ->orderByDesc('submitted_at')
            ->get();

        // Load secretary notes
        $secretaryNotes = \App\Models\ProposalNote::where('proposal_id', $proposal->id)
            ->where('note_type', \App\Models\ProposalNote::TYPE_SECRETARY)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        // Parse feedback_text into structured parsed_feedback for view rendering
        $feedbacks->transform(function ($fb) {
            if (is_array($fb->feedback_text)) {
                $parsed = $fb->feedback_text;
            } elseif (is_string($fb->feedback_text)) {
                $decoded = json_decode($fb->feedback_text, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $parsed = $decoded;
                } else {
                    $parsed = ['summary' => $fb->feedback_text];
                }
            } else {
                $parsed = ['summary' => (string) $fb->feedback_text];
            }

            $fb->parsed_feedback = $parsed;
            return $fb;
        });

        $summary = [
            'approved' => $feedbacks->where('recommendation', ReviewFeedback::RECOMMENDATION_APPROVED)->count(),
            'revision' => $feedbacks->where('recommendation', ReviewFeedback::RECOMMENDATION_REVISION)->count(),
            'rejected' => $feedbacks->where('recommendation', ReviewFeedback::RECOMMENDATION_REJECTED)->count(),
            'total_reviewers' => $feedbacks->count(),
        ];

        $feedbacks->transform(function ($fb) {
            if (is_array($fb->feedback_text)) {
                $parsed = $fb->feedback_text;
            } elseif (is_string($fb->feedback_text)) {
                $decoded = json_decode($fb->feedback_text, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $parsed = $decoded;
                } else {
                    $parsed = ['summary' => $fb->feedback_text];
                }
            } else {
                $parsed = ['summary' => (string) $fb->feedback_text];
            }

            $fb->parsed_feedback = $parsed;
            return $fb;
        });

        $proposal->pendingRevisionRequest = $proposal->revisions()->where('status', \App\Models\ProposalRevision::STATUS_REQUESTED)->exists();

        return view('peneliti.pengajuan.riwayat-pengajuan-show', compact('proposal', 'feedbacks', 'secretaryNotes', 'summary'));
    }

    /**
     * Show revision upload page for a proposal (peneliti)
     */
    public function showRevisionForm(Proposal $proposal)
    {
        $access = $this->checkAccess();

        if ($access === 'guest') {
            return redirect()->route('login');
        }
        if ($access === 'pending') {
            return redirect()->route('peneliti.dashboard')->with('error', 'Akun belum diaktivasi.');
        }

        if ($proposal->user_id !== Auth::id()) {
            abort(403);
        }

        // Load ALL proposal document versions (including all revisions submitted so far)
        // This allows researcher to see previous revision versions when uploading new revisions
        $files = \App\Models\ProposalFile::where('proposal_id', $proposal->id)
            ->orderByDesc('version')
            ->get()
            ->groupBy(function ($f) {
                // Prefer group_name if exists (new revisions), fallback to original_name
                return $f->group_name ?? $f->original_name; // group by persistent identity
            });

        $revisions = $proposal->revisions()->with('file')->orderByDesc('submitted_date')->get();

        // Determine the latest proposal file metadata for the revision form
        $latestFile = $files->flatten()->sortByDesc('version')->first();
        $originalName = $latestFile ? ($latestFile->group_name ?? $latestFile->original_name) : null;
        $latest = $latestFile;

        // Determine current revision upload state
        $pendingRevision = $proposal->revisions()
            ->where('status', \App\Models\ProposalRevision::STATUS_REQUESTED)
            ->latest('revision_number')
            ->first();

        $submittedRevision = $proposal->revisions()
            ->where('status', \App\Models\ProposalRevision::STATUS_SUBMITTED)
            ->latest('submitted_date')
            ->first();

        $canUploadRevision = $pendingRevision !== null;
        $revisionUploadState = $canUploadRevision ? 'requested' : ($submittedRevision ? 'submitted' : 'none');

        $feedbacks = ReviewFeedback::with(['review.reviewer'])
            ->where('proposal_id', $proposal->id)
            ->where('is_submitted', true)
            ->orderByDesc('submitted_at')
            ->get();

        // Get secretary revision notes
        $secretaryRevisionNotes = \App\Models\ProposalNote::with('user')
            ->where('proposal_id', $proposal->id)
            ->where('note_type', \App\Models\ProposalNote::TYPE_SECRETARY)
            ->orderByDesc('created_at')
            ->get();

        return view('peneliti.pengajuan.revision-upload', compact('proposal', 'files', 'revisions', 'feedbacks', 'canUploadRevision', 'secretaryRevisionNotes', 'revisionUploadState', 'originalName', 'latest'));
    }

    /**
     * Submit a revision for a proposal (by researcher)
     */
    public function submitRevision(Request $request, Proposal $proposal)
    {
        $access = $this->checkAccess();

        if ($access === 'guest') {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($access === 'pending') {
            return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
        }

        if ($proposal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'revision_files' => 'required|array|min:1',
            'revision_files.*' => 'nullable|file|mimes:pdf|max:5120',
            'revision_note' => 'nullable|string|max:2000'
        ]);

        // Check if there's a pending revision request from secretary
        $pendingRevision = $proposal->revisions()
            ->where('status', \App\Models\ProposalRevision::STATUS_REQUESTED)
            ->first();

        if (!$pendingRevision) {
            return redirect()->route('pengajuan.riwayat-pengajuan')
                ->with('error', 'Belum ada permintaan revisi dari sekretaris. Anda tidak dapat mengunggah revisi saat ini.');
        }

        // Filter out null files (files that weren't uploaded)
        $filesToUpload = array_filter($request->file('revision_files'), function ($file) {
            return $file !== null;
        });

        if (empty($filesToUpload)) {
            return redirect()->back()->with('error', 'Minimal satu file harus diunggah.');
        }

        $uploadedFiles = [];
        $uploadPath = "proposal_revisions/" . $proposal->user_id . "/" . now()->format('YmdHis');

        // Process each uploaded file
        foreach ($filesToUpload as $targetFileId => $file) {
            $filePath = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs($uploadPath, $file, uniqid('rev_') . '.' . $file->getClientOriginalExtension());

            // Determine version: increment within the same original file group
            $version = 1;
            $target = \App\Models\ProposalFile::where('proposal_id', $proposal->id)
                ->where('id', $targetFileId)
                ->first();

            // Determine grouping key and display name
            if ($target) {
                $groupKey = $target->group_name ?? $target->original_name;
                $displayName = $file->getClientOriginalName();
                $maxVersion = \App\Models\ProposalFile::where('proposal_id', $proposal->id)
                    ->where(function ($q) use ($groupKey) {
                        $q->where('group_name', $groupKey)->orWhere('original_name', $groupKey);
                    })
                    ->max('version');

                $version = ($maxVersion ?? 0) + 1;
            } else {
                $groupKey = $file->getClientOriginalName();
                $displayName = $file->getClientOriginalName();
                $maxVersion = \App\Models\ProposalFile::where('proposal_id', $proposal->id)
                    ->where('original_name', $groupKey)
                    ->max('version');

                $version = ($maxVersion ?? 0) + 1;
            }

            // Create ProposalFile record for this uploaded revision
            $pf = \App\Models\ProposalFile::create([
                'proposal_id' => $proposal->id,
                'file_path' => $filePath,
                'file_type' => \App\Models\ProposalFile::TYPE_REVISION,
                // store display filename in original_name, keep group_key in group_name
                'original_name' => $displayName,
                'group_name' => $groupKey,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'version' => $version,
                'is_active' => false,
            ]);

            $uploadedFiles[] = $pf->id;
        }

        // Update the pending revision record with uploaded files
        $pendingRevision->update([
            'status' => \App\Models\ProposalRevision::STATUS_SUBMITTED,
            'submitted_date' => now(),
            'file_id' => $uploadedFiles[0], // Store first file as primary reference
            'revision_note' => $request->revision_note,
        ]);

        // Update proposal status to ON_REVIEW sehingga sekretaris dapat melihatnya sebagai pada proses review
        $proposal->updateStatus(\App\Models\Proposal::STATUS_ON_REVIEW);

        // Reset decision_date so secretary can make a new decision on the revised proposal
        $proposal->decision_date = null;
        $proposal->save();

        // Ensure proposal reflects in_process status after submission
        if ($proposal->status !== \App\Models\Proposal::STATUS_ON_REVIEW) {
            $proposal->updateStatus(\App\Models\Proposal::STATUS_ON_REVIEW);
            $proposal->refresh();
        }

        // Pastikan proposal revisi kembali berada di tab "In Process" untuk sekretaris yang bertanggung jawab.
        $sekretarisId = $proposal->sekretaris_id
            ?? ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->value('assigned_to');

        // Jika belum ada sekretaris yang pernah ditetapkan, pilih satu sekretaris aktif secara default
        if (! $sekretarisId) {
            $candidate = User::whereHas('roles', function ($q) {
                $q->where('name', 'sekretaris');
            })->where('status', 'active')->first();

            if ($candidate) {
                $sekretarisId = $candidate->id;
            }
        }

        if ($sekretarisId) {
            // Simpan sekretaris_id ke proposal untuk konsistensi referensi
            if ($proposal->sekretaris_id !== $sekretarisId) {
                $proposal->sekretaris_id = $sekretarisId;
                $proposal->save();
            }
            $assignment = ProposalAssignment::firstOrNew([
                'proposal_id' => $proposal->id,
                'role' => ProposalAssignment::ROLE_SEKRETARIS,
                'assigned_to' => $sekretarisId,
            ]);

            if (! $assignment->exists) {
                $assignment->assigned_by = Auth::id();
                $assignment->sent_at = now();
                $assignment->save();
            } elseif (! $assignment->sent_at) {
                $assignment->sent_at = now();
                $assignment->save();
            }

            Notification::create([
                'user_id' => $sekretarisId,
                'title' => 'Proposal Revisi Dikirim',
                'message' => 'Proposal "' . $proposal->title . '" telah dikirim kembali oleh peneliti setelah revisi. Silakan cek tab Revised di Manajemen Proposal.',
                'type' => Notification::TYPE_PROPOSAL_STATUS ?? 'proposal_status',
                'status' => Notification::STATUS_UNREAD ?? 'unread',
                'data' => json_encode([
                    'proposal_id' => $proposal->id,
                    'revision_status' => 'submitted',
                ]),
            ]);
        }

        return redirect()->back()->with('success', 'Revisi berhasil dikirim.');
    }

    /**
     * Store informasi dasar proposal
     */
    public function store(Request $request)
    {
        $access = $this->checkAccess();

        if ($access !== null) {
            if ($access === 'guest') {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengajukan proposal.');
            }
            if ($access === 'pending') {
                return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi. Silakan tunggu aktivasi dari sekretariat.');
            }
        }

        // Validate basic information
        $validated = $request->validate([
            'nama_peneliti' => 'required|string|max:255',
            'asal_instansi' => 'required|string|max:255',
            'judul_penelitian' => 'required|string|max:255',
            'jenis_penelitian' => 'required|string|in:Kualitatif,Kuantitatif,Mixed Methods,Eksperimental,Deskriptif',
            'bidang_ilmu' => 'required|string|in:Biomedis,Sosial,Pendidikan,Teknik,Humaniora',
            'lokasi_penelitian' => 'required|string|max:255',
        ]);

        $request->session()->put('proposal_step1', $validated);

        return redirect()->route('pengajuan.upload-berkas');
    }

    /**
     * Halaman upload berkas
     */
    public function uploadBerkas()
    {
        $access = $this->checkAccess();

        if ($access !== null) {
            if ($access === 'guest') {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }
            if ($access === 'pending') {
                return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
            }
        }

        if (!session()->has('proposal_step1')) {
            return redirect()->route('pengajuan.upload-proposal')->with('info', 'Silakan lengkapi informasi dasar terlebih dahulu.');
        }

        $templates = TemplateProposal::where('is_active', true)
            ->orderBy('kategori')
            ->orderBy('nama_dokumen')
            ->get();

        $uploadedFiles = session('proposal_step2', []);

        return view('peneliti.pengajuan.upload-berkas', compact('templates', 'uploadedFiles'));
    }

    /**
     * Submit dokumen dan lanjut ke review
     */
    public function submitBerkas(Request $request)
    {
        $access = $this->checkAccess();

        if ($access !== null) {
            if ($access === 'guest') {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }
            if ($access === 'pending') {
                return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
            }
        }

        // Get all active templates
        $templates = TemplateProposal::where('is_active', true)->get();

        $sessionFiles = $request->session()->get('proposal_step2', []);

        $rules = [];
        $messages = [];
        foreach ($templates as $template) {
            $fieldName = 'template_' . $template->id;
            $hasSessionFile = isset($sessionFiles[$fieldName]);

            $rules[$fieldName] = $hasSessionFile ? 'nullable|file|mimes:pdf|max:5120' : 'required|file|mimes:pdf|max:5120';

            if (! $hasSessionFile) {
                $messages[$fieldName . '.required'] = "Dokumen '{$template->nama_dokumen}' wajib diupload.";
            }
            $messages[$fieldName . '.mimes'] = "Dokumen '{$template->nama_dokumen}' harus berformat PDF.";
            $messages[$fieldName . '.max'] = "Dokumen '{$template->nama_dokumen}' tidak boleh melebihi 5MB.";
        }

        $request->validate($rules, $messages);

        $userId = Auth::id();
        $timestamp = now()->format('YmdHis');
        $uploadPath = "proposal_uploads/{$userId}/{$timestamp}";

        $storedFiles = [];
        foreach ($templates as $template) {
            $fieldName = 'template_' . $template->id;
            $file = $request->file($fieldName);

            if ($file) {
                $filename = uniqid('template_' . $template->id . '_') . '.' . $file->getClientOriginalExtension();
                $filePath = Storage::disk('public')->putFileAs($uploadPath, $file, $filename);

                $storedFiles[$fieldName] = [
                    'template_id' => $template->id,
                    'template_name' => $template->nama_dokumen,
                    'path' => $filePath,
                    'original_name' => $file->getClientOriginalName(),
                    'original_ext' => $file->getClientOriginalExtension(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'file_type' => ProposalFile::TYPE_PROPOSAL,
                ];
            } elseif (isset($sessionFiles[$fieldName])) {
                $storedFiles[$fieldName] = $sessionFiles[$fieldName];
            }
        }

        $request->session()->put('proposal_step2', $storedFiles);

        return redirect()->route('pengajuan.review');
    }

    /**
     * Halaman review dan submit
     */
    public function review()
    {
        $access = $this->checkAccess();

        if ($access !== null) {
            if ($access === 'guest') {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }
            if ($access === 'pending') {
                return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
            }
        }

        if (!session()->has('proposal_step1') || !session()->has('proposal_step2')) {
            return redirect()->route('pengajuan.upload-proposal')->with('info', 'Silakan lengkapi semua tahap pengajuan.');
        }

        $proposalData = session('proposal_step1');
        $proposalFiles = session('proposal_step2');

        return view('peneliti.pengajuan.review', compact('proposalData', 'proposalFiles'));
    }

    /**
     * Show researcher page for admin-issued draft ethical clearances
     */
    public function ethicalClearance()
    {
        $access = $this->checkAccess();
        if ($access === 'guest') {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        if ($access === 'pending') {
            return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
        }

        $documents = EthicsDocument::with('proposal')
            ->whereHas('proposal', function ($query) {
                $query->where('user_id', Auth::id())
                    ->whereIn('status', [Proposal::STATUS_WAITING_FOR_CONFIRMATION, Proposal::STATUS_WITH_CHAIR, Proposal::STATUS_PUBLISHED]);
            })
            ->whereIn('status', [EthicsDocument::STATUS_DRAFT, EthicsDocument::STATUS_PUBLISHED])
            ->where(function ($query) {
                $query->whereRaw("JSON_VALID(notes) = 1 AND JSON_EXTRACT(notes, '$.assigned_admin_id') IS NOT NULL")
                    ->orWhere('status', EthicsDocument::STATUS_PUBLISHED);
            })
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('proposal_id')
            ->map(fn($group) => $group->first())
            ->values();

        return view('peneliti.pengajuan.ethical-clearance', compact('documents'));
    }

    private function resolvePreviewValue(array $data, string $field, array $notes, Proposal $proposal, string $fallback = ''): string
    {
        $incoming = $data[$field] ?? null;

        if (is_string($incoming)) {
            $incoming = trim($incoming);
        }

        if ($incoming !== null && $incoming !== '') {
            return (string) $incoming;
        }

        $noteValue = $notes[$field] ?? null;
        if (is_string($noteValue)) {
            $noteValue = trim($noteValue);
        }

        if ($noteValue !== null && $noteValue !== '') {
            return (string) $noteValue;
        }

        return $fallback;
    }

    private function getLatestDraftDocument(Proposal $proposal): ?EthicsDocument
    {
        return EthicsDocument::where('proposal_id', $proposal->id)
            ->where('status', EthicsDocument::STATUS_DRAFT)
            ->latest('created_at')
            ->first();
    }

    private function resolveRevisionDocumentNumber(?EthicsDocument $sourceDocument, Proposal $proposal, ?int $currentDocumentId = null): string
    {
        $baseNumber = trim((string) ($proposal->nomor_ec ?? $sourceDocument?->document_number ?? ''));

        if ($baseNumber === '') {
            $baseNumber = 'EC-' . now()->format('Y-m-d');
        }

        $candidate = $baseNumber;
        $counter = 1;

        while (
            EthicsDocument::where('document_number', $candidate)
            ->when($currentDocumentId !== null, fn($query) => $query->where('id', '!=', $currentDocumentId))
            ->exists()
        ) {
            $candidate = $baseNumber . '-rev-' . $counter++;
        }

        return $candidate;
    }

    private function buildCertificatePreviewData(Proposal $proposal, ?EthicsDocument $ethicsDocument = null, ?ProposalAssignment $assignment = null): array
    {
        $notes = [];

        if ($ethicsDocument) {
            $decoded = json_decode($ethicsDocument->notes ?: '{}', true);
            if (is_array($decoded)) {
                $notes = $decoded;
            } else {
                $notes = ['notes' => (string) $ethicsDocument->notes];
            }
        }

        if (!$assignment) {
            $assignment = ProposalAssignment::where('proposal_id', $proposal->id)
                ->where('role', ProposalAssignment::ROLE_KETUA)
                ->latest('created_at')
                ->first();
        }

        return [
            'title' => $this->resolvePreviewValue([], 'title', $notes, $proposal, $proposal->title ?? ''),
            'principal_investigator' => $this->resolvePreviewValue([], 'principal_investigator', $notes, $proposal, $proposal->researcher?->name ?? $proposal->nama_peneliti ?? ''),
            'members' => $this->resolvePreviewValue([], 'members', $notes, $proposal),
            'institution' => $this->resolvePreviewValue([], 'institution', $notes, $proposal, optional($proposal->researcher)->institution ?? $proposal->asal_instansi ?? ''),
            'research_place' => $this->resolvePreviewValue([], 'research_place', $notes, $proposal),
            'nomor_ec' => $this->resolvePreviewValue([], 'nomor_ec', $notes, $proposal, $proposal->nomor_ec ?? $ethicsDocument?->document_number ?? ''),
            'chair_name' => $this->resolvePreviewValue([], 'chair_name', $notes, $proposal, $assignment?->assignedTo?->name ?? ''),
            'assigned_admin_id' => $notes['assigned_admin_id'] ?? null,
            'assigned_at' => $notes['assigned_at'] ?? null,
        ];
    }

    private function buildCertificatePdf(EthicsDocument $ethicsDocument, Proposal $proposal): array
    {
        $notes = [];
        if (!empty($ethicsDocument->notes)) {
            $decoded = json_decode($ethicsDocument->notes, true);
            if (is_array($decoded)) {
                $notes = $decoded;
            } else {
                $notes = ['notes' => (string) $ethicsDocument->notes];
            }
        }

        $certificatePreviewData = [
            'title' => $this->resolvePreviewValue([], 'title', $notes, $proposal, $proposal->title ?? '-'),
            'principal_investigator' => $this->resolvePreviewValue([], 'principal_investigator', $notes, $proposal, $proposal->researcher?->name ?? $proposal->nama_peneliti ?? '-'),
            'members' => $this->resolvePreviewValue([], 'members', $notes, $proposal, '-'),
            'institution' => $this->resolvePreviewValue([], 'institution', $notes, $proposal, optional($proposal->researcher)->institution ?? $proposal->asal_instansi ?? '-'),
            'research_place' => $this->resolvePreviewValue([], 'research_place', $notes, $proposal, '-'),
            'nomor_ec' => $ethicsDocument->document_number ?: $proposal->nomor_ec ?: '-',
            'chair_name' => $this->resolvePreviewValue([], 'chair_name', $notes, $proposal, 'Ketua Komite Etik'),
        ];

        $view = view('peneliti.pengajuan.partials.ethical-clearance-document', [
            'certificatePreviewData' => $certificatePreviewData,
            'issuedAt' => now()->locale('id')->isoFormat('D MMMM Y'),
            'pdfMode' => true,
        ])->render();

        $fileName = 'ethical-clearance-' . $proposal->id . '-' . $ethicsDocument->id . '-' . now()->format('YmdHis') . '.pdf';
        $path = 'ethics-documents/' . $fileName;

        $pdfContent = $this->renderHtmlToPdf($view);
        Storage::disk('public')->put($path, $pdfContent);

        return ['path' => $path, 'file_name' => $fileName];
    }

    private function ensurePreviewData(Proposal $proposal): EthicsDocument
    {
        $ethicsDocument = $this->getLatestDraftDocument($proposal) ?? $proposal->ethicsDocument;

        if (!$ethicsDocument) {
            $ethicsDocument = EthicsDocument::create([
                'proposal_id' => $proposal->id,
                'document_number' => $proposal->nomor_ec ?? '',
                'status' => EthicsDocument::STATUS_DRAFT,
                'file_path' => '',
                'original_name' => $proposal->title ?? 'Ethical-Clearance',
                'notes' => '{}',
            ]);
        }

        $notes = json_decode($ethicsDocument->notes ?: '{}', true);
        if (!is_array($notes)) {
            $notes = ['notes' => (string) $ethicsDocument->notes];
        }

        $assignment = ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_KETUA)
            ->latest('created_at')
            ->first();

        $defaults = [
            'title' => $this->resolvePreviewValue([], 'title', $notes, $proposal, $proposal->title ?? ''),
            'principal_investigator' => $this->resolvePreviewValue([], 'principal_investigator', $notes, $proposal, $proposal->researcher?->name ?? $proposal->nama_peneliti ?? ''),
            'members' => $this->resolvePreviewValue([], 'members', $notes, $proposal),
            'institution' => $this->resolvePreviewValue([], 'institution', $notes, $proposal, optional($proposal->researcher)->institution ?? $proposal->asal_instansi ?? ''),
            'research_place' => $this->resolvePreviewValue([], 'research_place', $notes, $proposal),
            'nomor_ec' => $this->resolvePreviewValue([], 'nomor_ec', $notes, $proposal, $proposal->nomor_ec ?? $ethicsDocument->document_number ?? ''),
            'chair_name' => $assignment?->assignedTo?->name ?? $notes['chair_name'] ?? '',
        ];

        $updatedNotes = array_merge($notes, $defaults);
        $updatedPayload = json_encode($updatedNotes);
        $documentNumber = $this->resolveRevisionDocumentNumber($ethicsDocument, $proposal, $ethicsDocument->id);

        if ($ethicsDocument->notes !== $updatedPayload || $ethicsDocument->document_number !== $documentNumber) {
            $ethicsDocument->update([
                'notes' => $updatedPayload,
                'document_number' => $documentNumber,
            ]);
        }

        return $ethicsDocument;
    }

    /**
     * Show researcher confirmation page for admin-configured ethical clearance
     */
    public function showEthicalClearanceConfirmation(Proposal $proposal)
    {
        $access = $this->checkAccess();
        if ($access === 'guest') {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        if ($access === 'pending') {
            return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
        }

        if ($proposal->user_id !== Auth::id()) {
            abort(403);
        }

        if ($proposal->status !== Proposal::STATUS_WAITING_FOR_CONFIRMATION) {
            return redirect()->route('pengajuan.riwayat-pengajuan')->with('info', 'Proposal tidak membutuhkan konfirmasi dokumen saat ini.');
        }

        $assignment = ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_KETUA)
            ->latest('created_at')
            ->first();

        $ethicsDocument = $this->ensurePreviewData($proposal);
        $certificatePreviewData = $this->buildCertificatePreviewData($proposal, $ethicsDocument, $assignment);
        $proposalFiles = $proposal->files()->where('is_active', true)->get();

        return view('peneliti.pengajuan.ethical-clearance-confirm', compact('proposal', 'assignment', 'ethicsDocument', 'proposalFiles', 'certificatePreviewData'));
    }

    public function saveEthicalClearancePreviewData(Request $request, Proposal $proposal)
    {
        $access = $this->checkAccess();
        if ($access === 'guest') {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
        }
        if ($access === 'pending') {
            return response()->json(['success' => false, 'message' => 'Akun Anda belum diaktivasi.'], 403);
        }

        if ($proposal->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($proposal->status, [Proposal::STATUS_WAITING_FOR_CONFIRMATION, Proposal::STATUS_WITH_CHAIR], true)) {
            return response()->json(['success' => false, 'message' => 'Proposal tidak membutuhkan konfirmasi dokumen saat ini.'], 422);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'principal_investigator' => 'nullable|string|max:255',
            'members' => 'nullable|string',
            'institution' => 'nullable|string|max:255',
            'research_place' => 'nullable|string|max:255',
        ]);

        $sourceDocument = $this->getLatestDraftDocument($proposal) ?? $proposal->ethicsDocument;

        if (!$sourceDocument) {
            $sourceDocument = EthicsDocument::create([
                'proposal_id' => $proposal->id,
                'document_number' => $proposal->nomor_ec ?? '',
                'status' => EthicsDocument::STATUS_DRAFT,
                'file_path' => '',
                'original_name' => $proposal->title ?? 'Ethical-Clearance',
                'notes' => '{}',
            ]);
        }

        $notes = json_decode($sourceDocument->notes ?: '{}', true);
        if (!is_array($notes)) {
            $notes = ['notes' => (string) $sourceDocument->notes];
        }

        $updatedNotes = array_merge($notes, [
            'title' => $this->resolvePreviewValue($validated, 'title', $notes, $proposal, $proposal->title ?? ''),
            'principal_investigator' => $this->resolvePreviewValue($validated, 'principal_investigator', $notes, $proposal, $proposal->researcher?->name ?? $proposal->nama_peneliti ?? ''),
            'members' => $this->resolvePreviewValue($validated, 'members', $notes, $proposal),
            'institution' => $this->resolvePreviewValue($validated, 'institution', $notes, $proposal, optional($proposal->researcher)->institution ?? $proposal->asal_instansi ?? ''),
            'research_place' => $this->resolvePreviewValue($validated, 'research_place', $notes, $proposal),
        ]);

        $ethicsDocument = $sourceDocument;
        $newPayload = json_encode($updatedNotes);

        if ($sourceDocument->notes !== $newPayload) {
            $ethicsDocument = EthicsDocument::create([
                'proposal_id' => $proposal->id,
                'document_number' => $this->resolveRevisionDocumentNumber($sourceDocument, $proposal),
                'ketua_id' => $sourceDocument->ketua_id,
                'status' => EthicsDocument::STATUS_DRAFT,
                'file_path' => $sourceDocument->file_path ?? '',
                'original_name' => $sourceDocument->original_name ?? $proposal->title ?? 'Ethical-Clearance',
                'notes' => $newPayload,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $this->buildCertificatePreviewData($proposal, $ethicsDocument),
        ]);
    }

    public function confirmEthicalClearance(Request $request, Proposal $proposal)
    {
        $access = $this->checkAccess();
        if ($access === 'guest') {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        if ($access === 'pending') {
            return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
        }

        if ($proposal->user_id !== Auth::id()) {
            abort(403);
        }

        if ($proposal->status !== Proposal::STATUS_WAITING_FOR_CONFIRMATION) {
            return redirect()->route('pengajuan.riwayat-pengajuan')->with('info', 'Proposal tidak membutuhkan konfirmasi dokumen saat ini.');
        }

        $assignment = ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_KETUA)
            ->whereNull('sent_at')
            ->latest()
            ->firstOrFail();

        DB::transaction(function () use ($proposal, $assignment) {
            $proposal->update([
                'status' => Proposal::STATUS_READY_FOR_CHAIR,
                'ketua_id' => $assignment->assigned_to,
            ]);

            $ethicsDocument = $this->getLatestDraftDocument($proposal);
            $documentNumber = $proposal->nomor_ec ?: ($ethicsDocument?->document_number ?: 'EC-' . now()->format('Y-m-d'));
            $resolvedDocumentNumber = $this->resolveRevisionDocumentNumber($ethicsDocument, $proposal, $ethicsDocument?->id);

            if (!$ethicsDocument) {
                $ethicsDocument = EthicsDocument::create([
                    'proposal_id' => $proposal->id,
                    'status' => EthicsDocument::STATUS_DRAFT,
                    'document_number' => $resolvedDocumentNumber,
                    'ketua_id' => $assignment->assigned_to,
                    'file_path' => $proposal->ethicsDocument?->file_path ?? '',
                    'original_name' => $proposal->ethicsDocument?->original_name ?? '',
                    'notes' => $proposal->ethicsDocument?->notes ?? 'Dokumen ethical clearance dikonfirmasi oleh peneliti.',
                ]);
            } else {
                $ethicsDocument->update([
                    'document_number' => $resolvedDocumentNumber,
                    'ketua_id' => $assignment->assigned_to,
                    'status' => EthicsDocument::STATUS_DRAFT,
                ]);
            }

            $pdfData = $this->buildCertificatePdf($ethicsDocument, $proposal);
            $ethicsDocument->update([
                'file_path' => $pdfData['path'],
                'original_name' => $pdfData['file_name'],
            ]);

            $assignment->update(['sent_at' => now()]);

            $proposal->update([
                'status' => Proposal::STATUS_WITH_CHAIR,
            ]);

            \App\Models\DocumentLog::create([
                'proposal_id' => $proposal->id,
                'ethics_document_id' => $ethicsDocument->id,
                'user_id' => Auth::id(),
                'activity' => \App\Models\DocumentLog::ACTIVITY_VERIFY,
                'description' => 'Researcher confirmed ethical clearance documents and sent to ketua.',
                'metadata' => ['assigned_to' => $assignment->assigned_to],
            ]);

            \App\Models\Notification::create([
                'user_id' => $assignment->assigned_to,
                'title' => 'Ethical Clearance Siap Ditandatangani',
                'message' => 'Proposal "' . $proposal->title . '" dengan nomor EC ' . ($proposal->nomor_ec ?? '-') . ' siap untuk ditandatangani oleh Anda.',
                'type' => \App\Models\Notification::TYPE_DOCUMENT_READY,
                'status' => \App\Models\Notification::STATUS_UNREAD,
                'data' => json_encode([
                    'proposal_id' => $proposal->id,
                    'ethics_document_id' => $ethicsDocument->id,
                ]),
            ]);
        });

        return redirect()->route('pengajuan.riwayat-pengajuan')->with('success', 'Dokumen ethical clearance berhasil dikonfirmasi. Dokumen akan segera dikirim ke ketua untuk tanda tangan.');
    }

    /**
     * Final submit proposal
     */
    public function finalSubmit(Request $request)
    {
        $access = $this->checkAccess();

        if ($access !== null) {
            if ($access === 'guest') {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }
            if ($access === 'pending') {
                return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
            }
        }

        if (!$request->session()->has('proposal_step1') || !$request->session()->has('proposal_step2')) {
            return redirect()->route('pengajuan.upload-proposal')->with('info', 'Silakan lengkapi semua tahap pengajuan.');
        }

        $proposalStep1 = $request->session()->get('proposal_step1');
        $proposalStep2 = $request->session()->get('proposal_step2');

        $proposal = Proposal::create([
            'user_id' => Auth::id(),
            'nama_peneliti' => $proposalStep1['nama_peneliti'] ?? null,
            'asal_instansi' => $proposalStep1['asal_instansi'] ?? null,
            'title' => $proposalStep1['judul_penelitian'],
            'description' => "Jenis penelitian: {$proposalStep1['jenis_penelitian']}\nBidang ilmu: {$proposalStep1['bidang_ilmu']}\nLokasi penelitian: {$proposalStep1['lokasi_penelitian']}",
            'status' => Proposal::STATUS_ON_REVIEW,
            'submission_date' => now()->toDateString(),
        ]);

        foreach ($proposalStep2 as $fileData) {
            ProposalFile::create([
                'proposal_id' => $proposal->id,
                'file_path' => $fileData['path'],
                'file_type' => $fileData['file_type'],
                'original_name' => $fileData['original_name'],
                'file_size' => $fileData['size'],
                'mime_type' => $fileData['mime_type'],
                'version' => 1,
                'is_active' => true,
            ]);
        }

        $request->session()->forget(['proposal_step1', 'proposal_step2']);

        // Record activity log for submission
        \App\Models\DocumentLog::create([
            'proposal_id' => $proposal->id,
            'user_id'     => Auth::id(),
            'activity'    => \App\Models\DocumentLog::ACTIVITY_UPLOAD,
            'description' => 'Submission created by researcher',
            'metadata'    => [],
        ]);

        return redirect()->route('pengajuan.success')->with('success', 'Proposal Anda telah berhasil diajukan!');
    }

    /**
     * Success page
     */
    public function success()
    {
        $access = $this->checkAccess();

        if ($access !== null) {
            return redirect()->route('peneliti.dashboard');
        }

        return view('peneliti.pengajuan.success');
    }

    public function downloadFile(TemplateProposal $template)
    {
        $access = $this->checkAccess();

        if ($access !== null) {
            if ($access === 'guest') {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }
            if ($access === 'pending') {
                return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
            }
        }

        if (!Storage::disk('public')->exists($template->file_path)) {
            return back()->with('error', 'File template tidak ditemukan. Hubungi admin.');
        }

        $fullPath = Storage::disk('public')->path($template->file_path);
        return response()->download($fullPath, $template->file_name);
    }

    public function viewRevisionFile(Proposal $proposal, ProposalFile $file)
    {
        $access = $this->checkAccess();
        if ($access === 'guest') {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($access === 'pending') {
            return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
        }

        if ($proposal->user_id !== Auth::id() || $file->proposal_id !== $proposal->id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($file->file_path));
    }

    public function downloadRevisionFile(Proposal $proposal, ProposalFile $file)
    {
        $access = $this->checkAccess();
        if ($access === 'guest') {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($access === 'pending') {
            return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
        }

        if ($proposal->user_id !== Auth::id() || $file->proposal_id !== $proposal->id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404);
        }

        return response()->download(Storage::disk('public')->path($file->file_path), $file->original_name);
    }

    public function previewEthicalClearance(Proposal $proposal)
    {
        $access = $this->checkAccess();
        if ($access === 'guest') {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        if ($access === 'pending') {
            return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
        }

        if ($proposal->user_id !== Auth::id()) {
            abort(403);
        }

        $ethicsDocument = $this->ensurePreviewData($proposal);
        $assignment = ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_KETUA)
            ->latest('created_at')
            ->first();
        $certificatePreviewData = $this->buildCertificatePreviewData($proposal, $ethicsDocument, $assignment);

        return view('peneliti.pengajuan.ethical-clearance-preview', compact('proposal', 'certificatePreviewData'));
    }

    public function downloadEthicsDocument(Proposal $proposal)
    {
        $access = $this->checkAccess();
        if ($access === 'guest') {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($access === 'pending') {
            return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
        }

        if ($proposal->user_id !== Auth::id()) {
            abort(403);
        }

        $ethicsDocument = EthicsDocument::where('proposal_id', $proposal->id)
            ->where('status', \App\Models\EthicsDocument::STATUS_PUBLISHED)
            ->latest('created_at')
            ->first();

        if (!$ethicsDocument) {
            abort(404, 'Dokumen Ethical Clearance belum tersedia.');
        }

        // Generate styled PDF directly to ensure proper formatting
        $fileName = 'Ethical-Clearance-' . $proposal->nomor_ec . '-' . now()->format('YmdHis') . '.pdf';
        $pdfContent = $this->generateStyledEthicsDocumentPdf($ethicsDocument, $proposal);

        return response()->streamDownload(
            fn() => print($pdfContent),
            $fileName,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]
        );
    }
}
