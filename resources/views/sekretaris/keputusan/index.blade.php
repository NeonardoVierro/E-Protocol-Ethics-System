@extends('layouts.sekretaris')

@section('title', 'Keputusan Proposal')
@section('page-title', 'Keputusan')
@section('breadcrumb', 'Approve / Revise / Reject Proposal')

@section('content')

{{-- Flash --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl mb-5 text-[13.5px] font-medium shadow-sm">
    <i class="fas fa-circle-check text-emerald-500"></i>
    {{ session('success') }}
    <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600 cursor-pointer">
        <i class="fas fa-xmark"></i>
    </button>
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl mb-5 text-[13.5px] font-medium shadow-sm">
    <i class="fas fa-circle-xmark text-red-500"></i>
    {{ session('error') }}
    <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600 cursor-pointer">
        <i class="fas fa-xmark"></i>
    </button>
</div>
@endif

{{-- Page Header --}}
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Keputusan Proposal</h2>
    <p class="text-sm text-slate-500 mt-1">Tentukan keputusan akhir berdasarkan hasil review para reviewer.</p>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    @php
    $total    = $keputusan->count();
    $approved = $keputusan->where('status', 'approved')->count();
    $rejected = $keputusan->where('status', 'rejected')->count();
    $pending  = $keputusan->where('status', 'on_review')->count();
    @endphp
    @foreach([
        ['icon'=>'fas fa-file-lines',   'bg'=>'bg-slate-100',   'color'=>'text-slate-500',  'label'=>'Total Proposal',        'val'=>$total],
        ['icon'=>'fas fa-circle-check', 'bg'=>'bg-emerald-50', 'color'=>'text-emerald-500','label'=>'Approved',              'val'=>$approved],
        ['icon'=>'fas fa-hourglass-half','bg'=>'bg-amber-50',  'color'=>'text-amber-500',  'label'=>'Menunggu Keputusan',    'val'=>$pending],
        ['icon'=>'fas fa-circle-xmark', 'bg'=>'bg-red-50',    'color'=>'text-red-500',    'label'=>'Rejected',              'val'=>$rejected],
    ] as $s)
    <div class="bg-white border border-slate-200 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
        <div class="w-10 h-10 {{ $s['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="{{ $s['icon'] }} {{ $s['color'] }} text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-0.5">{{ $s['label'] }}</p>
            <p class="text-[20px] font-bold text-slate-900 leading-none">{{ $s['val'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Tabel --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden"
     x-data="keputusanManager()"
     x-init="init()">

    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <span class="text-[15px] font-bold text-slate-900">Daftar Proposal</span>
        <div class="relative">
            <select x-model="filterStatus"
                    class="appearance-none pl-3 pr-8 py-1.5 text-[13px] border border-slate-200 rounded-lg bg-white outline-none focus:border-blue-400 text-slate-700 cursor-pointer">
                <option value="">Semua Status</option>
                <option value="on_review">On Review</option>
                <option value="approved">Approved</option>
                <option value="revised">Revisi</option>
                <option value="rejected">Rejected</option>
            </select>
            <i class="fas fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3 w-[38%]">Proposal</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[14%]">Status</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[16%]">Tanggal Submit</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[14%]">Tanggal Keputusan</th>
                    <th class="text-right text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3 w-[18%]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($keputusan as $proposal)
                @php
                $statusConfig = [
                    'on_review'           => ['bg-yellow-50 text-yellow-700', 'On Review'],
                    'revised'             => ['bg-orange-50 text-orange-700', 'Revisi'],
                    'approved'            => ['bg-emerald-50 text-emerald-700','Approved'],
                    'rejected'            => ['bg-red-50 text-red-600',       'Rejected'],
                    'waiting_for_publish' => ['bg-purple-50 text-purple-700', 'Waiting For Publish'],
                    'published'           => ['bg-teal-50 text-teal-700',     'Published'],
                ];
                [$badge, $label] = $statusConfig[$proposal->status] ?? ['bg-slate-100 text-slate-600', $proposal->status];
                $sudahDiputuskan = $proposal->decision_date !== null;
                @endphp
                <tr class="hover:bg-slate-50/60 transition-colors"
                    :class="'{{ $proposal->id }}' == highlightId ? 'bg-blue-50/40 ring-2 ring-blue-200 ring-inset' : ''"
                    x-show="!filterStatus || filterStatus === '{{ $proposal->status }}'">

                    {{-- Proposal --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="text-[13.5px] font-semibold text-slate-800 leading-snug">{{ $proposal->title }}</div>
                            @if($proposal->revisions_count > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-semibold">
                                Vol.{{ $proposal->revisions_count + 1 }}
                            </span>
                            @endif
                        </div>
                        <div class="text-[11.5px] text-slate-400 mt-0.5">
                            {{ $proposal->researcher->name ?? '-' }}
                            @if($proposal->researcher?->email)
                            · {{ $proposal->researcher->email }}
                            @endif
                        </div>
                        @if($proposal->rejection_reason)
                        <div class="text-[11px] text-red-400 mt-1 italic">
                            <i class="fas fa-circle-info text-[9px]"></i>
                            {{ Str::limit($proposal->rejection_reason, 60) }}
                        </div>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10.5px] font-bold tracking-wide {{ $badge }}">
                            {{ $label }}
                        </span>
                    </td>

                    {{-- Tanggal Submit --}}
                    <td class="px-4 py-4 text-[13px] text-slate-500">
                        {{ $proposal->submission_date?->format('d M Y') ?? '-' }}
                    </td>

                    {{-- Tanggal Keputusan --}}
                    <td class="px-4 py-4 text-[13px] text-slate-500">
                        {{ $proposal->decision_date?->format('d M Y') ?? '-' }}
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4 text-right">
                        @if($sudahDiputuskan)
                            <div class="flex items-center justify-end gap-2">
                                <span class="text-[12px] text-slate-400 italic">Sudah diputuskan</span>
                                <a href="{{ route('sekretaris.hasil-review.show', $proposal->id) }}"
                                   class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" title="Lihat Hasil Review">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        @else
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('sekretaris.hasil-review.show', $proposal->id) }}"
                                   class="w-7 h-7 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" title="Lihat Hasil Review">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <button @click="openModal(
                                            {{ $proposal->id }},
                                            '{{ addslashes($proposal->title) }}',
                                            '{{ $proposal->researcher->name ?? '' }}'
                                        )"
                                        class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-white bg-[#1e3a5f] hover:bg-[#162d4a] px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                                    <i class="fas fa-gavel text-[10px]"></i> Putuskan
                                </button>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <i class="fas fa-inbox text-slate-300 text-4xl mb-3 block"></i>
                        <p class="text-[14px] text-slate-400 font-medium">Tidak ada proposal yang perlu diputuskan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══════════════════════════════════════
         MODAL: Beri Keputusan
    ══════════════════════════════════════ --}}
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         @keydown.escape.window="modalOpen = false">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="modalOpen = false"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="text-[16px] font-bold text-slate-900">Beri Keputusan</h3>
                    <p class="text-[12px] text-slate-400 mt-0.5 truncate max-w-[280px]" x-text="modalTitle"></p>
                </div>
                <button @click="modalOpen = false"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 cursor-pointer">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Nama peneliti --}}
            <div class="px-6 pt-4">
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5">
                    <i class="fas fa-user text-slate-400 text-xs"></i>
                    <span class="text-[13px] text-slate-600" x-text="modalResearcher"></span>
                </div>
            </div>

            {{-- Body --}}
            <form :action="'{{ url('sekretaris/keputusan/update') }}'" method="POST">
                @csrf
                <input type="hidden" name="proposal_id" :value="modalProposalId">

                <div class="px-6 py-5 space-y-4">

                    {{-- Pilih keputusan --}}
                    <div>
                        <label class="block text-[11.5px] font-semibold text-slate-500 mb-2">
                            Keputusan <span class="text-red-400">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach([
                                ['val'=>'approved','icon'=>'fa-check',  'activeB'=>'border-emerald-500 bg-emerald-50','activeI'=>'bg-emerald-500','activeT'=>'text-emerald-700','label'=>'Approve'],
                                ['val'=>'revised', 'icon'=>'fa-pen',    'activeB'=>'border-amber-500 bg-amber-50',   'activeI'=>'bg-amber-500',  'activeT'=>'text-amber-700',  'label'=>'Revisi'],
                                ['val'=>'rejected','icon'=>'fa-xmark',  'activeB'=>'border-red-500 bg-red-50',       'activeI'=>'bg-red-500',    'activeT'=>'text-red-700',    'label'=>'Reject'],
                            ] as $opt)
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="{{ $opt['val'] }}"
                                       x-model="selectedStatus" class="sr-only">
                                <div class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-all text-center"
                                     :class="selectedStatus === '{{ $opt['val'] }}'
                                         ? '{{ $opt['activeB'] }}'
                                         : 'border-slate-200 hover:border-slate-300'">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center transition-colors"
                                         :class="selectedStatus === '{{ $opt['val'] }}' ? '{{ $opt['activeI'] }}' : 'bg-slate-100'">
                                        <i class="fas {{ $opt['icon'] }} text-sm"
                                           :class="selectedStatus === '{{ $opt['val'] }}' ? 'text-white' : 'text-slate-400'"></i>
                                    </div>
                                    <span class="text-[12px] font-semibold transition-colors"
                                          :class="selectedStatus === '{{ $opt['val'] }}' ? '{{ $opt['activeT'] }}' : 'text-slate-600'">
                                        {{ $opt['label'] }}
                                    </span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Textarea alasan (revisi/reject) --}}
                    <div x-show="selectedStatus === 'rejected' || selectedStatus === 'revised'"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5">
                            <span x-text="selectedStatus === 'rejected' ? 'Alasan Penolakan' : 'Catatan Revisi'"></span>
                            <span class="text-red-400">*</span>
                        </label>
                        <textarea name="rejection_reason" x-model="alasan" rows="3"
                                  :placeholder="selectedStatus === 'rejected'
                                      ? 'Jelaskan alasan penolakan proposal ini...'
                                      : 'Jelaskan poin-poin yang perlu direvisi...'"
                                  class="w-full px-3.5 py-2.5 text-[13px] border border-slate-200 rounded-xl outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all text-slate-700 placeholder-slate-400 resize-none"></textarea>
                    </div>

                    {{-- Info approve --}}
                    <div x-show="selectedStatus === 'approved'"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="flex items-start gap-2.5 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
                        <i class="fas fa-circle-info text-emerald-500 text-sm flex-shrink-0 mt-0.5"></i>
                        <p class="text-[12.5px] text-emerald-700 leading-relaxed">
                            Proposal akan disetujui dan <strong>draft Ethical Clearance</strong> akan otomatis dibuat untuk ditindaklanjuti.
                        </p>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/60">
                    <button type="button" @click="modalOpen = false"
                            class="px-5 py-2.5 text-[13.5px] font-semibold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="!selectedStatus || ((selectedStatus === 'rejected' || selectedStatus === 'revised') && !alasan.trim())"
                            class="inline-flex items-center gap-2 text-[13.5px] font-semibold px-6 py-2.5 rounded-xl transition-colors"
                            :class="(selectedStatus && (selectedStatus === 'approved' || alasan.trim()))
                                ? 'bg-[#1e3a5f] hover:bg-[#162d4a] text-white cursor-pointer'
                                : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                        <i class="fas fa-gavel text-xs"></i>
                        Simpan Keputusan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function keputusanManager() {
    return {
        modalOpen:        false,
        modalProposalId:  null,
        modalTitle:       '',
        modalResearcher:  '',
        selectedStatus:   '',
        alasan:           '',
        filterStatus:     '',
        highlightId:      null,

        // Auto-open modal jika dari "Lanjut ke Keputusan"
        init() {
            @if($autoOpenProposalId && $autoOpenProposal)
            this.$nextTick(() => {
                this.highlightId = '{{ $autoOpenProposalId }}';
                // Scroll ke baris yang di-highlight
                setTimeout(() => {
                    const row = document.querySelector(`[x-data] tr[x-bind\\:class*="{{ $autoOpenProposalId }}"]`);
                    if (row) row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Buka modal otomatis
                    this.openModal(
                        {{ $autoOpenProposal->id }},
                        '{{ addslashes($autoOpenProposal->title) }}',
                        '{{ addslashes($autoOpenProposal->researcher->name ?? '') }}'
                    );
                }, 400);
            });
            @endif
        },

        openModal(id, title, researcher) {
            this.modalProposalId = id;
            this.modalTitle      = title;
            this.modalResearcher = researcher;
            this.selectedStatus  = '';
            this.alasan          = '';
            this.modalOpen       = true;
        },
    };
}
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush