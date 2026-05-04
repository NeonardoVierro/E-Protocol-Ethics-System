<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProposalMasukController extends Controller
{
    /**
     * Menampilkan daftar proposal yang perlu direview oleh reviewer.
     */
    public function index()
    {
        // Data sementara (nanti ambil dari database dengan status 'assigned' atau 'pending review')
        $proposals = [
            [
                'id' => 'EC-101',
                'judul' => 'Analisis Dampak Etis AI di Sektor Kesehatan',
                'pemohon' => 'Dr. Rahmat Wijaya',
                'tanggal_masuk' => '2025-10-23',
            ],
            [
                'id' => 'EC-102',
                'judul' => 'Penggunaan Data Genetik dalam Riset Medis',
                'pemohon' => 'Prof. Siti Nurhaliza',
                'tanggal_masuk' => '2025-10-24',
            ],
            [
                'id' => 'EC-105',
                'judul' => 'Etika Pengawasan Biometrik di Publik',
                'pemohon' => 'Maya Indah, M.Sc',
                'tanggal_masuk' => '2025-10-25',
            ],
        ];

        return view('reviewer.proposal-masuk.index', compact('proposals'));
    }
}