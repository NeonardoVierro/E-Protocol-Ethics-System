<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
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
            'total_proposal' => Proposal::count(),
            'new_proposal' => Proposal::where('status', Proposal::STATUS_NEW)->count(),
            'on_review' => Proposal::where('status', Proposal::STATUS_ON_REVIEW)->count(),
            'approved' => Proposal::where('status', Proposal::STATUS_APPROVED)->count(),
            'rejected' => Proposal::where('status', Proposal::STATUS_REJECTED)->count(),
        ];
        
        return view('sekretaris.dashboard', $data);
    }
}