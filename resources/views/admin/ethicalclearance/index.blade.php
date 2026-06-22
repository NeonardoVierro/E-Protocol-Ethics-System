@extends('layouts.admin')

@section('title', 'Ethical Clearance - Admin')
@section('page-title', 'Ethical Clearance')
@section('breadcrumb', 'Kelola ethical clearance')

@section('content')

{{-- ═══════════════════════════════════════════
     2 Kolom: Queue (kiri) + Process Panel (kanan)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-12 gap-5" x-data="ethicalClearance()">

    {{-- ── KIRI: Pending Queue + Stat Cards ── --}}
    <div class="col-span-7 flex flex-col gap-5">

        {{-- Pending Clearance Queue --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="text-[16px] font-bold text-slate-900 tracking-tight">Pending Clearance Queue</h2>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-600 bg-amber-50 rounded-full px-3 py-1">
                    <i class="fas fa-bolt text-[10px]"></i>
                    12 Ready
                </span>
            </div>

            {{-- Table --}}
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
                        @foreach($docs as $d)
                        @php
                            $docId = $d->document_number ?: 'EC-' . $d->id;
                            $proposalId = $d->proposal?->id ?? $d->id;
                            $researcher = $d->proposal?->researcher?->name ?? $d->proposal?->nama_peneliti ?? ($d->ketua?->name ?? '-');
                            $title = $d->proposal?->title ?? ($d->original_name ?? 'Untitled');
                            $date = $d->created_at?->format('M d, Y') ?? '-';
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors"
                            :class="selectedId === '{{ $docId }}' ? 'bg-blue-50/60' : ''"
                            @click="selectProposal('{{ $docId }}', {{ $proposalId }}, '{{ addslashes($researcher) }}', '{{ addslashes($title) }}')">
                            <td class="px-4 py-4">
                                <span class="text-[13.5px] font-bold leading-snug"
                                      :class="selectedId === '{{ $docId }}' ? 'text-[#1e3a5f]' : 'text-slate-800'">
                                    {{ $docId }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-[13px] text-slate-600">{{ $researcher }}</td>
                            <td class="px-4 py-4 text-[13px] text-slate-500 leading-relaxed">{{ $title }}</td>
                            <td class="px-4 py-4 text-[13px] text-slate-500">{{ $date }}</td>
                            <td class="px-4 py-4 text-center">
                                <button @click.stop="showPreview({{ $proposalId }})"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-[#1e3a5f] hover:bg-[#1e3a5f]/10 transition-colors"
                                        title="Lihat Preview">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                </tbody>
            </table>
        </div>

        {{-- Stat Cards (3 kolom) --}}
        <div class="grid grid-cols-3 gap-4">
            @php
            $stats = [
                ['icon'=>'fas fa-file-circle-check','iconBg'=>'bg-teal-50',   'iconColor'=>'text-teal-500',  'label'=>'IN PROCESSING','value'=>'08'],
                ['icon'=>'fas fa-gear',              'iconBg'=>'bg-purple-50', 'iconColor'=>'text-purple-500','label'=>'APPROVED TODAY','value'=>'14'],
                ['icon'=>'fas fa-circle-exclamation','iconBg'=>'bg-orange-50', 'iconColor'=>'text-orange-400','label'=>'EXPIRING SOON', 'value'=>'03'],
            ];
            @endphp
            @foreach($stats as $s)
            <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
                <div class="w-11 h-11 {{ $s['iconBg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="{{ $s['icon'] }} {{ $s['iconColor'] }} text-base"></i>
                </div>
                <div>
                    <p class="text-[9.5px] font-bold tracking-widest uppercase text-slate-400 mb-1">{{ $s['label'] }}</p>
                    <p class="text-[26px] font-bold text-slate-900 leading-none tracking-tight">{{ $s['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

    </div>

    {{-- ── KANAN: Process Clearance Panel + Preview ── --}}
    <div class="col-span-5 flex flex-col gap-5">

        {{-- Process Clearance Panel --}}
        <div class="bg-slate-50 border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            {{-- Panel Header --}}
            <div class="px-6 pt-5 pb-4 border-b border-slate-200">
                <h3 class="text-[15px] font-bold text-[#1e3a5f] mb-1">Process Clearance</h3>
                <p class="text-[12.5px] text-slate-500 leading-snug">
                    Configuring ethical certificate for
                    <button @click="" class="font-bold text-[#1e3a5f] underline underline-offset-2 cursor-pointer" x-text="selectedId">EC-2023-0902</button>.
                </p>
            </div>

            <div class="px-6 py-5 space-y-5">

                {{-- Nomor Ethical Clearance --}}
                <div>
                    <label class="block text-[10.5px] font-bold tracking-widest uppercase text-slate-400 mb-2">Nomor Ethical Clearance</label>
                    <div class="flex gap-2">
                        <input type="text"
                               x-model="clearanceNumber"
                               placeholder="Format: EC-YYYY-MM-XXXX"
                               class="flex-1 px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 font-medium">
                        <button @click="generateNomorEc()"
                                :disabled="!activePropId || generatingNomor"
                                class="w-10 h-10 bg-[#1e3a5f] rounded-xl flex items-center justify-center text-white hover:bg-[#162d4a] transition-colors cursor-pointer flex-shrink-0 disabled:bg-slate-300 disabled:cursor-not-allowed">
                            <i class="fas fa-rotate text-sm" :class="{'animate-spin': generatingNomor}"></i>
                        </button>
                    </div>
                    <p class="text-[10.5px] text-slate-400 mt-1">Format: EC-YYYY-MM-XXXX (contoh: EC-2024-06-0001)</p>
                </div>

                {{-- Assign Ketua Board --}}
                <div>
                    <label class="block text-[10.5px] font-bold tracking-widest uppercase text-slate-400 mb-2">Assign Ketua Board</label>
                    <div class="relative">
                        <select x-model="selectedKetua" :disabled="loadingKetua" class="w-full appearance-none px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 cursor-pointer disabled:bg-slate-100 disabled:cursor-not-allowed">
                            <option value="">Pilih Ketua...</option>
                            <template x-if="loadingKetua">
                                <option value="" disabled>Loading...</option>
                            </template>
                            <template x-for="k in ketuaList" :key="k.id">
                                <option :value="k.id" x-text="k.name"></option>
                            </template>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        <p x-show="ketuaList.length === 0 && !loadingKetua" class="text-[11px] text-red-500 mt-1">Tidak ada ketua tersedia</p>
                    </div>
                </div>


                {{-- Action Buttons --}}
                <div class="space-y-2">
                    <button @click="simpanAssignment()"
                            :disabled="!selectedKetua || !clearanceNumber.trim()"
                            class="w-full py-3 rounded-xl text-[13.5px] font-bold text-white flex items-center justify-center gap-2 transition-colors cursor-pointer"
                            :class="(selectedKetua && clearanceNumber.trim()) ? 'bg-[#1e3a5f] hover:bg-[#162d4a]' : 'bg-slate-300 cursor-not-allowed'">
                        <i class="fas fa-save text-sm"></i>
                        Simpan Assignment
                    </button>
                    <button @click="kirimAssignment()"
                            :disabled="!isSaved"
                            class="w-full py-3 rounded-xl text-[13.5px] font-bold text-white flex items-center justify-center gap-2 transition-colors cursor-pointer"
                            :class="isSaved ? 'bg-purple-600 hover:bg-purple-700' : 'bg-slate-300 cursor-not-allowed'">
                        <i class="fas fa-paper-plane text-sm"></i>
                        Kirim ke Ketua
                    </button>
                </div>
                <p class="text-[11px] text-slate-400 text-center leading-snug -mt-2">
                    By sending to ketua, the proposal will be moved to the publishing queue.
                </p>

            </div>
        </div>

        {{-- Preview Template --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-[14px] font-bold text-slate-900">Preview Dokumen</h3>
                <button x-show="previewUrl" @click="closePreview()"
                        class="text-[11px] text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-xmark"></i> Tutup
                </button>
            </div>
            <div x-show="previewUrl" class="p-0" style="height: 500px;">
                <iframe :src="previewUrl" 
                        class="w-full h-full border-0"
                        frameborder="0"></iframe>
            </div>
            <div x-show="!previewUrl" class="flex flex-col items-center justify-center py-10 px-6 text-center">
                <div class="w-16 h-16 mb-4 flex items-center justify-center">
                    <i class="fas fa-shield-check text-slate-200 text-5xl"></i>
                </div>
                <p class="text-[12.5px] text-slate-400">Klik ikon mata di kolom Action untuk melihat preview PDF</p>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function ethicalClearance() {
    return {
        selectedId:         '{{ $docs->first() ? ($docs->first()->document_number ?: "EC-".$docs->first()->id) : "" }}',
        clearanceNumber:    '{{ $docs->first() ? ($docs->first()->document_number ?: "") : "" }}',
        selectedKetua:      null,
        ketuaList:          [],
        loadingKetua:       false,
        isSaved:            false,
        activePropId:       null,
        selectedTitle:      null,
        selectedResearcher: null,
        previewUrl:         null,
        generatingNomor:   false,

        init() {
            this.loadKetuaList();
        },

        async loadKetuaList() {
            this.loadingKetua = true;
            try {
                const res = await fetch('/admin/ethical-clearance/ketua-list', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                const data = await res.json();
                console.log('Ketua list loaded:', data);
                this.ketuaList = data;
            } catch (e) {
                console.error('Failed to load ketua list:', e);
                this.ketuaList = [];
            } finally {
                this.loadingKetua = false;
            }
        },

        selectProposal(docId, proposalId, researcher, title) {
            this.selectedId = docId;
            this.clearanceNumber = docId;
            this.activePropId = proposalId;
            this.selectedTitle = title;
            this.selectedResearcher = researcher;
            this.isSaved = false;
            this.selectedKetua = null;
            console.log('Proposal selected:', { docId, proposalId, researcher, title });
            
            // Load existing assignment if any
            this.loadExistingAssignment(proposalId);
        },

        async loadExistingAssignment(proposalId) {
            try {
                const res = await fetch(`/admin/ethical-clearance/get-assignment?proposal_id=${proposalId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                const data = await res.json();
                if (data.success && data.assignment) {
                    this.selectedKetua = data.assignment.ketua_id;
                    this.clearanceNumber = data.assignment.nomor_ec;
                    this.isSaved = true;
                }
            } catch (e) {
                console.error('Failed to load existing assignment:', e);
            }
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
                    },
                    body: JSON.stringify({ proposal_id: this.activePropId }),
                });
                const data = await res.json();
                if (data.success) {
                    this.clearanceNumber = data.nomor_ec;
                } else {
                    alert('Gagal generate nomor EC.');
                }
            } catch (e) {
                console.error('Failed to generate nomor EC:', e);
                alert('Gagal generate nomor EC.');
            } finally {
                this.generatingNomor = false;
            }
        },

        showPreview(proposalId) {
            this.previewUrl = `/admin/proposal/${proposalId}/preview`;
        },

        closePreview() {
            this.previewUrl = null;
        },

        async simpanAssignment() {
            if (!this.selectedKetua || !this.clearanceNumber.trim()) return;
            try {
                const res = await fetch('/admin/ethical-clearance/pilih-ketua', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({
                        proposal_id: this.activePropId,
                        ketua_id: this.selectedKetua,
                        nomor_ec: this.clearanceNumber,
                    }),
                });
                if (!res.ok) throw new Error();
                this.isSaved = true;
                alert('Assignment berhasil disimpan. Klik "Kirim ke Ketua" untuk mengirim.');
            } catch {
                alert('Gagal menyimpan assignment.');
            }
        },

        async kirimAssignment() {
            if (!this.isSaved) return;
            try {
                const res = await fetch('/admin/ethical-clearance/kirim-ketua', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ proposal_id: this.activePropId }),
                });
                if (!res.ok) throw new Error();
                alert('Proposal berhasil dikirim ke ketua.');
                location.reload();
            } catch {
                alert('Gagal mengirim ke ketua.');
            }
        },
    };
}
</script>
@endpush