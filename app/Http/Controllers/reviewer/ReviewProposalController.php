<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewProposalController extends Controller
{
    /**
     * Menampilkan halaman review untuk proposal tertentu.
     * Jika tidak ada parameter, tampilkan form kosong atau pilih proposal terlebih dahulu.
     */
    public function index(Request $request)
    {
        // Ambil ID proposal dari query string (?id=EC-101)
        $proposalId = $request->query('id');
        
        // Data sementara (nanti ambil dari database)
        $proposal = null;
        if ($proposalId) {
            // Simulasi data proposal
            $proposal = [
                'id' => $proposalId,
                'judul' => 'Analisis Dampak Etis AI di Sektor Kesehatan',
                'pemohon' => 'Dr. Rahmat Wijaya',
                'tanggal_masuk' => '2025-10-23',
                'files' => [
                    ['nama' => 'Proposal_EC101.pdf', 'type' => 'pdf'],
                    ['nama' => 'surat_pernyataan.pdf', 'type' => 'pdf'],
                    ['nama' => 'informed_consent.docx', 'type' => 'docx'],
                ]
            ];
        }

        return view('reviewer.review-proposal.index', compact('proposal'));
    }

    /**
     * Menyimpan hasil review (submit review).
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'proposal_id' => 'required|string',
            'feedback' => 'required|string|min:10',
            'status' => 'nullable|in:diterima,revisi,ditolak',
        ]);

        // Simpan ke database (contoh)
        // Review::create([...]);

        // Redirect ke halaman riwayat review dengan pesan sukses
        return redirect()->route('reviewer.riwayat-review')
                         ->with('success', 'Review berhasil disubmit.');
    }
}