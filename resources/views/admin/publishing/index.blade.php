@extends('layouts.admin')

@section('title', 'Publishing - Admin')
@section('page-title', 'Publishing')
@section('breadcrumb', 'Kelola publikasi dokumen untuk ethical clearance')

@section('content')

{{-- ═══════════════════════════════════════════
     Page Header
═══════════════════════════════════════════ --}}
<div class="flex items-start justify-between mb-6">
    {{-- Reviewers Active --}}
    <div class="flex items-center gap-2.5 bg-white border border-slate-200 rounded-xl px-3.5 py-2 shadow-sm">
        <div class="flex items-center">
            @foreach(['HV','MS','AL'] as $i => $init)
            <div class="w-7 h-7 rounded-full bg-slate-400 {{ $i > 0 ? '-ml-2' : '' }} border-2 border-white flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0">
                {{ $init }}
            </div>
            @endforeach
            <div class="-ml-2 w-7 h-7 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-slate-600 text-[9px] font-bold flex-shrink-0">+12</div>
        </div>
        <span class="text-[12.5px] font-semibold text-slate-600">Reviewers Active</span>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     Stat Cards (3 kolom)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-3 gap-5 mb-5">
    @php
    $stats = [
        ['icon'=>'fas fa-circle-check',  'iconBg'=>'bg-blue-50',   'iconColor'=>'text-blue-500',   'label'=>'TOTAL READY TO PUBLISH', 'value'=>'42'],
        ['icon'=>'fas fa-calendar-check','iconBg'=>'bg-emerald-50', 'iconColor'=>'text-emerald-500','label'=>'PUBLISHED THIS MONTH',   'value'=>'18'],
        ['icon'=>'fas fa-hourglass-half','iconBg'=>'bg-orange-50',  'iconColor'=>'text-orange-400', 'label'=>'PENDING VERIFICATION',   'value'=>'05'],
    ];
    @endphp
    @foreach($stats as $s)
    <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 {{ $s['iconBg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="{{ $s['icon'] }} {{ $s['iconColor'] }} text-lg"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">{{ $s['label'] }}</p>
            <p class="text-[28px] font-bold text-slate-900 leading-none tracking-tight">{{ $s['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════
     Bulk Actions Banner
═══════════════════════════════════════════ --}}
<div class="rounded-2xl p-5 mb-5 flex items-center justify-between" style="background: #1e3a5f;"
     x-data="{ selected: 0 }">
    <div>
        <h3 class="text-[15px] font-bold text-white mb-1">Bulk Actions & Registry Release</h3>
        <p class="text-[12.5px] text-blue-200/80 leading-relaxed max-w-lg">
            Select multiple clearance certificates from the queue to publish them to the public registry simultaneously.
        </p>
    </div>
    <div class="flex items-center gap-4 flex-shrink-0">
        <div class="text-right">
            <p class="text-[9.5px] font-bold tracking-widest uppercase text-blue-300/70 mb-0.5">Selection Mode</p>
            <p class="text-[13px] font-bold text-white"><span x-text="selected">0</span> items selected</p>
        </div>
        <button onclick="featureInDevelopment('Publish Selected Certificates')"
                class="inline-flex items-center gap-2 bg-emerald-400 hover:bg-emerald-300 text-slate-900 text-[13px] font-bold px-5 py-2.5 rounded-xl transition-colors cursor-pointer">
            <i class="fas fa-cloud-arrow-up text-sm"></i>
            Publish Selected Certificates
        </button>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     Certificates Ready for Publishing
═══════════════════════════════════════════ --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-5"
     x-data="bulkSelect()">

    {{-- Table Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <i class="fas fa-file-certificate text-blue-500 text-base"></i>
            <span class="text-[15px] font-bold text-slate-900">Certificates Ready for Publishing</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="featureInDevelopment('Filter')"
                    class="w-8 h-8 border border-slate-200 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-sliders text-xs"></i>
            </button>
            <button onclick="featureInDevelopment('Refresh')"
                    class="w-8 h-8 border border-slate-200 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-rotate text-xs"></i>
            </button>
        </div>
    </div>

    {{-- Table --}}
    <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-6 py-3 w-10">
                    <input type="checkbox" @change="toggleAll($event)"
                           class="w-4 h-4 rounded border-slate-300 text-[#1e3a5f] cursor-pointer accent-[#1e3a5f]">
                </th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Proposal ID</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Researcher</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Clearance Number</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Decision Date</th>
                <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3">Public Status</th>
                <th class="text-right text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @php
            $certs = [
                ['id'=>'EC-2023-0891','initials'=>'HV','color'=>'bg-slate-400','researcher'=>'Dr. Helena Vane',   'clearance'=>'ETH-2023-VII-00902', 'date'=>'Oct 12, 2023','status'=>'READY',   'statusClass'=>'bg-emerald-50 text-emerald-700'],
                ['id'=>'EC-2023-0942','initials'=>'MS','color'=>'bg-blue-500', 'researcher'=>'Marcus Sterling',   'clearance'=>'ETH-2023-IX-11204',  'date'=>'Oct 15, 2023','status'=>'PRIVATE', 'statusClass'=>'bg-slate-100 text-slate-600'],
                ['id'=>'EC-2023-1102','initials'=>'AL','color'=>'bg-slate-400','researcher'=>'Prof. Anita Lowe',  'clearance'=>'ETH-2023-XI-00431',  'date'=>'Oct 18, 2023','status'=>'READY',   'statusClass'=>'bg-emerald-50 text-emerald-700'],
                ['id'=>'EC-2023-1125','initials'=>'JD','color'=>'bg-slate-300','researcher'=>'John Doe',          'clearance'=>'ETH-2023-X-08812',   'date'=>'Oct 20, 2023','status'=>'READY',   'statusClass'=>'bg-emerald-50 text-emerald-700'],
            ];
            @endphp

            @foreach($certs as $i => $c)
            <tr class="hover:bg-slate-50/60 transition-colors" :class="checked[{{ $i }}] ? 'bg-blue-50/40' : ''">
                <td class="px-6 py-4">
                    <input type="checkbox" x-model="checked[{{ $i }}]"
                           class="w-4 h-4 rounded border-slate-300 cursor-pointer accent-[#1e3a5f]">
                </td>
                <td class="px-4 py-4">
                    <span class="text-[13.5px] font-bold text-[#1e3a5f]">{{ $c['id'] }}</span>
                </td>
                <td class="px-4 py-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full {{ $c['color'] }} flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0">{{ $c['initials'] }}</div>
                        <span class="text-[13px] font-medium text-slate-700">{{ $c['researcher'] }}</span>
                    </div>
                </td>
                <td class="px-4 py-4 text-[13px] text-slate-600 font-medium">{{ $c['clearance'] }}</td>
                <td class="px-4 py-4 text-[13px] text-slate-500">{{ $c['date'] }}</td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wide uppercase {{ $c['statusClass'] }}">{{ $c['status'] }}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="featureInDevelopment('Preview Sertifikat')"
                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer" title="Preview">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                        <button onclick="featureInDevelopment('Export')"
                                class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer" title="Export">
                            <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                        </button>
                        <button onclick="featureInDevelopment('Publish')"
                                class="inline-flex items-center gap-1.5 bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-[12px] font-semibold px-3.5 py-1.5 rounded-lg transition-colors cursor-pointer">
                            Publish
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
        <span class="text-[12.5px] text-slate-400">Showing 4 of 42 ready certificates</span>
        <div class="flex items-center gap-1">
            <button onclick="featureInDevelopment('Previous')"
                    class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors cursor-pointer">
                <i class="fas fa-chevron-left text-xs"></i>
            </button>
            <button class="w-8 h-8 flex items-center justify-center bg-[#1e3a5f] text-white text-[13px] font-semibold rounded-lg">1</button>
            <button onclick="featureInDevelopment('Page 2')"
                    class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-[13px] text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">2</button>
            <button onclick="featureInDevelopment('Page 3')"
                    class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-[13px] text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">3</button>
            <button onclick="featureInDevelopment('Next')"
                    class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors cursor-pointer">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     ROW BAWAH: Publishing Guidelines (kiri) + Security Verified (kanan)
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-2 gap-5">

    {{-- Publishing Guidelines --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h3 class="text-[15px] font-bold text-slate-900 mb-3">Publishing Guidelines</h3>
        <p class="text-[13px] text-slate-500 leading-relaxed mb-5">
            Review all metadata before confirming publication. Once a certificate is published to the public registry, it can only be modified through an official ethical amendment process.
        </p>
        <ul class="space-y-2.5 mb-5">
            @foreach(['Verify researcher\'s credentials and institutional affiliation.','Ensure the PDF document contains the valid university seal and chair signature.'] as $item)
            <li class="flex items-start gap-2.5 text-[13px] text-slate-600">
                <i class="fas fa-circle-check text-emerald-500 text-sm mt-0.5 flex-shrink-0"></i>
                {{ $item }}
            </li>
            @endforeach
        </ul>
        <button onclick="featureInDevelopment('Download Policy Handbook')"
                class="inline-flex items-center gap-2 text-[13px] font-bold text-[#1e3a5f] hover:text-[#162d4a] transition-colors cursor-pointer">
            Download Policy Handbook (PDF)
            <i class="fas fa-arrow-up-right-from-square text-xs"></i>
        </button>
    </div>

    {{-- Security Verified --}}
    <div class="rounded-2xl p-6 flex flex-col items-start justify-between" style="background:#1e3a5f;">
        <div>
            <h3 class="text-[15px] font-bold text-white mb-2">Security Verified</h3>
            <p class="text-[13px] text-blue-200/80 leading-relaxed">
                All pending certificates have passed the automated compliance verification for data privacy and PI protection.
            </p>
        </div>
        <div class="mt-6 w-full">
            <div class="inline-flex items-center gap-2.5 bg-white/10 border border-white/20 rounded-xl px-4 py-2.5">
                <i class="fas fa-shield-halved text-blue-300 text-base"></i>
                <span class="text-[12px] font-bold tracking-widest uppercase text-blue-200">System Encrypted</span>
            </div>
        </div>
        {{-- Decorative shield --}}
        <div class="absolute opacity-5 right-8 bottom-4 pointer-events-none select-none" aria-hidden="true">
            <i class="fas fa-shield-halved text-white" style="font-size:100px;"></i>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function bulkSelect() {
    return {
        checked: { 0: false, 1: false, 2: false, 3: false },
        toggleAll(e) {
            const val = e.target.checked;
            Object.keys(this.checked).forEach(k => this.checked[k] = val);
        },
    };
}
</script>
@endpush