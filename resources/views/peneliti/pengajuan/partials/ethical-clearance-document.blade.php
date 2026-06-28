@php
    $preview = is_array($certificatePreviewData ?? null) ? $certificatePreviewData : [];
    $title = $preview['title'] ?? '-';
    $principalInvestigator = $preview['principal_investigator'] ?? '-';
    $members = $preview['members'] ?? '-';
    $institution = $preview['institution'] ?? '-';
    $researchPlace = $preview['research_place'] ?? '-';
    $chairName = $preview['chair_name'] ?? 'Ketua Komite Etik';
    $nomorEc = $preview['nomor_ec'] ?? '-';
    $issuedAt = $issuedAt ?? now()->locale('id')->isoFormat('D MMMM Y');
@endphp

<style>
    .pdf-certificate {
        background: #fff;
        color: #1a1a1a;
        font-family: "Times New Roman", Times, serif;
        font-size: 12px;
        line-height: 1.6;
        min-height: 842px;
        box-sizing: border-box;
        padding: 28px 32px;
        border: none;
        position: relative;
        max-width: 595px;
        margin: 0 auto;
    }
    .pdf-certificate .paper-backdrop {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0.03;
        pointer-events: none;
    }
    .pdf-certificate .paper-backdrop span {
        font-size: 280px;
        line-height: 1;
    }
    .pdf-certificate .section-header {
        margin-bottom: 14px;
        padding-bottom: 6px;
        text-align: center;
    }
    .pdf-certificate .kop-committee {
        font-size: 16px;
        font-weight: 800;
        margin: 0;
        letter-spacing: 0.6px;
    }
    .pdf-certificate .kop-university {
        font-size: 20px;
        font-weight: 900;
        margin: 6px 0 4px;
        letter-spacing: 0.8px;
    }
    .pdf-certificate .kop-address {
        font-size: 10.5px;
        font-style: italic;
        margin: 0;
        line-height: 1.15;
    }
    .pdf-certificate .kop-contact {
        font-size: 10.5px;
        margin: 0;
        line-height: 1.15;
    }
    .pdf-certificate .kop-hr {
        width: 100%;
        height: 1.5px;
        background: #000;
        margin: 12px 0 0 0;
    }
    .pdf-certificate .section-title {
        margin-bottom: 16px;
        text-align: center;
    }
    .pdf-certificate .section-title h5 {
        font-size: 14px;
        text-transform: uppercase;
        font-weight: 700;
        text-decoration: underline;
        margin-bottom: 6px;
    }
    .pdf-certificate .section-title p {
        margin: 0;
        font-size: 11px;
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }
    .pdf-certificate .content {
        margin-top: 16px;
        padding-left: 8px;
        padding-right: 8px;
        text-align: justify;
    }
    .pdf-certificate .content p.certificate-title {
        font-weight: 700;
        text-align: center;
        padding: 8px 16px;
        font-style: italic;
        margin: 10px 0 14px 0;
        font-size: 13px;
    }
    .pdf-certificate .certificate-grid {
        display: table;
        width: 100%;
        margin-top: 16px;
        border-collapse: collapse;
    }
    .pdf-certificate .certificate-grid .row {
        display: table-row;
    }
    .pdf-certificate .certificate-grid .col-label,
    .pdf-certificate .certificate-grid .col-value {
        display: table-cell;
        vertical-align: top;
        padding: 4px 8px;
    }
    .pdf-certificate .certificate-grid .col-label {
        width: 180px;
        font-weight: 700;
        padding-right: 12px;
        white-space: nowrap;
    }
    .pdf-certificate .certificate-grid .col-value {
        width: auto;
        word-wrap: break-word;
        white-space: normal;
    }
    .pdf-certificate .footer-row {
        margin-top: 28px;
        display: table;
        width: 100%;
    }
    .pdf-certificate .footer-right {
        text-align: right;
        display: table-cell;
        vertical-align: top;
        width: 50%;
    }
    .pdf-certificate .footer-right p {
        margin: 0;
    }
    .pdf-certificate .footer-right .signature {
        font-weight: 700;
        text-decoration: underline;
        margin-bottom: 0.25rem;
    }
    .pdf-certificate .bottom-footer {
        margin-top: 28px;
        display: table;
        width: 100%;
        border-top: 1px solid #e5e7eb;
        padding-top: 12px;
        box-sizing: border-box;
    }
    .pdf-certificate .bottom-footer .left,
    .pdf-certificate .bottom-footer .right {
        display: table-cell;
        vertical-align: middle;
    }
    .pdf-certificate .bottom-footer .left {
        width: 60%;
    }
    .pdf-certificate .bottom-footer .right {
        width: 40%;
        text-align: right;
        font-size: 10px;
        color: #6b7280;
    }
    .pdf-certificate .qr-box {
        width: 56px;
        height: 56px;
        border: 1px solid #e5e7eb;
        display: inline-block;
        vertical-align: middle;
        text-align: center;
        line-height: 56px;
        color: #9ca3af;
        margin-right: 12px;
        background: #fff;
    }
    .pdf-certificate .verification-text {
        display: inline-block;
        vertical-align: middle;
        font-size: 11px;
        color: #6b7280;
    }
</style>

@if($pdfMode ?? false)
    <style>
        @page { margin: 18mm; }
        body { margin: 0; }
    </style>
@endif

<div class="bg-white shadow-xl p-8 border border-slate-200 relative min-h-[842px] font-serif text-[12px] leading-relaxed text-[#1a1a1a] pdf-certificate">
    @if(!($pdfMode ?? false))
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
            <span class="material-symbols-outlined text-[280px]">verified</span>
        </div>
    @else
        <div class="paper-backdrop">
            <!-- Inline SVG watermark for Dompdf -->
            <svg width="520" height="520" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <g fill="#000000" fill-opacity="0.03">
                    <path d="M50 5 L61 35 L95 35 L67 55 L78 90 L50 70 L22 90 L33 55 L5 35 L39 35 Z"/>
                </g>
            </svg>
        </div>
    @endif

    <div class="text-center mb-6 section-header">
        <h4 class="kop-committee">KOMITE ETIK PENELITIAN KESEHATAN</h4>
        <h4 class="kop-university">UNIVERSITAS DIGITAL INDONESIA</h4>
        <p class="kop-address">Jl. Kampus Merdeka No. 123, Jakarta Selatan, 12345. Telp: (021) 555-0123</p>
        <p class="kop-contact">Email: ethics-committee@udi.ac.id | Web: ethics.udi.ac.id</p>
        <div class="kop-hr" aria-hidden="true"></div>
    </div>

    <div class="text-center mb-6 section-title">
        <h5 class="font-bold text-[13px] underline uppercase">KETERANGAN KELAIKAN ETIK</h5>
        <p class="font-sans font-medium text-[11px] mt-1">(ETHICAL CLEARANCE)</p>
        <p class="text-[12px] mt-2">Nomor: {{ $nomorEc }}</p>
    </div>

    <div class="space-y-4 px-2 text-justify content">
        <p>Komite Etik Penelitian Kesehatan Universitas Digital Indonesia setelah mempelajari protokol penelitian yang diajukan, dengan ini menyatakan bahwa penelitian dengan judul:</p>
        <p class="font-bold text-center py-2 px-4 italic certificate-title">"{{ $title }}"</p>
        <div class="certificate-grid mt-4">
            <div class="row"><div class="col-label">Peneliti Utama</div><div class="col-value">: {{ $principalInvestigator }}</div></div>
            <div class="row"><div class="col-label">Anggota Peneliti</div><div class="col-value">: {!! nl2br(e($members)) !!}</div></div>
            <div class="row"><div class="col-label">Institusi</div><div class="col-value">: {{ $institution }}</div></div>
            <div class="row"><div class="col-label">Tempat Penelitian</div><div class="col-value">: {{ $researchPlace }}</div></div>
        </div>

        <p class="mt-6">Dinyatakan <strong>LAIK ETIK</strong> untuk dilaksanakan. Sertifikat ini berlaku selama 1 (satu) tahun terhitung sejak tanggal diterbitkan.</p>
    </div>

    <div class="footer-row">
        <div></div>
        <div class="footer-right">
            <p>Jakarta, {{ $issuedAt }}</p>
            <p style="margin-bottom:48px;">Ketua Komite Etik,</p>
            <div>
                <p class="signature">{{ $chairName }}</p>
                <p style="margin-top:6px;">NIP. 197503122001121002</p>
            </div>
        </div>
    </div>

    <div class="bottom-footer" role="contentinfo">
        <div class="right">
            Halaman 1 dari 1 &nbsp; | &nbsp; Cetakan Sistem: -
        </div>
    </div>
</div>
