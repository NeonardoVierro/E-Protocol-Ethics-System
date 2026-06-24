<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EthicsDocument;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\User;
use App\Models\DocumentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EthicalClearanceController extends Controller
{
    public function index()
    {
        $docs = EthicsDocument::with('proposal', 'ketua')
            ->where('status', EthicsDocument::STATUS_DRAFT)
            ->whereHas('proposal', function ($q) {
                $q->where('status', Proposal::STATUS_APPROVED);
            })
            ->whereHas('documentLogs', function ($q) {
                $q->where('activity', DocumentLog::ACTIVITY_SENT_TO_ADMIN);
            })
            ->orderByDesc('created_at')
            ->get();

        return view('admin.ethicalclearance.index', compact('docs'));
    }

    public function getKetuaList()
    {
        $list = User::whereHas('roles', function ($q) {
                $q->where('name', 'ketua');
            })
            ->where('status', 'active')
            ->get(['id', 'name', 'email'])
            ->map(function ($user) {
                $user->active_proposals_count = ProposalAssignment::where('assigned_to', $user->id)
                    ->where('role', ProposalAssignment::ROLE_KETUA)
                    ->whereNotNull('sent_at')
                    ->whereHas('proposal', fn ($q) => $q->whereNotIn('status', [
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
            ],
        ]);
    }

    public function pilihKetua(Request $request)
    {
        $validated = $request->validate([
            'proposal_id' => 'required|exists:proposals,id',
            'ketua_id'    => 'required|exists:users,id',
            'nomor_ec'    => [
                'required',
                'string',
                'max:100',
                'regex:/^EC-\d{4}-\d{2}-\d{4}$/',
                Rule::unique('proposals', 'nomor_ec')->ignore($request->proposal_id),
            ],
        ], [
            'proposal_id.required' => 'Proposal belum dipilih.',
            'proposal_id.exists' => 'Proposal tidak valid.',
            'ketua_id.required' => 'Ketua belum dipilih.',
            'ketua_id.exists' => 'Ketua tidak valid.',
            'nomor_ec.regex' => 'Format nomor EC harus: EC-YYYY-MM-XXXX (contoh: EC-2026-06-0001)',
            'nomor_ec.unique' => 'Nomor EC sudah digunakan untuk proposal lain.',
        ]);

        $proposal = Proposal::find($request->proposal_id);

        if (! $proposal) {
            return response()->json(['success' => false, 'error' => 'Proposal tidak ditemukan.'], 404);
        }

        if ($proposal->status !== Proposal::STATUS_APPROVED) {
            return response()->json(['success' => false, 'error' => 'Proposal belum disetujui.'], 422);
        }

        ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_KETUA)
            ->whereNull('sent_at')
            ->delete();

        $proposal->update([
            'nomor_ec' => $validated['nomor_ec'],
            'status'   => Proposal::STATUS_WAITING_FOR_CONFIRMATION,
        ]);

        ProposalAssignment::create([
            'proposal_id' => $proposal->id,
            'assigned_by' => auth()->id(),
            'assigned_to' => $validated['ketua_id'],
            'role'        => ProposalAssignment::ROLE_KETUA,
            'sent_at'     => null,
        ]);

        DocumentLog::create([
            'proposal_id' => $proposal->id,
            'user_id'     => auth()->id(),
            'activity'    => DocumentLog::ACTIVITY_ASSIGN,
            'description' => 'Ketua dipilih dan proposal menunggu validasi peneliti.',
            'metadata'    => [
                'ketua_id' => $validated['ketua_id'],
                'nomor_ec'  => $validated['nomor_ec'],
            ],
        ]);

        if ($proposal->user_id) {
            \App\Models\Notification::create([
                'user_id' => $proposal->user_id,
                'title'   => 'Validasi Ethical Clearance',
                'message' => 'Anda perlu memvalidasi draft ethical clearance untuk proposal "' . $proposal->title . '" sebelum dikirim ke ketua.',
                'type'    => \App\Models\Notification::TYPE_DOCUMENT_READY,
                'status'  => \App\Models\Notification::STATUS_UNREAD,
                'data'    => json_encode([
                    'proposal_id' => $proposal->id,
                    'status'      => Proposal::STATUS_WAITING_FOR_CONFIRMATION,
                ]),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Assignment berhasil disimpan.']);
    }

    public function confirmEthicalClearance(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|exists:proposals,id',
        ]);

        $proposal = Proposal::findOrFail($request->proposal_id);

        if ($proposal->status !== Proposal::STATUS_WAITING_FOR_CONFIRMATION) {
            return response()->json(['error' => 'Proposal belum dalam tahap konfirmasi.'], 422);
        }

        $proposal->update([
            'status' => Proposal::STATUS_READY_FOR_CHAIR,
        ]);

        $assignment = ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_KETUA)
            ->whereNull('sent_at')
            ->latest()
            ->firstOrFail();

        DB::transaction(function () use ($assignment, $proposal) {
            $assignment->update(['sent_at' => now()]);

            $proposal->update([
                'status' => Proposal::STATUS_WITH_CHAIR,
            ]);

            DocumentLog::create([
                'proposal_id' => $proposal->id,
                'user_id'     => auth()->id(),
                'activity'    => DocumentLog::ACTIVITY_SIGN,
                'description' => 'Dokumen dikirim ke ketua untuk tanda tangan.',
                'metadata'    => [
                    'assigned_to' => $assignment->assigned_to,
                ],
            ]);

            if ($proposal->user_id) {
                \App\Models\Notification::create([
                    'user_id' => $proposal->user_id,
                    'title'   => 'Dokumen Dikirim ke Ketua',
                    'message' => 'Proposal "' . $proposal->title . '" sudah divalidasi dan dikirim ke ketua untuk tanda tangan.',
                    'type'    => \App\Models\Notification::TYPE_DOCUMENT_READY,
                    'status'  => \App\Models\Notification::STATUS_UNREAD,
                    'data'    => json_encode([
                        'proposal_id' => $proposal->id,
                        'status'      => Proposal::STATUS_WITH_CHAIR,
                    ]),
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
        $year = now()->format('Y');
        $month = now()->format('m');

        $lastNomor = Proposal::where('nomor_ec', 'like', "EC-{$year}-{$month}-%")
            ->orderByDesc('nomor_ec')
            ->value('nomor_ec');

        $nextSequence = 1;

        if ($lastNomor) {
            $parts = explode('-', $lastNomor);
            $lastSeq = end($parts);
            if (is_numeric($lastSeq)) {
                $nextSequence = ((int) $lastSeq) + 1;
            }
        }

        $nomorEc = 'EC-' . $year . '-' . $month . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'nomor_ec' => $nomorEc,
        ]);
    }
}