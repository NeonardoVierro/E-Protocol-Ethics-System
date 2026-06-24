<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview Ethical Clearance</title>
    <style>
        body { font-family: serif; color:#111; padding:40px; }
        .paper { width: 794px; margin: 0 auto; background: #fff; padding:40px; box-shadow:0 0 0 1px #e5e7eb; }
        .center { text-align:center; }
        .underline { text-decoration: underline; }
        .small { font-size: 12px; }
        .muted { color:#6b7280; }
    </style>
</head>
<body>
    <div class="paper">
        <div class="center" style="border-bottom:2px solid #000; padding-bottom:12px; margin-bottom:18px;">
            <h4 style="margin:0; font-weight:700; text-transform:uppercase;">KOMITE ETIK PENELITIAN KESEHATAN</h4>
            <h4 style="margin:0; font-weight:700; text-transform:uppercase;">UNIVERSITAS DIGITAL INDONESIA</h4>
            <p class="small muted" style="margin:0;">Jl. Kampus Merdeka No. 123, Jakarta Selatan, 12345. Telp: (021) 555-0123</p>
            <p class="small muted" style="margin:0;">Email: ethics-committee@udi.ac.id | Web: ethics.udi.ac.id</p>
        </div>

        <div class="center" style="margin-bottom:14px;">
            <h5 style="margin:0; font-weight:700; text-decoration:underline;">KETERANGAN KELAIKAN ETIK</h5>
            <p class="small" style="margin:4px 0 8px;">(ETHICAL CLEARANCE)</p>
            <p style="margin:0 0 8px;"><strong>Nomor:</strong> {{ $document_number ?: '-' }}</p>
        </div>

        <div class="small" style="text-align:justify;">
            <p>Komite Etik Penelitian Kesehatan Universitas Digital Indonesia setelah mempelajari protokol penelitian yang diajukan, dengan ini menyatakan bahwa penelitian dengan judul:</p>
            <p class="center" style="font-weight:700; font-style:italic;">"{{ $title ?? '-' }}"</p>

            <div style="margin-top:12px;">
                <div style="display:flex; gap:8px; margin-bottom:6px;"><div style="width:160px; font-weight:700;">Peneliti Utama</div><div>: {{ $principal_investigator ?? '-' }}</div></div>
                <div style="display:flex; gap:8px; margin-bottom:6px;"><div style="width:160px; font-weight:700;">Anggota Peneliti</div><div>: {!! nl2br(e($members ?? '-')) !!}</div></div>
                <div style="display:flex; gap:8px; margin-bottom:6px;"><div style="width:160px; font-weight:700;">Institusi</div><div>: {{ $institution ?? '-' }}</div></div>
                <div style="display:flex; gap:8px; margin-bottom:6px;"><div style="width:160px; font-weight:700;">Tempat Penelitian</div><div>: {{ $research_place ?? '-' }}</div></div>
            </div>

            <p style="margin-top:18px;">Dinyatakan <strong>LAIK ETIK</strong> untuk dilaksanakan. Sertifikat ini berlaku selama 1 (satu) tahun terhitung sejak tanggal diterbitkan.</p>
        </div>

        <div style="margin-top:40px; text-align:right;">
            <p>{{ $previewDate ?? 'Jakarta, ____________' }}</p>
            <p style="margin-top:30px;">Ketua Komite Etik,</p>
            <p style="margin-top:40px; font-weight:700; text-decoration:underline;">Prof. Dr. Ir. Budi Santoso, M.Eng</p>
            <p>NIP. 197503122001121002</p>
        </div>
    </div>
</body>
</html>
