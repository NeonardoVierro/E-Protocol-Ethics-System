<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EthicsDocument;

use Illuminate\Support\Facades\Auth;

class EthicalClearanceController extends Controller
{
    public function index()
    {
        // Show draft ethics documents to admin
        $docs = EthicsDocument::with('proposal', 'ketua')
            ->where('status', EthicsDocument::STATUS_DRAFT)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.ethicalclearance.index', compact('docs'));
    }
}