@extends('layouts.admin')

@section('title', 'Preview Ethical Clearance')
@section('page-title', 'Preview Ethical Clearance')
@section('breadcrumb', 'Preview ethical clearance')

@section('content')
<div class="grid grid-cols-12 gap-5" x-data="ethicalClearanceNew()">
    <div class="col-span-7 flex flex-col gap-5">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="text-[16px] font-bold text-slate-900 tracking-tight">Pending Clearance Queue</h2>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-600 bg-amber-50 rounded-full px-3 py-1">
                    <i class="fas fa-bolt text-[10px]"></i>
                    {{ $docs->count() }} Ready
                </span>
            </div>

            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[12%]">Proposal ID</th>
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[18%]">Researcher</th>
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[40%]">Title</th>
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[18%]">Date Approved</th>
                        <th class="text-center text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[12%]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($docs as $d)
                        @php
                            $docId = $d->document_number ?: 'EC-' . $d->id;
                            $proposalId = $d->proposal?->id ?? $d->id;
                            $researcher = $d->proposal?->researcher?->name ?? $d->proposal?->nama_peneliti ?? ($d->ketua?->name ?? '-');
                            $title = $d->proposal?->title ?? ($d->original_name ?? 'Untitled');
                            $date = $d->created_at?->format('M d, Y') ?? '-';
                            $members = $d->notes ? (json_decode($d->notes, true)['members'] ?? ($d->proposal?->anggota_peneliti ?? '-')) : ($d->proposal?->anggota_peneliti ?? '-');
                            $institution = $d->proposal?->asal_instansi ?? ($d->notes ? (json_decode($d->notes, true)['institution'] ?? '-') : '-');
                            $place = $d->notes ? (json_decode($d->notes, true)['research_place'] ?? ($d->proposal?->lokasi_penelitian ?? '-')) : ($d->proposal?->lokasi_penelitian ?? '-');
                        @endphp
                        <tr
                            data-doc-id="{{ $docId }}"
                            data-proposal-id="{{ $proposalId }}"
                            data-researcher="{{ e($researcher) }}"
                            data-title="{{ e($title) }}"
                            data-members="{{ e($members) }}"
                            data-institution="{{ e($institution) }}"
                            data-place="{{ e($place) }}"
                            class="hover:bg-slate-50/60 transition-all cursor-pointer border-l-4 border-transparent"
                            :class="selectedId === '{{ $docId }}' ? 'bg-blue-50/70 border-l-4 border-blue-500 shadow-sm' : ''"
                            @click="selectProposal('{{ $docId }}', {{ $proposalId }}, @js($researcher), @js($title), @js($members), @js($institution), @js($place))"
                        >
                            <td class="px-4 py-4">
                                <span class="text-[13.5px] font-bold leading-snug" :class="selectedId === '{{ $docId }}' ? 'text-[#1e3a5f]' : 'text-slate-800'">{{ $docId }}</span>
                            </td>
                            <td class="px-4 py-4 text-[13px] text-slate-600">{{ $researcher }}</td>
                            <td class="px-4 py-4 text-[13px] text-slate-500 leading-relaxed">{{ $title }}</td>
                            <td class="px-4 py-4 text-[13px] text-slate-500">{{ $date }}</td>
                            <td class="px-4 py-4 text-center">
                                <button type="button" @click.stop="selectProposal('{{ $docId }}', {{ $proposalId }}, @js($researcher), @js($title), @js($members), @js($institution), @js($place))" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-[#1e3a5f] hover:bg-[#1e3a5f]/10 transition-colors" title="Pilih">
                                    <i class="fas fa-arrow-right text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400">Belum ada dokumen pending.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
                <div class="w-11 h-11 bg-teal-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-circle-check text-teal-500 text-base"></i>
                </div>
                <div>
                    <p class="text-[9.5px] font-bold tracking-widest uppercase text-slate-400 mb-1">IN PROCESSING</p>
                    <p class="text-[26px] font-bold text-slate-900 leading-none tracking-tight">{{ $docs->count() }}</p>
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
                <div class="w-11 h-11 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-gear text-purple-500 text-base"></i>
                </div>
                <div>
                    <p class="text-[9.5px] font-bold tracking-widest uppercase text-slate-400 mb-1">APPROVED TODAY</p>
                    <p class="text-[26px] font-bold text-slate-900 leading-none tracking-tight">-</p>
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
                <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-circle-exclamation text-orange-400 text-base"></i>
                </div>
                <div>
                    <p class="text-[9.5px] font-bold tracking-widest uppercase text-slate-400 mb-1">EXPIRING SOON</p>
                    <p class="text-[26px] font-bold text-slate-900 leading-none tracking-tight">-</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-5 flex flex-col gap-5">
        <div class="bg-slate-50 border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 pt-5 pb-4 border-b border-slate-200">
                <h3 class="text-[15px] font-bold text-[#1e3a5f] mb-1">Process Clearance</h3>
                <p class="text-[12.5px] text-slate-500 leading-snug">Configuring ethical certificate for <button type="button" class="font-bold text-[#1e3a5f] underline underline-offset-2 cursor-pointer" x-text="selectedId || '-'">-</button>.</p>
            </div>

            <div class="px-6 py-5 space-y-5">
                <div>
                    <label class="block text-[10.5px] font-bold tracking-widest uppercase text-slate-400 mb-2">Nomor Ethical Clearance</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="clearanceNumber" placeholder="Format: EC-YYYY-MM-XXXX" class="flex-1 px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 font-medium">
                        <button type="button" @click="generateNomorEc()" :disabled="!activePropId || generatingNomor" class="w-10 h-10 bg-[#1e3a5f] rounded-xl flex items-center justify-center text-white hover:bg-[#162d4a] transition-colors cursor-pointer flex-shrink-0 disabled:bg-slate-300 disabled:cursor-not-allowed">
                            <i class="fas fa-rotate text-sm" :class="{'animate-spin': generatingNomor}"></i>
                        </button>
                    </div>
                    <p class="text-[10.5px] text-slate-400 mt-1">Bisa diketik manual atau klik tombol generate.</p>
                </div>

                <div>
                    <label class="block text-[10.5px] font-bold tracking-widest uppercase text-slate-400 mb-2">Assign Ketua Board</label>
                    <select x-model="selectedKetua" :disabled="loadingKetua" class="w-full appearance-none px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 cursor-pointer disabled:bg-slate-100 disabled:cursor-not-allowed">
                        <option value="">Pilih Ketua...</option>
                        <template x-for="k in ketuaList" :key="k.id">
                            <option :value="k.id" x-text="k.name"></option>
                        </template>
                    </select>
                </div>

                <div class="space-y-2">
                    <button type="button" @click="simpanAssignment()" :disabled="!selectedKetua || !clearanceNumber.trim()" class="w-full py-3 rounded-xl text-[13.5px] font-bold text-white flex items-center justify-center gap-2 transition-colors cursor-pointer" :class="(selectedKetua && clearanceNumber.trim()) ? 'bg-[#1e3a5f] hover:bg-[#162d4a]' : 'bg-slate-300 cursor-not-allowed'">
                        <i class="fas fa-save text-sm"></i>
                        Simpan Assignment
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-[14px] font-bold text-slate-900">Preview Dokumen</h3>
            </div>
            <div class="p-6 bg-slate-50">
                <div id="paper" class="w-full bg-white shadow-xl p-8 border border-slate-200 relative min-h-[842px] font-serif text-[12px] leading-relaxed text-[#1a1a1a]">
                    <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
                        <span class="material-symbols-outlined text-[400px]">verified</span>
                    </div>

                    <div class="text-center mb-8 border-b-2 border-double border-black pb-4">
                        <h4 class="font-bold text-[16px] uppercase leading-tight">KOMITE ETIK PENELITIAN KESEHATAN</h4>
                        <h4 class="font-bold text-[18px] uppercase mb-1">UNIVERSITAS DIGITAL INDONESIA</h4>
                        <p class="text-[10px] leading-tight italic">Jl. Kampus Merdeka No. 123, Jakarta Selatan, 12345. Telp: (021) 555-0123</p>
                        <p class="text-[10px] leading-tight font-sans">Email: ethics-committee@udi.ac.id | Web: ethics.udi.ac.id</p>
                    </div>

                    <div class="text-center mb-8">
                        <h5 class="font-bold text-[14px] underline uppercase">KETERANGAN KELAIKAN ETIK</h5>
                        <p class="font-sans font-medium text-[11px] mt-1">(ETHICAL CLEARANCE)</p>
                        <p class="text-[12px] mt-2" x-text="'Nomor: ' + (clearanceNumber || '-')">Nomor: -</p>
                    </div>

                    <div class="space-y-4 px-4 text-justify">
                        <p>Komite Etik Penelitian Kesehatan Universitas Digital Indonesia setelah mempelajari protokol penelitian yang diajukan, dengan ini menyatakan bahwa penelitian dengan judul:</p>
                        <p class="font-bold text-center py-2 px-4 italic" x-text='selectedTitle ? '"' + selectedTitle + '"' : "-"'>"-"</p>
                        <div class="grid grid-cols-12 gap-y-2 mt-4">
                            <div class="col-span-4 font-bold">Peneliti Utama</div>
                            <div class="col-span-8" x-text="': ' + (selectedResearcher || '-')">: -</div>

                            <div class="col-span-4 font-bold">Anggota Peneliti</div>
                            <div class="col-span-8" x-text="': ' + (selectedMembers || '-')">: -</div>

                            <div class="col-span-4 font-bold">Institusi</div>
                            <div class="col-span-8" x-text="': ' + (selectedInstitution || '-')">: -</div>

                            <div class="col-span-4 font-bold">Tempat Penelitian</div>
                            <div class="col-span-8" x-text="': ' + (selectedPlace || '-')">: -</div>
                        </div>

                        <p class="mt-6">Dinyatakan <strong>LAIK ETIK</strong> untuk dilaksanakan. Sertifikat ini berlaku selama 1 (satu) tahun terhitung sejak tanggal diterbitkan.</p>
                    </div>

                    <div class="mt-12 grid grid-cols-2">
                        <div class="col-start-2 text-center">
                            <p x-text="'Jakarta, ' + previewDate">Jakarta, ____________</p>
                            <p class="mb-16">Ketua Komite Etik,</p>
                            <div class="relative inline-block">
                                <p class="font-bold underline">Prof. Dr. Ir. Budi Santoso, M.Eng</p>
                                <p>NIP. 197503122001121002</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-lg bg-slate-50 border-t border-outline-variant flex items-center justify-end">
                <div class="flex gap-3">
                    <button type="button" class="flex items-center gap-2 px-6 py-3 bg-white border text-on-surface font-button rounded-lg btn-hoverable">Print Preview</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function ethicalClearanceNew() {
    return {
        selectedId: '',
        clearanceNumber: '',
        selectedKetua: '',
        ketuaList: [],
        loadingKetua: false,
        activePropId: null,
        selectedTitle: '',
        selectedResearcher: '',
        selectedMembers: '',
        selectedInstitution: '',
        selectedPlace: '',
        previewDate: new Date().toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }),
        generatingNomor: false,

        init() {
            this.loadKetuaList();
        },

        async loadKetuaList() {
            this.loadingKetua = true;
            try {
                const res = await fetch('/admin/ethical-clearance/ketua-list', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                this.ketuaList = await res.json();
            } catch (e) {
                console.error('Failed to load ketua list:', e);
                this.ketuaList = [];
            } finally {
                this.loadingKetua = false;
            }
        },

        selectProposal(docId, proposalId, researcher, title, members, institution, place) {
            this.selectedId = docId;
            this.clearanceNumber = docId;
            this.activePropId = proposalId;

            this.selectedResearcher = researcher || '';
            this.selectedTitle = title || '';
            this.selectedMembers = members || '-';
            this.selectedInstitution = institution || '-';
            this.selectedPlace = place || '-';
            this.selectedKetua = '';
        },

        async generateNomorEc() {
            if (!this.activePropId) return;
            this.generatingNomor = true;
            try {
                const res = await fetch('/admin/ethical-clearance/generate-nomor-ec', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ proposal_id: this.activePropId }),
                });
                const data = await res.json();
                if (data.success) this.clearanceNumber = data.nomor_ec;
                else alert('Gagal generate nomor EC.');
            } catch (e) {
                console.error('Failed to generate nomor EC:', e);
                alert('Gagal generate nomor EC.');
            } finally {
                this.generatingNomor = false;
            }
        },

        async simpanAssignment() {
            if (!this.selectedKetua || !this.clearanceNumber.trim() || !this.activePropId) return;
            try {
                const res = await fetch('/admin/ethical-clearance/pilih-ketua', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        proposal_id: this.activePropId,
                        ketua_id: this.selectedKetua,
                        nomor_ec: this.clearanceNumber.trim(),
                        title: this.selectedTitle,
                        principal_investigator: this.selectedResearcher,
                        members: this.selectedMembers,
                        institution: this.selectedInstitution,
                        research_place: this.selectedPlace,
                    }),
                });
                const data = await res.json();
                if (!res.ok) {
                    const msg = data?.message || data?.error || (data?.errors ? Object.values(data.errors).flat().join('
') : 'Gagal menyimpan assignment.');
                    throw new Error(msg);
                }
                const row = document.querySelector(`tr[data-proposal-id="${this.activePropId}"]`);
                if (row) row.remove();
                alert('Assignment berhasil disimpan.');
            } catch (err) {
                alert(err.message || 'Gagal menyimpan assignment.');
            }
        },
    };
}
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush