<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PublishingController extends Controller
{
    public function index()
    {
        // Show ethics documents that are signed and ready to publish
        $docs = \App\Models\EthicsDocument::with(['proposal', 'ketua'])
            ->where('status', \App\Models\EthicsDocument::STATUS_SIGNED)
            ->orderByDesc('signed_date')
            ->paginate(15);

        $publishedDocs = \App\Models\EthicsDocument::with(['proposal', 'ketua'])
            ->where('status', \App\Models\EthicsDocument::STATUS_PUBLISHED)
            ->orderByDesc('published_date')
            ->paginate(15, ['*'], 'published_docs_page');

        return view('admin.publishing.index', compact('docs', 'publishedDocs'));
    }

    public function publish(\App\Models\EthicsDocument $document)
    {
        if ($document->status !== \App\Models\EthicsDocument::STATUS_SIGNED) {
            return response()->json(['error' => 'Dokumen belum ditandatangani.'], 422);
        }

        DB::transaction(function () use ($document) {
            $document->update([
                'status'        => \App\Models\EthicsDocument::STATUS_PUBLISHED,
                'published_date' => now(),
            ]);

            // Update proposal status
            if ($document->proposal) {
                $document->proposal->update(['status' => Proposal::STATUS_PUBLISHED]);
            }

            // Log activity
            \App\Models\DocumentLog::create([
                'ethics_document_id' => $document->id,
                'proposal_id'        => $document->proposal_id,
                'user_id'            => auth()->id(),
                'activity'           => \App\Models\DocumentLog::ACTIVITY_PUBLISH,
                'description'        => 'Dokumen dipublikasi.',
            ]);

            // Notify peneliti that document is published
            if ($document->proposal && $document->proposal->user_id) {
                \App\Models\Notification::create([
                    'user_id' => $document->proposal->user_id,
                    'title'   => 'Ethical Clearance Telah Dipublikasi',
                    'message' => 'Dokumen Ethical Clearance untuk proposal "' . ($document->proposal->title ?? '—') . '" telah dipublikasi. Anda dapat mengunduh dokumen tersebut.',
                    'type'    => \App\Models\Notification::TYPE_DOCUMENT_READY,
                    'status'  => \App\Models\Notification::STATUS_UNREAD,
                    'data'    => json_encode(['ethics_document_id' => $document->id, 'proposal_id' => $document->proposal_id]),
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function bulkPublish(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:ethics_documents,id',
        ]);

        $updated = 0;
        foreach ($request->document_ids as $docId) {
            $document = \App\Models\EthicsDocument::find($docId);
            if ($document && $document->status === \App\Models\EthicsDocument::STATUS_SIGNED) {
                DB::transaction(function () use ($document) {
                    $document->update([
                        'status'         => \App\Models\EthicsDocument::STATUS_PUBLISHED,
                        'published_date' => now(),
                    ]);

                    if ($document->proposal) {
                        $document->proposal->update(['status' => Proposal::STATUS_PUBLISHED]);
                    }

                    \App\Models\DocumentLog::create([
                        'ethics_document_id' => $document->id,
                        'proposal_id'        => $document->proposal_id,
                        'user_id'            => auth()->id(),
                        'activity'           => \App\Models\DocumentLog::ACTIVITY_PUBLISH,
                        'description'        => 'Dokumen dipublikasi.',
                    ]);

                    if ($document->proposal && $document->proposal->user_id) {
                        \App\Models\Notification::create([
                            'user_id' => $document->proposal->user_id,
                            'title'   => 'Ethical Clearance Telah Dipublikasi',
                            'message' => 'Dokumen Ethical Clearance untuk proposal "' . ($document->proposal->title ?? '—') . '" telah dipublikasi.',
                            'type'    => \App\Models\Notification::TYPE_DOCUMENT_READY,
                            'status'  => \App\Models\Notification::STATUS_UNREAD,
                            'data'    => json_encode(['ethics_document_id' => $document->id, 'proposal_id' => $document->proposal_id]),
                        ]);
                    }
                });
                $updated++;
            }
        }

        return response()->json(['success' => true, 'updated' => $updated]);
    }

    /**
     * Preview the ethics document
     */
    public function preview(\App\Models\EthicsDocument $document)
    {
        // If a generated file exists, return it directly
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            return response()->file(Storage::disk('public')->path($document->file_path));
        }

        // Fallback: render an HTML preview populated from the draft notes
        $notes = [];
        if ($document->notes) {
            try {
                $decoded = json_decode($document->notes ?: '{}', true);
                if (is_array($decoded)) {
                    $notes = $decoded;
                } else {
                    $notes = ['notes' => (string) $document->notes];
                }
            } catch (\Throwable $e) {
                $notes = ['notes' => (string) $document->notes];
            }
        }

        $proposal = $document->proposal;
        $data = [
            'document_number' => $document->document_number ?? $proposal->nomor_ec ?? '',
            'title' => $proposal->title ?? ($notes['title'] ?? ($document->original_name ?? '')),
            'principal_investigator' => $notes['principal_investigator'] ?? $proposal->researcher?->name ?? $proposal->nama_peneliti ?? '-',
            'members' => $notes['members'] ?? '-',
            'institution' => $notes['institution'] ?? $proposal->institution ?? '-',
            'research_place' => $notes['research_place'] ?? '-',
            'previewDate' => now()->format('d F Y'),
        ];

        return view('admin.publishing.preview', compact('document', 'proposal', 'data'));
    }

    /**
     * Download the ethics document
     */
    public function download(\App\Models\EthicsDocument $document)
    {
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            return response()->download(
                Storage::disk('public')->path($document->file_path),
                $document->original_name ?: 'ethical-clearance.pdf'
            );
        }

        // If no file exists, abort with 404
        abort(404, 'Dokumen tidak ditemukan.');
    }
}