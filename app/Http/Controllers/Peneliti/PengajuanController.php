<?php

namespace App\Http\Controllers\Peneliti;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\ProposalFile;
use App\Models\ReviewFeedback;
use App\Models\TemplateProposal;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
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
        $proposals = Proposal::with(['reviewFeedbacks' => function ($query) {
                $query->where('is_submitted', true)
                      ->with(['review.reviewer']);
            }])
            ->where('user_id', Auth::id())
            ->orderByDesc('submission_date')
            ->orderByDesc('created_at')
            ->get();

        $proposals->each(function ($proposal) {
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
        
        // Check if there's a pending revision request (researcher can upload if status is 'requested')
        $canUploadRevision = $proposal->revisions()
            ->where('status', \App\Models\ProposalRevision::STATUS_REQUESTED)
            ->exists();

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

        return view('peneliti.pengajuan.revision-upload', compact('proposal', 'files', 'revisions', 'feedbacks', 'canUploadRevision', 'secretaryRevisionNotes'));
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
            return redirect()->back()->with('error', 'Tidak ada permintaan revisi yang pending. Tunggu sekretaris untuk meminta revisi lebih lanjut.');
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
                    ->where(function($q) use ($groupKey) {
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
}