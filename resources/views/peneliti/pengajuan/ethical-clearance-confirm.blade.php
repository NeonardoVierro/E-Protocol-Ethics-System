@php
    $previewData = is_array($certificatePreviewData ?? null) ? $certificatePreviewData : [];
@endphp

@extends('layouts.dashboard')

@section('title', 'Konfirmasi Ethical Clearance')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-0 to-slate-100 py-8 lg:py-12 px-4 sm:px-6 lg:px-8" x-data="{
    clearanceNumber: @js($previewData['nomor_ec'] ?? $proposal->nomor_ec ?? $ethicsDocument?->document_number ?? ''),
    title: @js($previewData['title'] ?? $proposal->title ?? ''),
    principalInvestigator: @js($previewData['principal_investigator'] ?? optional($proposal->researcher)->name ?? ''),
    members: @js($previewData['members'] ?? ''),
    institution: @js($previewData['institution'] ?? ''),
    researchPlace: @js($previewData['research_place'] ?? ''),
    chairName: @js(optional($assignment?->assignedTo)->name ?? ''),
    init() {
        this.$nextTick(() => this.initFromDraft());
    },
    initFromDraft() {
        const data = @json($certificatePreviewData ?? []);

        if (data.nomor_ec) this.clearanceNumber = data.nomor_ec;
        if (data.title) this.title = data.title;
        if (data.principal_investigator) this.principalInvestigator = data.principal_investigator;
        if (data.members) this.members = data.members;
        if (data.institution) this.institution = data.institution;
        if (data.research_place) this.researchPlace = data.research_place;
        if (data.chair_name) this.chairName = data.chair_name;
    },
    async savePreviewData() {
        try {
            await fetch('{{ route('pengajuan.ethical-clearance.save-preview-data', $proposal->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    title: this.title,
                    principal_investigator: this.principalInvestigator,
                    members: this.members,
                    institution: this.institution,
                    research_place: this.researchPlace,
                }),
            });
        } catch (e) {
            console.error('Failed to save preview data:', e);
        }
    },
    async submitConfirm(event) {
        await this.savePreviewData();
        event.target.submit();
    }
}">
    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-3xl shadow-lg border border-slate-200 overflow-hidden">
            <div class="px-8 py-8 border-b border-slate-200">
                <h1 class="text-3xl font-bold text-slate-900">Konfirmasi Dokumen Ethical Clearance</h1>
                <p class="text-slate-600 mt-2">Periksa kembali detail pengajuan dan nomor EC. Setelah Anda konfirmasi, dokumen akan dikirim ke ketua untuk tanda tangan.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-slate-50 rounded-3xl border border-slate-200 p-6">
                        <h2 class="text-xl font-semibold text-slate-900 mb-4">Informasi Proposal</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400 mb-2">Judul Proposal</p>
                                <p class="text-sm text-slate-800 font-semibold">{{ $proposal->title }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400 mb-2">Nomor EC</p>
                                <p class="text-sm text-slate-800 font-semibold">{{ $previewData['nomor_ec'] ?? $proposal->nomor_ec ?? $ethicsDocument?->document_number ?? 'Belum terisi' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400 mb-2">Peneliti</p>
                                <p class="text-sm text-slate-800 font-semibold">{{ optional($proposal->researcher)->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400 mb-2">Ketua yang ditetapkan</p>
                                <p class="text-sm text-slate-800 font-semibold">{{ optional($assignment?->assignedTo)->name ?? $previewData['chair_name'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 p-6">
                        <h2 class="text-xl font-semibold text-slate-900 mb-4">Pratinjau Sertifikat</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10.5px] font-bold tracking-widest uppercase text-slate-400 mb-2">Project Title</label>
                                <input type="text" x-model="title" @input="savePreviewData()" class="w-full px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 font-medium">
                            </div>
                            <div>
                                <label class="block text-[10.5px] font-bold tracking-widest uppercase text-slate-400 mb-2">Ketua Peneliti</label>
                                <input type="text" x-model="principalInvestigator" @input="savePreviewData()" class="w-full px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 font-medium">
                            </div>
                            <div>
                                <label class="block text-[10.5px] font-bold tracking-widest uppercase text-slate-400 mb-2">Anggota Peneliti (pisahkan baris)</label>
                                <textarea x-model="members" @input="savePreviewData()" rows="3" class="w-full px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 font-medium"></textarea>
                            </div>
                            <div>
                                <label class="block text-[10.5px] font-bold tracking-widest uppercase text-slate-400 mb-2">Institusi</label>
                                <input type="text" x-model="institution" @input="savePreviewData()" class="w-full px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 font-medium">
                            </div>
                            <div>
                                <label class="block text-[10.5px] font-bold tracking-widest uppercase text-slate-400 mb-2">Tempat Penelitian</label>
                                <input type="text" x-model="researchPlace" @input="savePreviewData()" class="w-full px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 font-medium">
                            </div>
                        </div>
                        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-[0.16em] text-slate-400 mb-3">Pratinjau Sertifikat</p>
                            <div class="rounded-2xl border border-slate-200 bg-white p-6 font-serif text-[12px] leading-relaxed text-[#1a1a1a] relative min-h-[420px]">
                                <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
                                    <span class="material-symbols-outlined text-[280px]">verified</span>
                                </div>

                                <div class="text-center mb-6 border-b-2 border-double border-black pb-4">
                                    <h4 class="font-bold text-[14px] uppercase leading-tight">KOMITE ETIK PENELITIAN KESEHATAN</h4>
                                    <h4 class="font-bold text-[15px] uppercase mb-1">UNIVERSITAS DIGITAL INDONESIA</h4>
                                    <p class="text-[10px] leading-tight italic">Jl. Kampus Merdeka No. 123, Jakarta Selatan, 12345. Telp: (021) 555-0123</p>
                                    <p class="text-[10px] leading-tight font-sans">Email: ethics-committee@udi.ac.id | Web: ethics.udi.ac.id</p>
                                </div>

                                <div class="text-center mb-6">
                                    <h5 class="font-bold text-[13px] underline uppercase">KETERANGAN KELAIKAN ETIK</h5>
                                    <p class="font-sans font-medium text-[11px] mt-1">(ETHICAL CLEARANCE)</p>
                                    <p class="text-[12px] mt-2" x-text="'Nomor: ' + (clearanceNumber || '-')">Nomor: -</p>
                                </div>

                                <div class="space-y-4 px-2 text-justify">
                                    <p>Komite Etik Penelitian Kesehatan Universitas Digital Indonesia setelah mempelajari protokol penelitian yang diajukan, dengan ini menyatakan bahwa penelitian dengan judul:</p>
                                    <p class="font-bold text-center py-2 px-4 italic" x-text="title || '-'">-</p>
                                    <div class="grid grid-cols-12 gap-y-2 mt-4">
                                        <div class="col-span-4 font-bold">Peneliti Utama</div>
                                        <div class="col-span-8" x-text="': ' + (principalInvestigator || '-')">: -</div>

                                        <div class="col-span-4 font-bold">Anggota Peneliti</div>
                                        <div class="col-span-8" x-html="members ? ': ' + members.replace(/\n/g, '<br/>') : ': -'">: -</div>

                                        <div class="col-span-4 font-bold">Institusi</div>
                                        <div class="col-span-8" x-text="': ' + (institution || '-')">: -</div>

                                        <div class="col-span-4 font-bold">Tempat Penelitian</div>
                                        <div class="col-span-8" x-text="': ' + (researchPlace || '-')">: -</div>
                                    </div>

                                    <p class="mt-6">Dinyatakan <strong>LAIK ETIK</strong> untuk dilaksanakan. Sertifikat ini berlaku selama 1 (satu) tahun terhitung sejak tanggal diterbitkan.</p>
                                </div>

                                <div class="mt-8 grid grid-cols-2">
                                    <div class="col-start-2 text-center">
                                        <p class="text-[11px]">Jakarta, ____________</p>
                                        <p class="mb-12">Ketua Komite Etik,</p>
                                        <div class="relative inline-block">
                                            <p class="font-bold underline" x-text="chairName || 'Ketua Komite Etik'">Ketua Komite Etik</p>
                                            <p>NIP. 197503122001121002</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 p-6">
                        <h2 class="text-xl font-semibold text-slate-900 mb-4">Dokumen Proposal</h2>
                        <div class="space-y-3">
                            @foreach($proposalFiles as $file)
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $file->original_name }}</p>
                                        <p class="text-xs text-slate-500">{{ ucfirst($file->file_type) }}</p>
                                    </div>
                                    @if($file->file_path)
                                        <a href="{{ route('pengajuan.riwayat-pengajuan.revision-file.view', ['proposal' => $proposal->id, 'file' => $file->id]) }}" class="text-blue-600 text-sm hover:underline">Lihat</a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-slate-50 rounded-3xl border border-slate-200 p-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-3">Status Saat Ini</h3>
                        <div class="rounded-2xl bg-white border border-slate-200 p-4">
                            <p class="text-sm text-slate-500 mb-2">Status proposal</p>
                            <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-xs font-semibold">{{ $proposal->status_label }}</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 p-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-3">Petunjuk</h3>
                        <ol class="list-decimal list-inside space-y-3 text-sm text-slate-600">
                            <li>Periksa kembali seluruh data proposal dan nomor EC.</li>
                            <li>Pastikan Ketua yang ditetapkan sudah sesuai.</li>
                            <li>Jika sudah benar, klik tombol konfirmasi untuk melanjutkan ke tanda tangan Ketua.</li>
                        </ol>
                    </div>

                    <form action="{{ route('pengajuan.ethical-clearance.confirm.submit', $proposal->id) }}" method="POST" @submit.prevent="savePreviewData().then(() => $el.submit())">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">Konfirmasi dan Kirim ke Ketua</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
