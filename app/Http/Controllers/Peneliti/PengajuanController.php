<?php

namespace App\Http\Controllers\Peneliti;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalFile;
use App\Models\TemplateProposal;
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
        $proposals = Proposal::with(['reviewFeedbacks.review.reviewer'])
            ->where('user_id', Auth::id())
            ->orderByDesc('submission_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($proposal) {
                $proposal->submitted_review_feedbacks = $proposal->reviewFeedbacks
                    ->where('is_submitted', true)
                    ->map(function ($feedback) {
                        $parsed = null;
                        if (is_array($feedback->feedback_text)) {
                            $parsed = $feedback->feedback_text;
                        } elseif (is_string($feedback->feedback_text)) {
                            $decoded = json_decode($feedback->feedback_text, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $parsed = $decoded;
                            } else {
                                $parsed = ['general_comments' => $feedback->feedback_text];
                            }
                        } else {
                            $parsed = ['general_comments' => (string) $feedback->feedback_text];
                        }

                        return (object) [
                            'recommendation' => $feedback->recommendation,
                            'recommendation_label' => $feedback->recommendation_label,
                            'recommendation_badge' => $feedback->recommendation_badge,
                            'reviewer_name' => optional($feedback->review->reviewer)->name,
                            'submitted_at' => optional($feedback->submitted_at)->format('d M Y'),
                            'feedback' => $parsed,
                        ];
                    });

                return $proposal;
            });

        return view('peneliti.pengajuan.riwayat-pengajuan', compact('proposals'));
    }

    /**
     * Tampilkan form unggah revisi untuk proposal tertentu (peneliti pemilik)
     */
    public function uploadRevisi($proposalId)
    {
        $access = $this->checkAccess();

        if ($access === 'guest') {
            return view('peneliti.pengajuan.guest-message', [
                'title' => 'Unggah Revisi',
                'message' => 'Silakan login terlebih dahulu untuk mengunggah revisi.',
                'icon' => 'upload_file'
            ]);
        }

        if ($access === 'pending') {
            return view('peneliti.pengajuan.pending-message', [
                'title' => 'Unggah Revisi',
                'message' => 'Akun Anda belum diaktivasi. Tunggu aktivasi sekretariat.',
                'icon' => 'pending'
            ]);
        }

        $proposal = Proposal::with('revisions')->where('id', $proposalId)->where('user_id', Auth::id())->firstOrFail();

        return view('peneliti.pengajuan.upload-revisi', compact('proposal'));
    }

    /**
     * Proses unggah revisi
     */
    public function submitRevisi(Request $request, $proposalId)
    {
        $access = $this->checkAccess();

        if ($access !== null) {
            if ($access === 'guest') {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengajukan revisi.');
            }
            if ($access === 'pending') {
                return redirect()->route('peneliti.dashboard')->with('error', 'Akun Anda belum diaktivasi.');
            }
        }

        $proposal = Proposal::where('id', $proposalId)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'revision_file' => 'required|file|mimes:pdf|max:10240',
            'revision_note' => 'nullable|string|max:2000',
        ]);

        $file = $request->file('revision_file');
        $path = $file->store('proposals/revisions', 'public');

        $proposalFile = ProposalFile::create([
            'proposal_id' => $proposal->id,
            'file_path' => $path,
            'file_type' => ProposalFile::TYPE_REVISION,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
            'version' => ($proposal->files()->max('version') ?? 0) + 1,
            'is_active' => true,
        ]);

        $revisionNumber = $proposal->revisions()->count() + 1;

        $revision = \App\Models\ProposalRevision::create([
            'proposal_id' => $proposal->id,
            'revision_number' => $revisionNumber,
            'revision_note' => $request->input('revision_note'),
            'requested_date' => now(),
            'status' => \App\Models\ProposalRevision::STATUS_IN_PROGRESS,
        ]);

        // Submit the revision (this will attach file and update proposal status)
        $revision->submit($proposalFile->id);

        return redirect()->route('pengajuan.riwayat-pengajuan')->with('success', 'Revisi berhasil diunggah.');
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
            'status' => Proposal::STATUS_NEW,
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

        return Storage::disk('public')->download(
            $template->file_path,
            $template->file_name
        );
    }
}
