@extends('layouts.sekretaris')

@section('title', 'Hasil Review')
@section('page-title', 'Hasil Review')
@section('breadcrumb', 'Feedback dari Reviewer')

@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-800">Hasil Review</h2>
            <p class="text-sm text-slate-500">Ringkasan hasil review yang telah diserahkan oleh reviewer.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-center">
                <div class="text-xs text-slate-500">Total</div>
                <div class="text-lg font-bold text-slate-800">{{ $stats['total'] ?? 0 }}</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-slate-500">Disetujui</div>
                <div class="text-lg font-bold text-green-600">{{ $stats['approved'] ?? 0 }}</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-slate-500">Perlu Revisi</div>
                <div class="text-lg font-bold text-amber-600">{{ $stats['revision'] ?? 0 }}</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-slate-500">Ditolak</div>
                <div class="text-lg font-bold text-red-600">{{ $stats['rejected'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-4">
            <div class="text-sm text-slate-500">Menampilkan {{ $feedbacks->count() }} dari {{ $feedbacks->total() }} hasil</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-gradient-to-r from-surface-container-low to-white sticky top-0">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Proposal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Reviewer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Rekomendasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($feedbacks as $fb)
                    <tr class="hover:shadow-sm transition-shadow">
                        <td class="px-6 py-4 text-sm">
                            <div class="font-semibold text-slate-800">{{ optional($fb->proposal)->nomor_ec ?? ('#' . ($fb->proposal_id ?? '')) }}</div>
                            <div class="text-sm text-slate-600 truncate max-w-xl">{{ optional($fb->proposal)->title ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-slate-700 font-semibold">{{ strtoupper(substr(optional($fb->review->reviewer)->name ?? optional($fb->reviewer)->name ?? '-',0,1)) }}</div>
                                <div>
                                    <div class="font-medium">{{ optional($fb->review)->reviewer->name ?? optional($fb->reviewer)->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-500">{{ optional($fb->review->reviewer)->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $fb->getRecommendationBadgeAttribute() }}">{{ $fb->getRecommendationLabelAttribute() }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ optional($fb->submitted_at)->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('sekretaris.hasil-review.show', optional($fb->proposal)->id ?? '#') }}" class="inline-flex items-center gap-2 bg-primary/10 text-primary px-3 py-1 rounded-md text-sm font-medium hover:bg-primary/20 transition" title="Lihat Review">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span class="hidden sm:inline">Lihat</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada hasil review.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-white border-t border-slate-100 flex items-center justify-between">
            <div class="text-sm text-slate-600">Menampilkan halaman {{ $feedbacks->currentPage() }} dari {{ $feedbacks->lastPage() }}</div>
            <div>
                {{ $feedbacks->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection