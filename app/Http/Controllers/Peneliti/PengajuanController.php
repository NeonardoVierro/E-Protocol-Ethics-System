<?php

namespace App\Http\Controllers\Peneliti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    /**
     * Halaman Upload Proposal
     * Bisa diakses guest tapi isinya menampilkan pesan login
     */
    public function uploadProposal()
    {
        return view('peneliti.pengajuan.upload-proposal');
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
        return view('peneliti.pengajuan.riwayat-pengajuan');
    }
}