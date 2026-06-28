@extends('layouts.sekretaris')

@section('title', 'Dashboard Sekretaris')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Statistik Proposal')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-8">
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex flex-col justify-between">
        <div class="flex justify-between items-start gap-4">
            <div>
                <p class="text-slate-500 uppercase tracking-[0.18em] text-[11px]">New Proposal</p>
                <p class="mt-3 text-3xl font-bold text-slate-950">{{ $new_proposal ?? 0 }}</p>
            </div>
            <span class="material-symbols-outlined text-primary text-4xl">pending_actions</span>
        </div>
        <p class="mt-5 text-xs text-slate-500">Proposal baru yang belum mulai diproses.</p>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex flex-col justify-between">
        <div class="flex justify-between items-start gap-4">
            <div>
                <p class="text-slate-500 uppercase tracking-[0.18em] text-[11px]">On Review</p>
                <p class="mt-3 text-3xl font-bold text-slate-950">{{ $on_review ?? 0 }}</p>
            </div>
            <span class="material-symbols-outlined text-secondary text-4xl">rate_review</span>
        </div>
        <p class="mt-5 text-xs text-slate-500">Proposal yang sedang direview oleh reviewer.</p>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex flex-col justify-between">
        <div class="flex justify-between items-start gap-4">
            <div>
                <p class="text-slate-500 uppercase tracking-[0.18em] text-[11px]">Approved</p>
                <p class="mt-3 text-3xl font-bold text-slate-950">{{ $approved ?? 0 }}</p>
            </div>
            <span class="material-symbols-outlined text-emerald-600 text-4xl">verified</span>
        </div>
        <p class="mt-5 text-xs text-slate-500">Proposal yang sudah disetujui dan siap diproses.</p>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex flex-col justify-between">
        <div class="flex justify-between items-start gap-4">
            <div>
                <p class="text-slate-500 uppercase tracking-[0.18em] text-[11px]">Rejected</p>
                <p class="mt-3 text-3xl font-bold text-slate-950">{{ $rejected ?? 0 }}</p>
            </div>
            <span class="material-symbols-outlined text-error text-4xl">block</span>
        </div>
        <p class="mt-5 text-xs text-slate-500">Proposal yang perlu revisi atau ditolak.</p>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex flex-col justify-between">
        <div class="flex justify-between items-start gap-4">
            <div>
                <p class="text-slate-500 uppercase tracking-[0.18em] text-[11px]">Total Proposal</p>
                <p class="mt-3 text-3xl font-bold text-slate-950">{{ $total_proposal ?? 0 }}</p>
            </div>
            <span class="material-symbols-outlined text-primary text-4xl">folder_open</span>
        </div>
        <p class="mt-5 text-xs text-slate-500">Jumlah keseluruhan proposal di sistem.</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-[1.8fr_1fr] gap-6 mb-8">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-semibold text-slate-950">Activity Summary</h2>
                <p class="text-sm text-slate-500">Daftar proposal terbaru dengan atribut utama untuk Sekretaris.</p>
            </div>
            <a href="{{ route('sekretaris.manajemen-proposal') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 text-sm text-primary hover:bg-primary/5 transition">
                View All Queue
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-100">
            <table class="min-w-full text-left divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">Title</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">On Behalf</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($recentProposals as $proposal)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-slate-900">{{ $proposal->title }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $proposal->researcher->name ?? $proposal->nama_peneliti ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold uppercase {{ $proposal->status_badge }}">{{ $proposal->status_label }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('sekretaris.proposal.show', $proposal) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-500">Tidak ada proposal terbaru untuk ditampilkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
        <div class="mb-5">
            <h3 class="text-lg font-semibold text-slate-950">Quick Actions</h3>
            <p class="text-sm text-slate-500">Akses cepat ke fitur utama sekretariat.</p>
        </div>

        <div class="grid gap-3">
            <a href="{{ route('sekretaris.manajemen-proposal') }}" class="flex items-center gap-3 rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm font-semibold text-slate-900 hover:border-primary hover:bg-primary/5 transition">
                <span class="material-symbols-outlined text-primary text-3xl">description</span>
                Manajemen Proposal
            </a>
            <a href="{{ route('sekretaris.hasil-review') }}" class="flex items-center gap-3 rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm font-semibold text-slate-900 hover:border-primary hover:bg-primary/5 transition">
                <span class="material-symbols-outlined text-primary text-3xl">assignment_turned_in</span>
                Hasil Review
            </a>
            <a href="{{ route('sekretaris.keputusan') }}" class="flex items-center gap-3 rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm font-semibold text-slate-900 hover:border-primary hover:bg-primary/5 transition">
                <span class="material-symbols-outlined text-primary text-3xl">gavel</span>
                Keputusan
            </a>
            <a href="{{ route('sekretaris.draf-ethical-clearance') }}" class="flex items-center gap-3 rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm font-semibold text-slate-900 hover:border-primary hover:bg-primary/5 transition">
                <span class="material-symbols-outlined text-primary text-3xl">fact_check</span>
                Draft Ethical Clearance
            </a>
            <a href="{{ route('sekretaris.arsip') }}" class="flex items-center gap-3 rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm font-semibold text-slate-900 hover:border-primary hover:bg-primary/5 transition">
                <span class="material-symbols-outlined text-primary text-3xl">archive</span>
                Arsip Dokumen
            </a>
            <a href="{{ route('sekretaris.user-management') }}" class="flex items-center gap-3 rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm font-semibold text-slate-900 hover:border-primary hover:bg-primary/5 transition">
                <span class="material-symbols-outlined text-primary text-3xl">group</span>
                Manajemen User
            </a>
        </div>
    </div>
</div>
@endsection