<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProposalAssignmentController extends Controller
{
    // ── INDEX: daftar semua proposal ─────────────
    public function index()
    {
        $proposals = Proposal::with([
            'researcher',
            'assignments' => fn($q) => $q->with('assignedTo'),
        ])
        ->orderByRaw("FIELD(status,
            'new_proposal',
            'on_review',
            'waiting_for_publish',
            'approved',
            'published',
            'revised',
            'rejected'
        )")
        ->orderByDesc('created_at')
        ->paginate(15);

        return view('admin.proposalassignment.index', compact('proposals'));
    }

    // ── GET daftar sekretaris (untuk modal) ───────
    public function getSekretarisList()
    {
        $list = User::whereHas('roles', function($q) {
                $q->where('name', 'sekretaris');
            })
            ->where('status', 'active')
            ->get(['id','name','email'])
            ->map(function($user) {
                // Hitung dari sisi ProposalAssignment, bukan dari User
                $user->active_proposals_count = ProposalAssignment::where('assigned_to', $user->id)
                    ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                    ->whereNotNull('sent_at')
                    ->whereHas('proposal', fn($q) => $q->whereNotIn('status', [
                        Proposal::STATUS_APPROVED,
                        Proposal::STATUS_REJECTED,
                        Proposal::STATUS_PUBLISHED,
                    ]))
                    ->count();
                return $user;
            })
            ->sortBy('active_proposals_count')
            ->values();

        return response()->json($list);
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

    // ── PILIH SEKRETARIS (simpan assignment, belum dikirim) ─────
    public function pilihSekretaris(Request $request, Proposal $proposal)
    {
        $request->validate([
            'sekretaris_id' => 'required|exists:users,id',
        ]);

        // Hapus assignment sekretaris yang belum dikirim untuk proposal ini
        ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->whereNull('sent_at')
            ->delete();

        // Buat assignment baru (belum dikirim)
        ProposalAssignment::create([
            'proposal_id' => $proposal->id,
            'assigned_by' => auth()->id(),
            'assigned_to' => $request->input('sekretaris_id'),
            'role'        => ProposalAssignment::ROLE_SEKRETARIS,
            'sent_at'     => null,
        ]);

        return response()->json(['success' => true]);
    }

// ── KIRIM KE SEKRETARIS ───────────────────────
    public function kirimSekretaris(Proposal $proposal)
    {
        $assignment = ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->whereNull('sent_at')
            ->latest()
            ->firstOrFail();

        DB::transaction(function () use ($assignment, $proposal) {
            $assignment->update(['sent_at' => now()]);

            $proposal->update([
                'sekretaris_id' => $assignment->assigned_to,
                'status'        => Proposal::STATUS_ON_REVIEW,
            ]);
        });

        return response()->json(['success' => true]);
    }

    // ── PILIH KETUA (simpan, belum kirim) ─────────
    public function pilihKetua(Request $request, Proposal $proposal)
    {
        $request->validate([
            'ketua_id'  => 'required|exists:users,id',
            'nomor_ec'  => 'required|string|max:100',
        ]);

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

    // ── KIRIM KE KETUA ────────────────────────────
    public function kirimKetua(Proposal $proposal)
    {
        $assignment = ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_KETUA)
            ->whereNull('sent_at')
            ->latest()
            ->firstOrFail();

        DB::transaction(function () use ($assignment, $proposal) {
            $assignment->update(['sent_at' => now()]);

            $proposal->update([
                'ketua_id' => $assignment->assigned_to,
                'status'   => Proposal::STATUS_WAITING_FOR_PUBLISH,
            ]);
        });

        return response()->json(['success' => true]);
    }

    // ── PUBLISH ───────────────────────────────────
    public function publish(Proposal $proposal)
    {
        if ($proposal->status !== Proposal::STATUS_WAITING_FOR_PUBLISH) {
            return response()->json(['error' => 'Proposal belum siap dipublish.'], 422);
        }

        $proposal->update(['status' => Proposal::STATUS_PUBLISHED]);

        return response()->json(['success' => true]);
    }
}