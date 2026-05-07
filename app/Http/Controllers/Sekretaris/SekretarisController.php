<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;

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
        $proposals = [
            ['id' => 'P001', 'judul' => 'Studi Etika AI', 'status' => 'lengkap', 'tanggal' => '2025-05-01'],
            ['id' => 'P002', 'judul' => 'Penelitian Klinis', 'status' => 'kurang', 'tanggal' => '2025-05-02'],
            ['id' => 'P003', 'judul' => 'Etika Data Pasien', 'status' => 'lengkap', 'tanggal' => '2025-05-03'],
        ];
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
}