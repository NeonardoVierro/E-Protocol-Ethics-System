<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SecretaryDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:sekretaris|ketua']);
    }
    
    public function index()
    {
        $data = [
            'title' => 'Dashboard Sekretaris',
        ];
        
        return view('sekretaris.dashboard', $data);
    }
}