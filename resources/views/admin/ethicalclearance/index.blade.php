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
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">Proposal ID</th>
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Researcher</th>
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Title</th>
                        <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Date Approved</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                    $proposals = [
                        ['id'=>'EC-2023-0891','researcher'=>'Dr. Helena Vane',  'title'=>'Impact of Micro-p...','date'=>'Oct 24, 2023'],
                        ['id'=>'EC-2023-0902','researcher'=>'Prof. Alan Turing','title'=>'Neural Pattern An...','date'=>'Oct 25, 2023'],
                        ['id'=>'EC-2023-0915','researcher'=>'Sarah Connor',     'title'=>'Societal Resilienc...','date'=>'Oct 26, 2023'],
                        ['id'=>'EC-2023-0924','researcher'=>'Dr. Marcus Wright','title'=>'Cyber-Human Inte...', 'date'=>'Oct 27, 2023'],
                    ];
                    @endphp

                    @foreach($proposals as $p)
                    <tr class="hover:bg-slate-50/60 transition-colors cursor-pointer"
                        :class="selectedId === '{{ $p['id'] }}' ? 'bg-blue-50/60' : ''"
                        @click="selectProposal('{{ $p['id'] }}', '{{ $p['researcher'] }}', '{{ $p['title'] }}')">
                        <td class="px-6 py-4">
                            <span class="text-[13.5px] font-bold leading-snug"
                                  :class="selectedId === '{{ $p['id'] }}' ? 'text-[#1e3a5f]' : 'text-slate-800'">
                                {{ $p['id'] }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-[13px] text-slate-600">{{ $p['researcher'] }}</td>
                        <td class="px-4 py-4 text-[13px] text-slate-500 truncate max-w-[140px]">{{ $p['title'] }}</td>
                        <td class="px-4 py-4 text-[13px] text-slate-500">{{ $p['date'] }}</td>
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
                               class="flex-1 px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 font-medium">
                        <button onclick="featureInDevelopment('Generate Nomor')"
                                class="w-10 h-10 bg-[#1e3a5f] rounded-xl flex items-center justify-center text-white hover:bg-[#162d4a] transition-colors cursor-pointer flex-shrink-0">
                            <i class="fas fa-rotate text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- Assign Ketua Board --}}
                <div>
                    <label class="block text-[10.5px] font-bold tracking-widest uppercase text-slate-400 mb-2">Assign Ketua Board</label>
                    <div class="relative">
                        <select class="w-full appearance-none px-3.5 py-2.5 text-[13.5px] border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 cursor-pointer">
                            <option>Prof. Dr. Robertus Oppenheimer</option>
                            <option>Prof. Dr. Alan Turing</option>
                            <option>Dr. Helena Vane</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                {{-- Kelola Dokumen Paket --}}
                <div>
                    <label class="block text-[10.5px] font-bold tracking-widest uppercase text-slate-400 mb-2">Kelola Dokumen Paket</label>
                    <div class="space-y-2">

                        {{-- Doc 1 --}}
                        <div class="flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-2.5">
                            <i class="fas fa-file-pdf text-red-500 text-base flex-shrink-0"></i>
                            <span class="flex-1 text-[13px] font-medium text-slate-700 truncate">Final_Proposal_v3.pdf</span>
                            <button onclick="featureInDevelopment('Preview Dokumen')"
                                    class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>

                        {{-- Doc 2 --}}
                        <div class="flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-2.5">
                            <i class="fas fa-file-lines text-blue-500 text-base flex-shrink-0"></i>
                            <span class="flex-1 text-[13px] font-medium text-slate-700 truncate">Review_Summary_Report.pdf</span>
                            <button onclick="featureInDevelopment('Preview Dokumen')"
                                    class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>

                        {{-- Upload --}}
                        <button onclick="featureInDevelopment('Upload Signed Certificate')"
                                class="w-full flex items-center gap-3 border-2 border-dashed border-slate-200 rounded-xl px-4 py-2.5 text-slate-400 hover:border-blue-300 hover:text-blue-400 hover:bg-blue-50/40 transition-all cursor-pointer">
                            <i class="fas fa-file-arrow-up text-base flex-shrink-0"></i>
                            <span class="text-[13px] font-medium">Upload Signed Certificate...</span>
                        </button>

                    </div>
                </div>

                {{-- Finalisasi Button --}}
                <button onclick="featureInDevelopment('Finalisasi & Publish Dokumen')"
                        class="w-full py-3.5 rounded-xl text-[14px] font-bold text-white flex items-center justify-center gap-2.5 transition-colors cursor-pointer"
                        style="background:#1e3a5f;">
                    <i class="fas fa-sparkles text-sm"></i>
                    Finalisasi & Publish Dokumen
                </button>
                <p class="text-[11px] text-slate-400 text-center leading-snug -mt-2">
                    By finalizing, the researcher will be notified and the certificate will be officially recorded in the institutional database.
                </p>

            </div>
        </div>

        {{-- Preview Template --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex flex-col items-center justify-center py-10 px-6 text-center">
                <div class="w-16 h-16 mb-4 flex items-center justify-center">
                    <i class="fas fa-shield-check text-slate-200 text-5xl"></i>
                </div>
                <h3 class="text-[15px] font-bold text-slate-900 mb-1">Preview Template</h3>
                <p class="text-[12.5px] text-slate-400 mb-5">Standard Institutional Ethical Certificate<br>(Rev. 2023)</p>
                <button onclick="featureInDevelopment('Open Full Preview')"
                        class="inline-flex items-center gap-2 text-[13px] font-bold text-[#1e3a5f] hover:text-[#162d4a] transition-colors cursor-pointer">
                    <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                    Open Full Preview
                </button>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function ethicalClearance() {
    return {
        selectedId:      'EC-2023-0902',
        clearanceNumber: 'ETH-2023-VII-00902',

        selectProposal(id, researcher, title) {
            this.selectedId = id;
            // Auto-generate nomor dari ID
            const parts = id.split('-');
            this.clearanceNumber = `ETH-${parts[1]}-VII-${parts[2]}`;
        },
    };
}
</script>
@endpush