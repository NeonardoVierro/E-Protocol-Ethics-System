@extends('layouts.admin')

@section('title', 'Proposal Assignment - Admin')
@section('page-title', 'Proposal Assignment')
@section('breadcrumb', 'Kelola proposal assignment untuk ethical clearance')

@section('content')

{{-- Flash --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl mb-5 text-[13.5px] font-medium shadow-sm">
    <i class="fas fa-circle-check text-emerald-500"></i> {{ session('success') }}
    <button @click="show = false" class="ml-auto cursor-pointer text-emerald-400"><i class="fas fa-xmark"></i></button>
</div>
@endif

{{-- Page Header --}}
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Proposal Assignment</h2>
    <p class="text-sm text-slate-500 mt-1">Tetapkan sekretaris dan ketua untuk setiap proposal yang masuk.</p>
</div>

{{-- Tabel --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden"
     x-data="proposalAssignment()">

    {{-- Header tabel --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <span class="text-[15px] font-bold text-slate-900">Daftar Proposal</span>
        <span class="text-[12.5px] text-slate-400">{{ $proposals->total() }} proposal</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px]">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-6 py-3 w-[40%]">Proposal</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[20%]">Status</th>
                    <th class="text-left text-[10px] font-bold tracking-wider uppercase text-slate-400 px-4 py-3 w-[40%]">Sekretaris</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($proposals as $proposal)
                @php
                    // Cek assignment yang sudah ada
                    $assignSekre = $proposal->assignments->where('role','sekretaris')->sortByDesc('created_at')->first();

                    $sekreSudahDipilih = $assignSekre !== null;
                    $sekreSudahDikirim = $assignSekre && $assignSekre->sent_at;
                @endphp
                <tr class="hover:bg-slate-50/60 transition-colors">

                    {{-- Kolom 1: Judul + email peneliti --}}
                    <td class="px-6 py-4">
                        <div class="text-[13.5px] font-semibold text-slate-800 leading-snug line-clamp-2">
                            {{ $proposal->title }}
                        </div>
                        <div class="text-[11.5px] text-slate-400 mt-0.5">
                            {{ $proposal->researcher->email ?? '-' }}
                        </div>
                        <div class="text-[11px] text-slate-300 mt-0.5">
                            {{ $proposal->submission_date?->format('d M Y') ?? '-' }}
                        </div>
                    </td>

                    {{-- Kolom 2: Status --}}
                    <td class="px-4 py-4">
                        @php
                        $statusConfig = [
                            'new_proposal'        => ['bg-blue-50 text-blue-700',   'New Proposal'],
                            'in_process'          => ['bg-cyan-50 text-cyan-700',   'In Process'],
                            'on_review'           => ['bg-yellow-50 text-yellow-700','On Review'],
                            'revised'             => ['bg-orange-50 text-orange-700','Revisi'],
                            'approved'            => ['bg-green-50 text-green-700',  'Approved'],
                            'rejected'            => ['bg-red-50 text-red-600',      'Rejected'],
                            'waiting_for_publish' => ['bg-purple-50 text-purple-700','Waiting For Publish'],
                            'published'           => ['bg-teal-50 text-teal-700',    'Published'],
                        ];
                        [$badge, $label] = $statusConfig[$proposal->status] ?? ['bg-slate-100 text-slate-600', $proposal->status];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10.5px] font-bold tracking-wide {{ $badge }}">
                            {{ $label }}
                        </span>
                    </td>

                    {{-- Kolom 3: Sekretaris --}}
                    <td class="px-4 py-4">
                        @if($sekreSudahDikirim)
                            {{-- Sudah dikirim: tampilkan nama --}}
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-emerald-600 flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0">
                                    {{ strtoupper(substr($assignSekre->assignedTo->name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-[12.5px] font-semibold text-slate-700">{{ $assignSekre->assignedTo->name ?? '-' }}</div>
                                    <div class="text-[10.5px] text-emerald-600 flex items-center gap-1">
                                        <i class="fas fa-check text-[9px]"></i> Terkirim
                                    </div>
                                </div>
                            </div>
                        @elseif($sekreSudahDipilih)
                            {{-- Dipilih tapi belum dikirim --}}
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-amber-500 flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0">
                                    {{ strtoupper(substr($assignSekre->assignedTo->name ?? 'S', 0, 1)) }}
                                </div>
                                <div class="text-[12.5px] font-semibold text-slate-700">{{ $assignSekre->assignedTo->name ?? '-' }}</div>
                                <button onclick="gantiSekretaris({{ $proposal->id }})"
                                        class="text-[11px] text-slate-400 hover:text-slate-600 underline cursor-pointer ml-auto">Ganti</button>
                                <button onclick="kirimSekretaris({{ $proposal->id }}, this)"
                                        class="inline-flex items-center gap-1 text-[11.5px] font-semibold text-white bg-[#1e3a5f] hover:bg-[#162d4a] px-2.5 py-1 rounded-lg cursor-pointer transition-colors">
                                    <i class="fas fa-paper-plane text-[9px]"></i> Kirim
                                </button>
                            </div>
                        @else
                            {{-- Belum dipilih --}}
                            <button @click="openSekretarisModal({{ $proposal->id }})"
                                    class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-[#1e3a5f] border border-[#1e3a5f] hover:bg-[#1e3a5f] hover:text-white px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                                <i class="fas fa-user-plus text-[10px]"></i> Pilih Sekretaris
                            </button>
                        @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-16 text-center">
                        <i class="fas fa-inbox text-slate-300 text-4xl mb-3 block"></i>
                        <p class="text-[14px] text-slate-400 font-medium">Belum ada proposal masuk</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($proposals->hasPages())
    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
        <span class="text-[12.5px] text-slate-400">
            Showing {{ $proposals->firstItem() }}–{{ $proposals->lastItem() }} of {{ $proposals->total() }}
        </span>
        <div class="flex items-center gap-1">
            @if($proposals->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-300 cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i></span>
            @else
                <a href="{{ $proposals->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors"><i class="fas fa-chevron-left text-xs"></i></a>
            @endif
            @foreach($proposals->getUrlRange(1, $proposals->lastPage()) as $page => $url)
                @if($page == $proposals->currentPage())
                    <span class="w-8 h-8 flex items-center justify-center bg-[#1e3a5f] text-white text-[13px] font-semibold rounded-lg">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-[13px] text-slate-600 hover:bg-slate-50 transition-colors">{{ $page }}</a>
                @endif
            @endforeach
            @if($proposals->hasMorePages())
                <a href="{{ $proposals->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-50 transition-colors"><i class="fas fa-chevron-right text-xs"></i></a>
            @else
                <span class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-300 cursor-not-allowed"><i class="fas fa-chevron-right text-xs"></i></span>
            @endif
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         MODAL: Pilih Sekretaris
    ══════════════════════════════════════ --}}
    <div x-show="sekretarisModalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         @keydown.escape.window="sekretarisModalOpen = false">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="sekretarisModalOpen = false"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="text-[16px] font-bold text-slate-900">Pilih Sekretaris</h3>
                <button @click="sekretarisModalOpen = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 cursor-pointer">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="px-6 py-4 max-h-[50vh] overflow-y-auto">
                {{-- Loading --}}
                <div x-show="loadingSekre" class="py-8 text-center">
                    <i class="fas fa-spinner fa-spin text-slate-400 text-2xl"></i>
                    <p class="text-[13px] text-slate-400 mt-2">Memuat daftar sekretaris...</p>
                </div>

                {{-- List --}}
                <div x-show="!loadingSekre" class="space-y-2">
                    <template x-for="s in sekretarisList" :key="s.id">
                        <div @click="selectedSekretaris = s.id"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all"
                             :class="selectedSekretaris === s.id ? 'border-[#1e3a5f] bg-blue-50' : 'border-slate-200 hover:border-slate-300'">
                            <div class="w-9 h-9 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 x-text="s.name.charAt(0).toUpperCase()"></div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[13.5px] font-semibold text-slate-800" x-text="s.name"></div>
                                <div class="text-[11.5px] text-slate-400" x-text="s.email"></div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="text-[11px] font-bold text-slate-500" x-text="s.active_proposals_count + ' proposal'"></div>
                                <div class="text-[10px] text-slate-400">aktif</div>
                            </div>
                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                                 :class="selectedSekretaris === s.id ? 'border-[#1e3a5f] bg-[#1e3a5f]' : 'border-slate-300'">
                                <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedSekretaris === s.id"></div>
                            </div>
                        </div>
                    </template>
                    <p x-show="sekretarisList.length === 0 && !loadingSekre" class="text-[13px] text-slate-400 text-center py-6">
                        Tidak ada sekretaris aktif tersedia.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/60">
                <button @click="sekretarisModalOpen = false"
                        class="px-5 py-2.5 text-[13.5px] font-semibold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                    Batal
                </button>
                <button @click="simpanSekretaris()"
                        :disabled="!selectedSekretaris"
                        class="inline-flex items-center gap-2 text-[13.5px] font-semibold px-6 py-2.5 rounded-xl transition-colors"
                        :class="selectedSekretaris ? 'bg-[#1e3a5f] hover:bg-[#162d4a] text-white cursor-pointer' : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                    <i class="fas fa-user-check text-xs"></i> Pilih Sekretaris
                </button>
            </div>
        </div>
    </div>


    {{-- Toast --}}
    <div x-show="toast" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-[13px] font-medium pointer-events-none"
         :class="toastType === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'">
        <i class="fas" :class="toastType === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'"></i>
        <span x-text="toastMsg"></span>
    </div>

</div>
@endsection

@push('scripts')
<script>
function proposalAssignment() {
    return {
        // ── Modal Sekretaris ──────────────────────
        sekretarisModalOpen: false,
        sekretarisList:      [],
        selectedSekretaris:  null,
        loadingSekre:        false,
        activePropId:        null,

        // ── Toast ─────────────────────────────────
        toast:    false,
        toastMsg: '',
        toastType:'success',

        // ── Open modal sekretaris ─────────────────
        async openSekretarisModal(proposalId) {
            this.activePropId       = proposalId;
            this.selectedSekretaris = null;
            this.sekretarisModalOpen= true;
            this.loadingSekre       = true;

            try {
                const res = await fetch('{{ route("admin.proposal-assignment.sekretaris-list") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                this.sekretarisList = await res.json();
            } catch (e) {
                this.showToast('Gagal memuat daftar sekretaris', 'error');
            } finally {
                this.loadingSekre = false;
            }
        },

        // ── Simpan pilihan sekretaris ─────────────
        async simpanSekretaris() {
            if (!this.selectedSekretaris) return;
            try {
                const res = await fetch(`/admin/proposal-assignment/${this.activePropId}/pilih-sekretaris`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({ sekretaris_id: this.selectedSekretaris }),
                });
                if (!res.ok) throw new Error();
                this.sekretarisModalOpen = false;
                this.showToast('Sekretaris berhasil dipilih. Klik "Kirim" untuk mengirim.', 'success');
                setTimeout(() => location.reload(), 1500);
            } catch {
                this.showToast('Gagal memilih sekretaris', 'error');
            }
        },

        // ── Toast helper ──────────────────────────
        showToast(msg, type = 'success') {
            this.toastMsg  = msg;
            this.toastType = type;
            this.toast     = true;
            setTimeout(() => this.toast = false, 3500);
        },
    };
}

// ── Konfirmasi Modal ──────────────────────────────────────────────
function showKonfirmasiModal(pesan, onKonfirmasi) {
    // Buat modal konfirmasi
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center';
    overlay.innerHTML = `
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
            <div class="px-6 py-5 text-center">
                <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-paper-plane text-blue-500 text-xl"></i>
                </div>
                <h3 class="text-[16px] font-bold text-slate-900 mb-2">Konfirmasi Pengiriman</h3>
                <p class="text-[13px] text-slate-500 leading-relaxed">${pesan}</p>
            </div>
            <div class="flex gap-3 px-6 pb-5">
                <button id="konfirmasi-batal"
                        class="flex-1 py-2.5 border border-slate-200 rounded-xl text-[13.5px] font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
                    Batal
                </button>
                <button id="konfirmasi-ok"
                        class="flex-1 py-2.5 bg-[#1e3a5f] hover:bg-[#162d4a] rounded-xl text-[13.5px] font-semibold text-white transition-colors cursor-pointer">
                    Ya, Kirim
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    // Tombol batal
    overlay.querySelector('#konfirmasi-batal').addEventListener('click', () => {
        overlay.remove();
    });

    // Klik backdrop
    overlay.querySelector('.absolute').addEventListener('click', () => {
        overlay.remove();
    });

    // Tombol konfirmasi
    overlay.querySelector('#konfirmasi-ok').addEventListener('click', () => {
        overlay.remove();
        onKonfirmasi();
    });
}

// ── Kirim Sekretaris ──────────────────────────────────────────────
async function kirimSekretaris(proposalId, btn) {
    showKonfirmasiModal(
        'Proposal akan dikirim ke sekretaris yang dipilih. Tindakan ini akan mengubah status proposal menjadi <strong>On Review</strong>.',
        async () => {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin text-[9px]"></i> Mengirim...';
            try {
                const res = await fetch(`/admin/proposal-assignment/${proposalId}/kirim-sekretaris`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                });
                if (!res.ok) throw new Error();
                location.reload();
            } catch {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane text-[9px]"></i> Kirim';
                alert('Gagal mengirim ke sekretaris.');
            }
        }
    );
}

// ── Ganti (re-open modal) ─────────────────────────────────────────
function gantiSekretaris(proposalId) {
    Alpine.$data(document.querySelector('[x-data]')).openSekretarisModal(proposalId);
}
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush