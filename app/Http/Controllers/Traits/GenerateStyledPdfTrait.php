<?php

namespace App\Http\Controllers\Traits;

use App\Models\EthicsDocument;
use App\Models\Proposal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

trait GenerateStyledPdfTrait
{
    /**
     * Generate styled PDF for ethics document with proper formatting
     */
    protected function generateStyledEthicsDocumentPdf(EthicsDocument $ethicsDocument, Proposal $proposal): string
    {
        $notes = [];
        if (!empty($ethicsDocument->notes)) {
            $decoded = json_decode($ethicsDocument->notes, true);
            if (is_array($decoded)) {
                $notes = $decoded;
            }
        }

        $certificatePreviewData = [
            'title' => $notes['title'] ?? $proposal->title ?? '-',
            'principal_investigator' => $notes['principal_investigator'] ?? $proposal->researcher?->name ?? $proposal->nama_peneliti ?? '-',
            'members' => $notes['members'] ?? '-',
            'institution' => $notes['institution'] ?? optional($proposal->researcher)->institution ?? $proposal->asal_instansi ?? '-',
            'research_place' => $notes['research_place'] ?? '-',
            'nomor_ec' => $ethicsDocument->document_number ?: $proposal->nomor_ec ?: '-',
            'chair_name' => $notes['chair_name'] ?? 'Ketua Komite Etik',
        ];

        // Render HTML view with PDF mode enabled
        $htmlContent = view('peneliti.pengajuan.partials.ethical-clearance-document', [
            'certificatePreviewData' => $certificatePreviewData,
            'issuedAt' => $ethicsDocument->published_date 
                ? $ethicsDocument->published_date->locale('id')->isoFormat('D MMMM Y')
                : now()->locale('id')->isoFormat('D MMMM Y'),
            'pdfMode' => true,
        ])->render();

        // Wrap fragment into a full HTML document so DOMPDF correctly processes styles
        $html = '<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1">';
        $html .= '</head><body>' . $htmlContent . '</body></html>';

        return $this->renderHtmlToPdf($html);
    }

    /**
     * Render HTML to PDF with error handling
     */
    protected function renderHtmlToPdf(string $html): string
    {
        try {
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions(['isRemoteEnabled' => true, 'isSvgEnabled' => true]);
            return $pdf->output();
        } catch (\Exception $e) {
            Log::error('DOMPDF rendering failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            // Fallback - return a simple PDF with error message
            return $this->buildSimplePdf([
                'Dokumen PDF gagal dirender',
                'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Build simple text-only PDF as fallback
     */
    protected function buildSimplePdf(array $lines): string
    {
        $escapedLines = array_map(function ($line) {
            return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], (string) $line);
        }, $lines);

        $content = '';
        $y = 760;
        foreach ($escapedLines as $line) {
            $content .= "BT /F1 12 Tf 50 {$y} Td ({$line}) Tj ET\n";
            $y -= 16;
        }

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj";
        $objects[] = "4 0 obj\n<< /Length 0 >>\nstream\n{$content}endstream\nendobj";
        $objects[] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj";

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        $offset = strlen($pdf);

        foreach ($objects as $object) {
            $offsets[] = $offset;
            $pdf .= $object . "\n";
            $offset = strlen($pdf);
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        foreach ($offsets as $offsetValue) {
            $pdf .= sprintf("%010d 00000 n \n", $offsetValue);
        }

        $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n{$xrefPosition}\n%%EOF";

        return $pdf;
    }
}
