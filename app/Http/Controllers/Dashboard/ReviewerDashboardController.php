<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:reviewer']);
    }
    
    public function index()
    {
        $data = [
            'title' => 'Dashboard Reviewer',
        ];
        
        return view('reviewer.dashboard', $data);
    }
}