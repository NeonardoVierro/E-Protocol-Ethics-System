<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EthicsDocument;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EthicalClearanceController extends Controller
{
    public function index()
    {
        // Show draft ethics documents to admin
        // Filter: status DRAFT, proposal status APPROVED, and already sent from sekretaris (has DocumentLog activity sent_to_admin)
        $docs = EthicsDocument::with('proposal', 'ketua')
            ->where('status', EthicsDocument::STATUS_DRAFT)
            ->whereHas('proposal', function($q) {
                $q->where('status', Proposal::STATUS_APPROVED);
            })
            ->whereHas('documentLogs', function($q) {
                $q->where('activity', \App\Models\DocumentLog::ACTIVITY_SENT_TO_ADMIN);
            })
            ->orderByDesc('created_at')
            ->get();

        return view('admin.ethicalclearance.index', compact('docs'));
    }

    public function getKetuaList()
    {
        $list = User::whereHas('roles', function($q) {
                $q->where('name', 'ketua');
            })
            ->where('status', 'active')
            ->get(['id','name','email'])
            ->map(function($user) {
                $user->active_proposals_count = ProposalAssignment::where('assigned_to', $user->id)
                    ->where('role', ProposalAssignment::ROLE_KETUA)
                    ->whereNotNull('sent_at')
                    ->whereHas('proposal', fn($q) => $q->whereNotIn('status', [
                        Proposal::STATUS_PUBLISHED,
                        Proposal::STATUS_REJECTED,
                    ]))
                    ->count();
                return $user;
            })
            ->sortBy('active_proposals_count')
            ->values();

        return response()->json($list);
    }

    public function getAssignment(Request $request)
    {
        $proposalId = $request->query('proposal_id');
        
        if (!$proposalId) {
            return response()->json(['success' => false]);
        }

        $proposal = Proposal::find($proposalId);

        if (!$proposal) {
            return response()->json(['success' => false]);
        }

        $assignment = ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_KETUA)
            ->latest()
            ->first();

        if (!$assignment) {
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => true,
            'assignment' => [
                'ketua_id' => $assignment->assigned_to,
                'nomor_ec' => $proposal->nomor_ec,
            ]
        ]);
    }

    public function pilihKetua(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|exists:proposals,id',
            'ketua_id'   => 'required|exists:users,id',
            'nomor_ec'   => 'required|string|max:100|regex:/^EC-\d{4}-\d{2}-\d{4}$/|unique:proposals,nomor_ec,' . $request->proposal_id,
        ], [
            'nomor_ec.regex' => 'Format nomor EC harus: EC-YYYY-MM-XXXX (contoh: EC-2024-06-0001)',
            'nomor_ec.unique' => 'Nomor EC sudah digunakan untuk proposal lain.',
        ]);

        $proposal = Proposal::findOrFail($request->proposal_id);

        if ($proposal->status !== Proposal::STATUS_APPROVED) {
            return response()->json(['error' => 'Proposal belum disetujui.'], 422);
        }

        ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_KETUA)
            ->whereNull('sent_at')
            ->delete();

        $proposal->update(['nomor_ec' => $request->nomor_ec]);

        ProposalAssignment::create([
            'proposal_id' => $proposal->id,
            'assigned_by' => auth()->id(),
            'assigned_to' => $request->ketua_id,
            'role'        => ProposalAssignment::ROLE_KETUA,
            'sent_at'     => null,
        ]);

        return response()->json(['success' => true]);
    }

    public function kirimKetua(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|exists:proposals,id',
        ]);

        $proposal = Proposal::findOrFail($request->proposal_id);

        $assignment = ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_KETUA)
            ->whereNull('sent_at')
            ->latest()
            ->firstOrFail();

        DB::transaction(function () use ($assignment, $proposal) {
            $assignment->update(['sent_at' => now()]);

            $proposal->update([
                'status'   => Proposal::STATUS_WAITING_FOR_CONFIRMATION,
            ]);

            \App\Models\DocumentLog::create([
                'proposal_id' => $proposal->id,
                'user_id'     => auth()->id(),
                'activity'    => \App\Models\DocumentLog::ACTIVITY_ASSIGN,
                'description' => 'Submission processed and sent to researcher for final confirmation.',
                'metadata'    => ['assigned_to' => $assignment->assigned_to],
            ]);

            // Notify researcher to confirm the ethical clearance details
            if ($proposal->user_id) {
                \App\Models\Notification::create([
                    'user_id' => $proposal->user_id,
                    'title'   => 'Konfirmasi Dokumen Ethical Clearance',
                    'message' => 'Proposal "' . $proposal->title . '" telah dikonfigurasi oleh admin. Silakan konfirmasi dokumen sebelum dikirim ke ketua untuk tanda tangan.',
                    'type'    => \App\Models\Notification::TYPE_DOCUMENT_READY,
                    'status'  => \App\Models\Notification::STATUS_UNREAD,
                    'data'    => json_encode(['proposal_id' => $proposal->id, 'status' => Proposal::STATUS_WAITING_FOR_CONFIRMATION]),
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function generateNomorEc(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|exists:proposals,id',
        ]);

        $proposal = Proposal::findOrFail($request->proposal_id);

        // Generate format: EC-YYYY-MM-XXXX
        $year = now()->format('Y');
        $month = now()->format('m');

        // Get the last EC number for this month
        $lastProposal = Proposal::where('nomor_ec', 'like', "EC-{$year}-{$month}-%")
            ->orderBy('nomor_ec', 'desc')
            ->first();

        $sequence = 1;
        if ($lastProposal) {
            // Extract sequence from last number (e.g., EC-2024-06-0001 -> 0001)
            $lastSequence = (int) substr($lastProposal->nomor_ec, -4);
            $sequence = $lastSequence + 1;
        }

        $nomorEc = "EC-{$year}-{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'nomor_ec' => $nomorEc,
        ]);
    }
}