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
     * Halaman Upload Proposal
     * Bisa diakses guest tapi isinya menampilkan pesan login
     */
    public function uploadProposal()
    {
        $templates = TemplateProposal::where('is_active', true)
            ->orderBy('kategori')
            ->orderBy('nama_dokumen')
            ->get();

        $proposalData = session('proposal_step1', []);
        
        return view('peneliti.pengajuan.upload-proposal', compact('templates', 'proposalData'));
    }

    /**
     * Halaman Download Template
     * Bisa diakses guest tapi isinya menampilkan pesan login
     */
    public function downloadTemplate()
    {
        return view('peneliti.pengajuan.download-template');
    }

    /**
     * Halaman Riwayat Pengajuan dengan tracking proposal
     * Bisa diakses guest tapi isinya menampilkan pesan login
     */
    public function riwayatPengajuan()
    {
        $proposals = collect();

        if (Auth::check() && Auth::user()->hasRole('peneliti') && Auth::user()->status === 'active') {
            $proposals = Proposal::where('user_id', Auth::id())
                ->orderByDesc('submission_date')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('peneliti.pengajuan.riwayat-pengajuan', compact('proposals'));
    }

    /**
     * Store informasi dasar proposal
     */
    public function store(Request $request)
    {
        // Only authenticated peneliti with active status can submit
        if (!Auth::check() || !Auth::user()->hasRole('peneliti') || Auth::user()->status !== 'active') {
            return redirect()->route('login')->with('error', 'Anda harus login sebagai peneliti aktif untuk mengajukan proposal.');
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

        // Store in session for multi-step form
        $request->session()->put('proposal_step1', $validated);

        // Redirect to file upload step
        return redirect()->route('pengajuan.upload-berkas');
    }

    /**
     * Halaman upload berkas
     */
    public function uploadBerkas()
    {
        // Verify step 1 is complete
        if (!session()->has('proposal_step1')) {
            return redirect()->route('pengajuan.upload-proposal')->with('info', 'Silakan lengkapi informasi dasar terlebih dahulu.');
        }

        // Get all active templates
        $templates = TemplateProposal::where('is_active', true)
            ->orderBy('kategori')
            ->orderBy('nama_dokumen')
            ->get();

        // Get already uploaded files from session (if returning from review)
        $uploadedFiles = session('proposal_step2', []);

        return view('peneliti.pengajuan.upload-berkas', compact('templates', 'uploadedFiles'));
    }

    /**
     * Submit dokumen dan lanjut ke review
     */
    public function submitBerkas(Request $request)
    {
        // Only authenticated peneliti with active status can submit
        if (!Auth::check() || !Auth::user()->hasRole('peneliti') || Auth::user()->status !== 'active') {
            return redirect()->route('login')->with('error', 'Anda harus login sebagai peneliti aktif untuk mengajukan proposal.');
        }

        // Get all active templates
        $templates = TemplateProposal::where('is_active', true)->get();

        // Existing uploaded file metadata in session
        $sessionFiles = $request->session()->get('proposal_step2', []);

        // Build dynamic validation rules based on templates and session state
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

        // Validate files with custom messages
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

        // Store uploaded file metadata in session for review step
        $request->session()->put('proposal_step2', $storedFiles);

        // Redirect to review step
        return redirect()->route('pengajuan.review');
    }

    /**
     * Halaman review dan submit
     */
    public function review()
    {
        // Verify both steps are complete
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
        // Only authenticated peneliti with active status can submit
        if (!Auth::check() || !Auth::user()->hasRole('peneliti') || Auth::user()->status !== 'active') {
            return redirect()->route('login')->with('error', 'Anda harus login sebagai peneliti aktif untuk mengajukan proposal.');
        }

        // Verify both steps are complete
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

        return redirect()->route('pengajuan.success')->with('success', 'Proposal Anda telah berhasil diajukan!');
    }

    /**
     * Success page
     */
    public function success()
    {
        return view('peneliti.pengajuan.success');
    }
}