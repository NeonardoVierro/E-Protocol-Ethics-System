<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\GenerateStyledPdfTrait;
use App\Models\EthicsDocument;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KetuaController extends Controller
{
    use GenerateStyledPdfTrait;

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
        $docs = EthicsDocument::with('proposal', 'ketua')
            ->where('status', EthicsDocument::STATUS_DRAFT)
            ->where('ketua_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        $docs->each(function (EthicsDocument $document) {
            $document->preview_data = $this->buildPreviewData($document);
        });

        return view('ketua.persetujuan-ttd.index', compact('docs'));
    }

    public function downloadPreviewPdf(EthicsDocument $document)
    {
        if (Auth::id() !== $document->ketua_id) {
            abort(403);
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            return response()->download(
                Storage::disk('public')->path($document->file_path),
                $document->original_name ?: 'ethical-clearance.pdf'
            );
        }

        $pdfPath = $this->storePreviewPdf($document);

        return response()->download(
            Storage::disk('public')->path($pdfPath),
            ($document->proposal?->title ?? 'ethical-clearance') . '-preview.pdf'
        );
    }

    /**
     * Menandatangani dokumen
     */
    public function signDocument(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:ethics_documents,id',
        ]);

        if (!$request->hasFile('signed_file')) {
            return response()->json(['error' => 'Silakan unggah file PDF hasil tanda tangan terlebih dahulu.'], 422);
        }

        $request->validate([
            'signed_file' => 'file|mimes:pdf|max:10240',
        ]);

        $document = EthicsDocument::findOrFail($request->document_id);

        if (Auth::id() !== $document->ketua_id) {
            return response()->json(['error' => 'Anda tidak memiliki izin untuk menandatangani dokumen ini.'], 403);
        }

        if ($document->status !== EthicsDocument::STATUS_DRAFT) {
            return response()->json(['error' => 'Dokumen tidak dalam status draft.'], 422);
        }

        $uploadedFile = $request->file('signed_file');
        $fileName = $uploadedFile->getClientOriginalName();
        $filePath = $uploadedFile->storeAs('ethics-signed', $this->buildSignedFilename($document, $uploadedFile->getClientOriginalExtension()), 'public');

        DB::transaction(function () use ($document, $filePath, $fileName) {
            $document->update([
                'status'      => EthicsDocument::STATUS_SIGNED,
                'signed_date' => now(),
                'file_path'   => $filePath,
                'original_name' => $fileName ?: ($document->original_name ?: 'ethical-clearance-signed.pdf'),
            ]);

            \App\Models\DocumentLog::create([
                'ethics_document_id' => $document->id,
                'proposal_id'        => $document->proposal_id,
                'user_id'            => Auth::id(),
                'activity'           => \App\Models\DocumentLog::ACTIVITY_SIGN,
                'description'        => 'Dokumen ditandatangani oleh ketua.',
            ]);

            $admin = User::whereHas('roles', function ($q) {
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

        return response()->json(['success' => true, 'document_id' => $document->id]);
    }

    private function buildPreviewData(EthicsDocument $document): array
    {
        $notes = [];
        if (!empty($document->notes)) {
            $decoded = json_decode($document->notes, true);
            if (is_array($decoded)) {
                $notes = $decoded;
            } else {
                $notes = ['notes' => (string) $document->notes];
            }
        }

        $proposal = $document->proposal;

        return [
            'title' => $this->resolvePreviewValue($notes, 'title', $proposal?->title ?? ''),
            'principal_investigator' => $this->resolvePreviewValue($notes, 'principal_investigator', $proposal?->researcher?->name ?? $proposal?->nama_peneliti ?? ''),
            'members' => $this->resolvePreviewValue($notes, 'members', ''),
            'institution' => $this->resolvePreviewValue($notes, 'institution', optional($proposal?->researcher)->institution ?? $proposal?->asal_instansi ?? ''),
            'research_place' => $this->resolvePreviewValue($notes, 'research_place', ''),
            'notes' => $this->resolvePreviewValue($notes, 'notes', ''),
            'nomor_ec' => $document->document_number ?: $proposal?->nomor_ec ?? '-',
            'chair_name' => $document->ketua?->name ?? 'Ketua Komite Etik',
        ];
    }

    private function resolvePreviewValue(array $notes, string $field, string $fallback = ''): string
    {
        $value = $notes[$field] ?? null;
        if (is_string($value)) {
            $value = trim($value);
            if ($value !== '') {
                return $value;
            }
        }

        return $fallback;
    }

    private function storePreviewPdf(EthicsDocument $document): string
    {
        $previewData = $this->buildPreviewData($document);
        // QR generation removed (not included in PDF)

        $partial = view('peneliti.pengajuan.partials.ethical-clearance-document', [
            'certificatePreviewData' => [
                'title' => $previewData['title'] ?? '-',
                'principal_investigator' => $previewData['principal_investigator'] ?? '-',
                'members' => $previewData['members'] ?? '-',
                'institution' => $previewData['institution'] ?? '-',
                'research_place' => $previewData['research_place'] ?? '-',
                'nomor_ec' => $previewData['nomor_ec'] ?? ($document->document_number ?: '-'),
                'chair_name' => $previewData['chair_name'] ?? 'Ketua Komite Etik',
            ],
            'issuedAt' => now()->locale('id')->isoFormat('D MMMM Y'),
            'pdfMode' => true,
        ])->render();

        // Wrap fragment into a full HTML document so Dompdf correctly processes styles
        $html = '<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1">';
        $html .= '</head><body>' . $partial . '</body></html>';

        $pdfContent = $this->renderHtmlToPdf($html);
        $fileName = 'ethical-clearance-preview-' . $document->id . '-' . now()->format('YmdHis') . '.pdf';
        $path = 'ethics-preview/' . $fileName;
        Storage::disk('public')->put($path, $pdfContent);

        return $path;
    }

    

    private function buildSignedFilename(EthicsDocument $document, string $extension = 'pdf'): string
    {
        $baseName = $document->proposal?->title ?? 'ethical-clearance';
        $baseName = preg_replace('/[^A-Za-z0-9._-]+/', '-', strtolower($baseName));
        $baseName = trim($baseName, '-');

        return ($baseName ?: 'ethical-clearance') . '-' . $document->id . '-' . now()->format('YmdHis') . '.' . $extension;
    }
}
