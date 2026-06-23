<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\EthicsDocument;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KetuaController extends Controller
{
    /**
     * Dashboard Ketua
     */
    public function dashboard()
    {
        // Get statistics for ketua dashboard
        $pendingDocuments = EthicsDocument::where('status', EthicsDocument::STATUS_DRAFT)
            ->where('ketua_id', Auth::id())
            ->count();

        $signedDocuments = EthicsDocument::where('status', EthicsDocument::STATUS_SIGNED)
            ->where('ketua_id', Auth::id())
            ->count();

        return view('ketua.dashboard', compact('pendingDocuments', 'signedDocuments'));
    }

    /**
     * Halaman Persetujuan & TTD untuk Ketua
     * Menampilkan dokumen yang perlu ditandatangani
     */
    public function persetujuanTtd()
    {
        // show documents that need persetujuan / tanda tangan
        // Filter: status DRAFT, has ketua_id (assigned to this ketua), and ketua_id matches current user
        $docs = EthicsDocument::with('proposal', 'ketua')
            ->where('status', EthicsDocument::STATUS_DRAFT)
            ->where('ketua_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('ketua.persetujuan-ttd.index', compact('docs'));
    }

    /**
     * Menandatangani dokumen
     */
    public function signDocument(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:ethics_documents,id',
        ]);

        $document = EthicsDocument::findOrFail($request->document_id);

        // Only the assigned ketua can sign
        if (Auth::id() !== $document->ketua_id) {
            return response()->json(['error' => 'Anda tidak memiliki izin untuk menandatangani dokumen ini.'], 403);
        }

        if ($document->status !== EthicsDocument::STATUS_DRAFT) {
            return response()->json(['error' => 'Dokumen tidak dalam status draft.'], 422);
        }

        DB::transaction(function () use ($document) {
            $document->update([
                'status'      => EthicsDocument::STATUS_SIGNED,
                'signed_date' => now(),
            ]);

            // Log activity
            \App\Models\DocumentLog::create([
                'ethics_document_id' => $document->id,
                'proposal_id'        => $document->proposal_id,
                'user_id'            => Auth::id(),
                'activity'           => \App\Models\DocumentLog::ACTIVITY_SIGN,
                'description'        => 'Dokumen ditandatangani oleh ketua.',
            ]);

            // Notify admin that document is signed
            $admin = User::whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })->first();

            if ($admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title'   => 'Dokumen Telah Ditandatangani',
                    'message' => 'Dokumen untuk proposal "' . ($document->proposal->title ?? '—') . '" telah ditandatangani oleh ketua. Siap untuk dipublish.',
                    'type'    => Notification::TYPE_DOCUMENT_READY,
                    'status'  => Notification::STATUS_UNREAD,
                    'data'    => json_encode(['ethics_document_id' => $document->id, 'proposal_id' => $document->proposal_id]),
                ]);
            }
        });

        return response()->json(['success' => true]);
    }
}
