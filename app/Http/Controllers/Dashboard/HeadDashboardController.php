<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HeadDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:ketua']);
    }
    
    public function index()
    {
        $data = [
            'title' => 'Dashboard Ketua',
        ];
        
        return view('ketua.dashboard', $data);
    }
}