<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResearcherDashboardController extends Controller
{
    public function __construct()
    {
        // Tidak ada middleware auth di sini karena dashboard peneliti bisa diakses semua role
    }
    
    public function index()
    {
        $data = [
            'title' => 'Dashboard Peneliti',
        ];
        
        return view('peneliti.dashboard', $data);
    }
}