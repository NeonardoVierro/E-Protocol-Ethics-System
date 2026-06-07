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

{{-- Stat Cards --}}
<div class="grid grid-cols-3 gap-5 mb-6">
    @php
    $total    = $keputusan->count();
    $approved = $keputusan->where('status', 'approved')->count();
    $rejected = $keputusan->where('status', 'rejected')->count();
    $pending  = $keputusan->whereNotIn('status', ['approved','rejected'])->count();
    @endphp
    <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-file-lines text-slate-500 text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Total Proposal</p>
            <p class="text-[22px] font-bold text-slate-900 leading-none">{{ $total }}</p>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-circle-check text-emerald-500 text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Approved</p>
            <p class="text-[22px] font-bold text-slate-900 leading-none">{{ $approved }}</p>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-hourglass-half text-amber-500 text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Menunggu Keputusan</p>
            <p class="text-[22px] font-bold text-slate-900 leading-none">{{ $pending }}</p>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden"
     x-data="keputusanManager()">

    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <span class="text-[15px] font-bold text-slate-900">Daftar Proposal</span>
        {{-- Filter status --}}
        <div class="flex items-center gap-2">
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
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3 w-[35%]">Proposal</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[15%]">Status</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[15%]">Tanggal Submit</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[15%]">Keputusan Sebelumnya</th>
                    <th class="text-right text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3 w-[20%]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($keputusan as $proposal)
                @php
                $statusConfig = [
                    'new_proposal'        => ['bg-blue-50 text-blue-700',    'New Proposal'],
                    'in_process'          => ['bg-cyan-50 text-cyan-700',    'In Process'],
                    'on_review'           => ['bg-yellow-50 text-yellow-700','On Review'],
                    'revised'             => ['bg-orange-50 text-orange-700','Revisi'],
                    'approved'            => ['bg-emerald-50 text-emerald-700','Approved'],
                    'rejected'            => ['bg-red-50 text-red-600',      'Rejected'],
                    'waiting_for_publish' => ['bg-purple-50 text-purple-700','Waiting For Publish'],
                    'published'           => ['bg-teal-50 text-teal-700',    'Published'],
                ];
                [$badge, $label] = $statusConfig[$proposal->status] ?? ['bg-slate-100 text-slate-600', $proposal->status];
                @endphp
                <tr class="hover:bg-slate-50/60 transition-colors"
                    x-show="!filterStatus || filterStatus === '{{ $proposal->status }}'">

                    {{-- Proposal info --}}
                    <td class="px-6 py-4">
                        <div class="text-[13.5px] font-semibold text-slate-800 leading-snug">
                            {{ $proposal->title }}
                        </div>
                        <div class="text-[11.5px] text-slate-400 mt-0.5">
                            {{ $proposal->researcher->name ?? '-' }}
                            @if($proposal->researcher?->email)
                            · <span class="text-slate-300">{{ $proposal->researcher->email }}</span>
                            @endif
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10.5px] font-bold tracking-wide {{ $badge }}">
                            {{ $label }}
                        </span>
                    </td>

                    {{-- Tanggal --}}
                    <td class="px-4 py-4 text-[13px] text-slate-500">
                        {{ $proposal->submission_date?->format('d M Y') ?? '-' }}
                    </td>

                    {{-- Keputusan sebelumnya --}}
                    <td class="px-4 py-4">
                        @if($proposal->status === 'revised')
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-amber-50 border border-amber-200 rounded-lg text-[11.5px] font-semibold text-amber-700">
                            <i class="fas fa-hourglass-half text-amber-500 text-[10px]"></i>
                            Sedang tahap revisi
                        </div>
                        @elseif($proposal->decision_date)
                        <div class="text-[12px] text-slate-500">
                            {{ $proposal->decision_date->format('d M Y') }}
                        </div>
                        @else
                        <span class="text-[12px] text-slate-300 italic">Belum ada</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4 text-right">
                        @if(in_array($proposal->status, ['approved','rejected','published','waiting_for_publish']))
                            <span class="text-[12px] text-slate-400 italic">Sudah diputuskan</span>
                        @elseif($proposal->status === 'revised')
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-[12px] text-slate-400 italic">Menunggu revisi</span>
                                <span class="text-[10px] text-slate-300">dari peneliti</span>
                            </div>
                        @else
                            <button @click="openModal(
                                        {{ $proposal->id }},
                                        '{{ addslashes($proposal->title) }}',
                                        '{{ $proposal->status }}'
                                    )"
                                    class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-white bg-[#1e3a5f] hover:bg-[#162d4a] px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                                <i class="fas fa-gavel text-[10px]"></i> Beri Keputusan
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <i class="fas fa-inbox text-slate-300 text-4xl mb-3 block"></i>
                        <p class="text-[14px] text-slate-400 font-medium">Tidak ada proposal untuk diputuskan.</p>
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
                    <p class="text-[12px] text-slate-400 mt-0.5 truncate max-w-xs" x-text="modalTitle"></p>
                </div>
                <button @click="modalOpen = false"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 cursor-pointer">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Body --}}
            <form :action="'{{ route('sekretaris.keputusan.update') }}'" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="proposal_id" :value="modalProposalId">

                <div class="px-6 py-5 space-y-4">

                    {{-- Pilih keputusan --}}
                    <div>
                        <label class="block text-[11.5px] font-semibold text-slate-500 mb-2">
                            Keputusan <span class="text-red-400">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            {{-- Approved --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="approved"
                                       x-model="selectedStatus" class="sr-only">
                                <div class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-all text-center"
                                     :class="selectedStatus === 'approved'
                                         ? 'border-emerald-500 bg-emerald-50'
                                         : 'border-slate-200 hover:border-slate-300'">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center"
                                         :class="selectedStatus === 'approved' ? 'bg-emerald-500' : 'bg-slate-100'">
                                        <i class="fas fa-check text-sm"
                                           :class="selectedStatus === 'approved' ? 'text-white' : 'text-slate-400'"></i>
                                    </div>
                                    <span class="text-[12px] font-semibold"
                                          :class="selectedStatus === 'approved' ? 'text-emerald-700' : 'text-slate-600'">
                                        Approve
                                    </span>
                                </div>
                            </label>

                            {{-- Revised --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="revised"
                                       x-model="selectedStatus" class="sr-only">
                                <div class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-all text-center"
                                     :class="selectedStatus === 'revised'
                                         ? 'border-amber-500 bg-amber-50'
                                         : 'border-slate-200 hover:border-slate-300'">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center"
                                         :class="selectedStatus === 'revised' ? 'bg-amber-500' : 'bg-slate-100'">
                                        <i class="fas fa-pen text-sm"
                                           :class="selectedStatus === 'revised' ? 'text-white' : 'text-slate-400'"></i>
                                    </div>
                                    <span class="text-[12px] font-semibold"
                                          :class="selectedStatus === 'revised' ? 'text-amber-700' : 'text-slate-600'">
                                        Revisi
                                    </span>
                                </div>
                            </label>

                            {{-- Rejected --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="rejected"
                                       x-model="selectedStatus" class="sr-only">
                                <div class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-all text-center"
                                     :class="selectedStatus === 'rejected'
                                         ? 'border-red-500 bg-red-50'
                                         : 'border-slate-200 hover:border-slate-300'">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center"
                                         :class="selectedStatus === 'rejected' ? 'bg-red-500' : 'bg-slate-100'">
                                        <i class="fas fa-xmark text-sm"
                                           :class="selectedStatus === 'rejected' ? 'text-white' : 'text-slate-400'"></i>
                                    </div>
                                    <span class="text-[12px] font-semibold"
                                          :class="selectedStatus === 'rejected' ? 'text-red-700' : 'text-slate-600'">
                                        Reject
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Catatan/alasan (wajib jika reject/revisi) --}}
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

                        {{-- File lampiran (opsional untuk revisi) --}}
                        <div x-show="selectedStatus === 'revised'"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5">
                                Lampiran Revisi (opsional)
                            </label>
                            <input type="file" name="revision_files[]" multiple accept=".pdf,.doc,.docx"
                                   class="w-full text-sm text-slate-700 file:px-3 file:py-2 file:border file:rounded file:border-slate-200 file:bg-white" />
                            <p class="text-[12px] text-slate-400 mt-1">Anda dapat menyertakan file PDF/Word hasil review reviewer atau dokumen revisi.</p>
                        </div>
                    </div>

                    {{-- Info approve --}}
                    <div x-show="selectedStatus === 'approved'"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="flex items-start gap-2.5 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
                        <i class="fas fa-circle-info text-emerald-500 text-sm flex-shrink-0 mt-0.5"></i>
                        <p class="text-[12.5px] text-emerald-700 leading-relaxed">
                            Proposal akan disetujui dan diteruskan ke admin untuk penomoran dan penunjukan ketua.
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
                            :disabled="!selectedStatus || (selectedStatus === 'rejected' && !alasan.trim())"
                            class="inline-flex items-center gap-2 text-[13.5px] font-semibold px-6 py-2.5 rounded-xl transition-colors"
                            :class="(selectedStatus && (selectedStatus === 'approved' || selectedStatus === 'revised' || (selectedStatus === 'rejected' && alasan.trim())))
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
        modalOpen:       false,
        modalProposalId: null,
        modalTitle:      '',
        selectedStatus:  '',
        alasan:          '',
        filterStatus:    '',

        openModal(id, title, currentStatus) {
            this.modalProposalId = id;
            this.modalTitle      = title;
            this.selectedStatus  = '';
            this.alasan          = '';
            this.modalOpen       = true;
        },
    };
}
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush