<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'in_process',
            'on_review',
            'waiting_for_confirmation',
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
                'status'        => Proposal::STATUS_IN_PROCESS,
            ]);

            // Record activity log for assignment to sekretaris
            \App\Models\DocumentLog::create([
                'proposal_id' => $proposal->id,
                'user_id'     => auth()->id(),
                'activity'    => \App\Models\DocumentLog::ACTIVITY_ASSIGN,
                'description' => 'Submission processed and assigned to sekretaris.',
                'metadata'    => ['assigned_to' => $assignment->assigned_to],
            ]);

            // Notifikasi ke sekretaris
            \App\Models\Notification::create([
                'user_id' => $assignment->assigned_to,
                'title'   => 'Proposal Baru Ditugaskan',
                'message' => 'Proposal "' . $proposal->title . '" telah dikirim kepada Anda untuk diproses.',
                'type'    => \App\Models\Notification::TYPE_DOCUMENT_READY,
                'status'  => \App\Models\Notification::STATUS_UNREAD,
                'data'    => json_encode([
                    'proposal_id' => $proposal->id,
                    'role'        => 'sekretaris',
                ]),
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

        // Update nomor_ec di proposal dan sinkronisasi ke ethics_documents
        $proposal->update(['nomor_ec' => $request->nomor_ec]);

        // Sinkronisasi penomoran ke ethics_documents
        $ethicsDocument = \App\Models\EthicsDocument::where('proposal_id', $proposal->id)->first();
        if ($ethicsDocument) {
            $ethicsDocument->update(['document_number' => $request->nomor_ec]);
        }

        ProposalAssignment::create([
            'proposal_id' => $proposal->id,
            'assigned_by' => auth()->id(),
            'assigned_to' => $request->ketua_id,
            'role'        => ProposalAssignment::ROLE_KETUA,
            'sent_at'     => null,
        ]);

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

    // Render preview for a proposal's ethical clearance (used in admin iframe).
    public function previewProposal(Proposal $proposal)
    {
        $ethicsDocument = \App\Models\EthicsDocument::where('proposal_id', $proposal->id)->first();

        // If a generated file exists, return it directly
        if ($ethicsDocument && $ethicsDocument->file_path && Storage::disk('public')->exists($ethicsDocument->file_path)) {
            return response()->file(Storage::disk('public')->path($ethicsDocument->file_path));
        }

        // Fallback: render an HTML preview populated from the draft notes or proposal
        $notes = [];
        if ($ethicsDocument) {
            try {
                $decoded = json_decode($ethicsDocument->notes ?: '{}', true);
                if (is_array($decoded)) {
                    $notes = $decoded;
                } else {
                    $notes = ['notes' => (string) $ethicsDocument->notes];
                }
            } catch (\Throwable $e) {
                $notes = ['notes' => (string) $ethicsDocument->notes];
            }
        }

        $data = [
            'document_number' => $ethicsDocument->document_number ?? $proposal->nomor_ec ?? '',
            'title' => $proposal->title ?? ($notes['title'] ?? ($ethicsDocument->original_name ?? '')),
            'principal_investigator' => $notes['principal_investigator'] ?? $proposal->researcher?->name ?? $proposal->nama_peneliti ?? '-',
            'members' => $notes['members'] ?? '-',
            'institution' => $notes['institution'] ?? $proposal->institution ?? '-',
            'research_place' => $notes['research_place'] ?? '-',
            'previewDate' => now()->format('d F Y'),
        ];

        return view('admin.ethicalclearance.preview', $data);
    }
}