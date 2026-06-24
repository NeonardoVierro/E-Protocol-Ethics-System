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

<div class="bg-white shadow-xl p-8 border border-slate-200 relative min-h-[842px] font-serif text-[12px] leading-relaxed text-[#1a1a1a]">
    @if(!($pdfMode ?? false))
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
            <span class="material-symbols-outlined text-[280px]">verified</span>
        </div>
    @endif

    <div class="text-center mb-6 border-b-2 border-double border-black pb-4">
        <h4 class="font-bold text-[14px] uppercase leading-tight">KOMITE ETIK PENELITIAN KESEHATAN</h4>
        <h4 class="font-bold text-[15px] uppercase mb-1">UNIVERSITAS DIGITAL INDONESIA</h4>
        <p class="text-[10px] leading-tight italic">Jl. Kampus Merdeka No. 123, Jakarta Selatan, 12345. Telp: (021) 555-0123</p>
        <p class="text-[10px] leading-tight font-sans">Email: ethics-committee@udi.ac.id | Web: ethics.udi.ac.id</p>
    </div>

    <div class="text-center mb-6">
        <h5 class="font-bold text-[13px] underline uppercase">KETERANGAN KELAIKAN ETIK</h5>
        <p class="font-sans font-medium text-[11px] mt-1">(ETHICAL CLEARANCE)</p>
        <p class="text-[12px] mt-2">Nomor: {{ $nomorEc }}</p>
    </div>

    <div class="space-y-4 px-2 text-justify">
        <p>Komite Etik Penelitian Kesehatan Universitas Digital Indonesia setelah mempelajari protokol penelitian yang diajukan, dengan ini menyatakan bahwa penelitian dengan judul:</p>
        <p class="font-bold text-center py-2 px-4 italic">"{{ $title }}"</p>
        <div class="grid grid-cols-12 gap-y-2 mt-4">
            <div class="col-span-4 font-bold">Peneliti Utama</div>
            <div class="col-span-8">: {{ $principalInvestigator }}</div>

            <div class="col-span-4 font-bold">Anggota Peneliti</div>
            <div class="col-span-8">{!! nl2br(e($members)) !!}</div>

            <div class="col-span-4 font-bold">Institusi</div>
            <div class="col-span-8">: {{ $institution }}</div>

            <div class="col-span-4 font-bold">Tempat Penelitian</div>
            <div class="col-span-8">: {{ $researchPlace }}</div>
        </div>

        <p class="mt-6">Dinyatakan <strong>LAIK ETIK</strong> untuk dilaksanakan. Sertifikat ini berlaku selama 1 (satu) tahun terhitung sejak tanggal diterbitkan.</p>
    </div>

    <div class="mt-8 grid grid-cols-2">
        <div class="col-start-2 text-center">
            <p>Jakarta, {{ $issuedAt }}</p>
            <p class="mb-12">Ketua Komite Etik,</p>
            <div class="relative inline-block">
                <p class="font-bold underline">{{ $chairName }}</p>
                <p>NIP. 197503122001121002</p>
            </div>
        </div>
    </div>
</div>
