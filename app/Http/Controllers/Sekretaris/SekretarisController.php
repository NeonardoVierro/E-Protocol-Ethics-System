<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Models\Proposal;

class SekretarisController extends Controller
{
    public function dashboard()
    {
        $data = [
            'total_proposal' => 24,
            'pending' => 7,
            'approved' => 12,
            'rejected' => 5,
        ];
        return view('sekretaris.dashboard', $data);
    }

    public function manajemenProposal()
    {
        $proposals = Proposal::with(['files' => function($query) {
            $query->where('is_active', true);
        }])
            ->orderByDesc('submission_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(function($proposal) {
                $activeFilesCount = $proposal->files->count();
                $proposal->files_count = $activeFilesCount;
                $proposal->has_documents = $activeFilesCount > 0;
                
                return $proposal;
            });
        
        return view('sekretaris.manajemen-proposal.index', compact('proposals'));
    }

    public function assignReviewer()
    {
        $proposals = [
            ['id' => 'P001', 'judul' => 'Studi Etika AI', 'reviewer_terpilih' => null],
            ['id' => 'P002', 'judul' => 'Penelitian Klinis', 'reviewer_terpilih' => null],
        ];
        $reviewers = ['Dr. Andi', 'Prof. Siti', 'Dr. Budi', 'Prof. Joko'];
        return view('sekretaris.assign-reviewer.index', compact('proposals', 'reviewers'));
    }

    public function hasilReview()
    {
        $reviews = [
            ['proposal_id' => 'P001', 'judul' => 'Studi Etika AI', 'reviewer' => 'Dr. Andi', 'feedback' => 'Revisi minor, perbaiki metodologi', 'tanggal' => '2025-05-03'],
            ['proposal_id' => 'P002', 'judul' => 'Penelitian Klinis', 'reviewer' => 'Prof. Siti', 'feedback' => 'Diterima dengan catatan', 'tanggal' => '2025-05-04'],
        ];
        return view('sekretaris.hasil-review.index', compact('reviews'));
    }

    public function keputusan()
    {
        $keputusan = [
            ['proposal_id' => 'P001', 'judul' => 'Studi Etika AI', 'status' => 'revisi', 'tenggat' => '2025-05-20'],
            ['proposal_id' => 'P002', 'judul' => 'Penelitian Klinis', 'status' => 'approved', 'tenggat' => null],
            ['proposal_id' => 'P003', 'judul' => 'Etika Data Pasien', 'status' => 'pending', 'tenggat' => null],
        ];
        return view('sekretaris.keputusan.index', compact('keputusan'));
    }

    public function draftEthicalClearance()
    {
        $drafts = [
            ['id' => 'EC001', 'proposal_id' => 'P002', 'judul' => 'Penelitian Klinis', 'status' => 'draft', 'tanggal' => '2025-05-10'],
        ];
        return view('sekretaris.draf-ethical-clearance.index', compact('drafts'));
    }

    public function arsipDokumen()
    {
        $arsip = [
            ['nama' => 'Proposal P001 - Studi Etika AI', 'tipe' => 'PDF', 'tanggal' => '2025-04-01', 'ukuran' => '2.3 MB'],
            ['nama' => 'Ethical Clearance EC001', 'tipe' => 'PDF', 'tanggal' => '2025-05-12', 'ukuran' => '1.1 MB'],
            ['nama' => 'Surat Tugas Reviewer P002', 'tipe' => 'DOCX', 'tanggal' => '2025-05-02', 'ukuran' => '0.5 MB'],
        ];
        return view('sekretaris.arsip-dokumen.index', compact('arsip'));
    }

    public function arsip()
    {
        $arsip = [
            ['nama' => 'Proposal P001 - Studi Etika AI', 'tipe' => 'PDF', 'tanggal' => '2025-04-01', 'ukuran' => '2.3 MB'],
            ['nama' => 'Ethical Clearance EC001', 'tipe' => 'PDF', 'tanggal' => '2025-05-12', 'ukuran' => '1.1 MB'],
            ['nama' => 'Surat Tugas Reviewer P002', 'tipe' => 'DOCX', 'tanggal' => '2025-05-02', 'ukuran' => '0.5 MB'],
        ];
        return view('sekretaris.arsip-dokumen.index', compact('arsip'));
    }

    public function userManagement()
    {
        $pendingUsers = User::where('status', 'pending')->get();
        return view('sekretaris.user-management.index', compact('pendingUsers'));
    }

    public function activateUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);
        
        // Buat notifikasi untuk user
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Akun Anda Telah Diaktivasi',
            'message' => 'Selamat! Akun Anda telah diaktivasi oleh sekretariat. Anda sekarang dapat login dan mengajukan ethical clearance.',
            'status' => 'unread',
            'type' => Notification::TYPE_ACCOUNT_ACTIVATION,
            'data' => json_encode(['activated_at' => now()->toDateTimeString()]),
        ]);
        
        return redirect()->route('sekretaris.user-management')->with('success', 'Akun berhasil diaktifkan dan notifikasi telah dikirim ke peneliti.');
    }
}