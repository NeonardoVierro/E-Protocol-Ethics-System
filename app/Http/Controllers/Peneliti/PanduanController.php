<?php

namespace App\Http\Controllers\Peneliti;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PanduanController extends Controller
{
    /**
     * Menampilkan halaman Syarat Pendaftaran Ethical Clearance
     */
    public function syaratPendaftaran()
    {
        return view('peneliti.panduan.syarat-pendaftaran');
    }

    /**
     * Menampilkan halaman Alur Pengajuan Ethical Clearance
     */
    public function alurPengajuan()
    {
        return view('peneliti.panduan.alur-pengajuan');
    }

    /**
     * Menampilkan halaman Panduan Reviewer
     */
    public function panduanReviewer()
    {
        return view('peneliti.panduan.panduan-reviewer');
    }
}