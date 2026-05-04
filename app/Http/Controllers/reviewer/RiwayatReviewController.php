<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RiwayatReviewController extends Controller
{
    /**
     * Menampilkan daftar proposal yang sudah direview oleh reviewer ini.
     */
    public function index()
    {
        // Data sementara (nanti ambil dari database)
        $riwayat = [
            [
                'id' => 'EC-098',
                'judul' => 'Etika Penggunaan AI dalam Rekrutmen',
                'tanggal_review' => '2025-10-15',
                'keputusan' => 'diterima',
                'feedback' => 'Proposal sudah sesuai dengan etika penelitian.',
            ],
            [
                'id' => 'EC-097',
                'judul' => 'Vaksinasi & Informed Consent Digital',
                'tanggal_review' => '2025-10-10',
                'keputusan' => 'revisi',
                'feedback' => 'Perlu perbaikan pada bagian informed consent.',
            ],
            [
                'id' => 'EC-095',
                'judul' => 'Penelitian Migrasi Data Pasien',
                'tanggal_review' => '2025-10-02',
                'keputusan' => 'ditolak',
                'feedback' => 'Melanggar privasi data pasien.',
            ],
        ];

        return view('reviewer.riwayat-review.index', compact('riwayat'));
    }

    /**
     * Menampilkan detail review tertentu.
     */
    public function show($id)
    {
        // Ambil data review berdasarkan ID
        // $review = Review::findOrFail($id);
        return view('reviewer.riwayat-review.show', compact('review'));
    }
}