<?php

namespace App\Http\Controllers\Peneliti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    /**
     * Constructor - pastikan user sudah login untuk semua method
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Halaman Upload Proposal
     */
    public function uploadProposal()
    {
        return view('peneliti.pengajuan.upload-proposal');
    }

    /**
     * Halaman Download Template
     */
    public function downloadTemplate()
    {
        return view('peneliti.pengajuan.download-template');
    }

    /**
     * Halaman Riwayat Pengajuan dengan tracking proposal
     */
    public function riwayatPengajuan()
    {
        return view('peneliti.pengajuan.riwayat-pengajuan');
    }
}