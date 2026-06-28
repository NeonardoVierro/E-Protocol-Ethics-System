@extends('layouts.sekretaris')

@section('title', 'Hasil Review')
@section('page-title', 'Hasil Review')
@section('breadcrumb', 'Feedback dari Reviewer')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-xl font-semibold text-slate-900">Hasil Review</h2>
            <p class="mt-1 text-sm text-slate-600">Ringkasan hasil review yang telah diserahkan oleh reviewer.</p>
        </div>

        <div class="px-6 py-4 grid gap-4 sm:grid-cols-4">
            <div class="rounded-3xl bg-slate-50 p-4 text-center">
                <div class="text-xs text-slate-500">Total</div>
                <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $stats['total'] ?? 0 }}</div>
            </div>
            <div class="rounded-3xl bg-emerald-50 p-4 text-center">
                <div class="text-xs text-slate-500">Disetujui</div>
                <div class="mt-2 text-2xl font-semibold text-emerald-700">{{ $stats['approved'] ?? 0 }}</div>
            </div>
            <div class="rounded-3xl bg-amber-50 p-4 text-center">
                <div class="text-xs text-slate-500">Perlu Revisi</div>
                <div class="mt-2 text-2xl font-semibold text-amber-700">{{ $stats['revision'] ?? 0 }}</div>
            </div>
            <div class="rounded-3xl bg-red-50 p-4 text-center">
                <div class="text-xs text-slate-500">Ditolak</div>
                <div class="mt-2 text-2xl font-semibold text-red-700">{{ $stats['rejected'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-slate-500">Menampilkan {{ $proposals->count() }} dari {{ $proposalIds->total() }} proposal</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.02em]">Proposal</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.02em]">On Behalf</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.02em]">Progress Review</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.02em]">Rekomendasi</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-[0.02em]">Terakhir Diupdate</th>
                        <th class="px-6 py-4 text-right font-semibold uppercase tracking-[0.02em]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($proposals as $proposal)
                    @php
                        $allRecommendations = $proposal->recommendations ?? ['approved' => 0, 'revision' => 0, 'rejected' => 0];
                        $dominantStatus = 'pending';
                        if ($allRecommendations['rejected'] > 0) $dominantStatus = 'rejected';
                        elseif ($allRecommendations['revision'] > 0) $dominantStatus = 'revision';
                        elseif ($allRecommendations['approved'] > 0) $dominantStatus = 'approved';
                        
                        $statusColor = $dominantStatus === 'approved' ? 'bg-emerald-50 hover:bg-emerald-100' : ($dominantStatus === 'revision' ? 'bg-amber-50 hover:bg-amber-100' : ($dominantStatus === 'rejected' ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-slate-50'));
                        $borderColor = $dominantStatus === 'approved' ? 'border-l-emerald-500' : ($dominantStatus === 'revision' ? 'border-l-amber-500' : ($dominantStatus === 'rejected' ? 'border-l-red-500' : 'border-l-slate-300'));
                    @endphp
                    
                    <tr class="border-l-4 {{ $borderColor }} {{ $statusColor }} transition-all duration-200">
                        <td class="px-6 py-4 max-w-xl text-slate-900">
                            <div class="font-semibold">{{ optional($proposal)->nomor_ec ?? ('#' . ($proposal->id ?? '')) }}</div>
                            <div class="mt-1 text-sm text-slate-600 truncate">{{ optional($proposal)->title ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-700">
                            <div class="font-medium">{{ optional($proposal->researcher)->name ?? '—' }}</div>
                            <div class="text-xs text-slate-500">{{ optional($proposal->researcher)->email ?? optional($proposal)->asal_instansi ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-semibold text-slate-900">{{ $proposal->feedbacks_count ?? 0 }} / {{ $proposal->reviewers_count ?? 0 }}</div>
                                <div class="w-20 h-2 bg-slate-200 rounded-full overflow-hidden">
                                    @php
                                        $progress = ($proposal->reviewers_count ?? 0) > 0 ? (($proposal->feedbacks_count ?? 0) / ($proposal->reviewers_count ?? 1)) * 100 : 0;
                                        $progressColor = $progress >= 100 ? 'bg-emerald-500' : ($progress >= 50 ? 'bg-amber-500' : 'bg-slate-400');
                                    @endphp
                                    <div class="h-full {{ $progressColor }}" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                @if($allRecommendations['approved'] > 0)
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-300">✓ {{ $allRecommendations['approved'] }}</span>
                                @endif
                                @if($allRecommendations['revision'] > 0)
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-300">⟳ {{ $allRecommendations['revision'] }}</span>
                                @endif
                                @if($allRecommendations['rejected'] > 0)
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 border border-red-300">✕ {{ $allRecommendations['rejected'] }}</span>
                                @endif
                                @if(!$allRecommendations['approved'] && !$allRecommendations['revision'] && !$allRecommendations['rejected'])
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-slate-100 text-slate-600">Menunggu</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ optional($proposal->latest_feedback->submitted_at ?? null)->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('sekretaris.hasil-review.show', $proposal->id) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 hover:bg-primary hover:text-white transition-all duration-200 shadow-sm" title="Lihat Detail Review" aria-label="Lihat Detail Review">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
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

        <div class="px-6 py-4 bg-white border-t border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-slate-600">Menampilkan halaman {{ $proposalIds->currentPage() }} dari {{ $proposalIds->lastPage() }}</div>
            <div>
                {{ $proposalIds->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection