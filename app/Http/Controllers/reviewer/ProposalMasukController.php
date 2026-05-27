<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalMasukController extends Controller
{
    /**
     * Menampilkan daftar proposal yang perlu direview oleh reviewer.
     */
    public function index()
    {
        // Fetch proposals assigned to current reviewer via ProposalAssignment
        $proposals = Proposal::whereHas('assignments', function ($query) {
            $query->where('role', ProposalAssignment::ROLE_REVIEWER)
                  ->where('assigned_to', Auth::id())
                  ->whereNotNull('sent_at');
        })
            ->with(['researcher', 'files'])
            ->orderByDesc('submission_date')
            ->get();

        return view('reviewer.proposal-masuk.index', compact('proposals'));
    }
}