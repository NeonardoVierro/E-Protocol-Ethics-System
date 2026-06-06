@extends('layouts.reviewer')

@section('title', 'Riwayat Review')
@section('page-title', 'Riwayat Review')

@section('content')
<script id="tailwind-config">/* tailwind config placeholder */</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9ff; }
</style>

<main class="ml-[0px] pt-6 min-h-screen">
    <div class="max-w-container-max mx-auto p-6">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-semibold text-on-surface mb-2">Riwayat Review</h1>
            <p class="text-sm text-slate-600 max-w-2xl">Daftar semua proposal yang telah selesai dievaluasi. Gunakan tabel di bawah untuk melihat kembali keputusan dan catatan review Anda.</p>
        </div>

        <!-- Filters & Stats -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <form method="GET" action="{{ route('reviewer.riwayat-review') }}" class="col-span-12 lg:col-span-8 bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center gap-4">
                <div class="flex-1 w-full md:w-auto">
                    <label class="block text-xs text-slate-400 mb-2">Filter Berdasarkan Review Type</label>
                    <select name="review_type" class="w-full border border-slate-200 rounded-lg p-2 text-sm">
                        <option value="">Semua Tipe</option>
                        <option value="exempted" {{ request('review_type') === 'exempted' ? 'selected' : '' }}>Exempted</option>
                        <option value="expedited" {{ request('review_type') === 'expedited' ? 'selected' : '' }}>Expedited</option>
                        <option value="full_board" {{ request('review_type') === 'full_board' ? 'selected' : '' }}>Full Board</option>
                    </select>
                </div>
                <div class="flex-1 w-full md:w-auto">
                    <label class="block text-xs text-slate-400 mb-2">Rentang Tanggal (Tanggal Review)</label>
                    <div class="flex gap-2">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-1/2 border border-slate-200 rounded-lg p-2 text-sm" />
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-1/2 border border-slate-200 rounded-lg p-2 text-sm" />
                    </div>
                </div>
                <div class="flex-none pt-2 md:pt-0">
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg">Terapkan Filter</button>
                </div>
            </form>

            <div class="col-span-12 lg:col-span-4 grid grid-cols-2 gap-4">
                <div class="bg-primary text-white p-4 rounded-xl shadow-sm flex flex-col justify-center">
                    <p class="text-xs uppercase tracking-widest text-blue-200">Total Direview</p>
                    <p class="text-2xl font-extrabold">{{ $totalReviews ?? $reviews->total() }}</p>
                </div>
                <div class="bg-secondary-container text-on-secondary-container p-4 rounded-xl shadow-sm flex flex-col justify-center">
                    <p class="text-xs uppercase tracking-widest text-on-secondary-container/80">Approval Rate</p>
                    <p class="text-2xl font-extrabold">{{ $approvalRate ?? 0 }}%</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs text-slate-500">ID Proposal</th>
                            <th class="px-6 py-4 text-xs text-slate-500">Judul Penelitian</th>
                            <th class="px-6 py-4 text-xs text-slate-500">Peneliti</th>
                            <th class="px-6 py-4 text-xs text-slate-500">Tanggal Submit</th>
                            <th class="px-6 py-4 text-xs text-slate-500 text-center">Keputusan Reviewer</th>
                            <th class="px-6 py-4 text-xs text-slate-500 text-center">Status Final</th>
                            <th class="px-6 py-4 text-xs text-slate-500 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($reviews as $review)
                            @php
                                $proposal = $review->proposal;
                                $feedback = $review->feedback;
                                $rec = $feedback?->recommendation ?? 'unknown';
                                $recLabel = $rec === 'approved' ? 'Approve' : ($rec === 'revision' ? 'Revision' : ($rec === 'rejected' ? 'Reject' : 'Unknown'));
                                $statusLabel = $rec === 'approved' ? 'Cleared' : ($rec === 'rejected' ? 'Rejected' : 'Pending Committee');
                                $statusIcon = $rec === 'approved' ? 'check_circle' : ($rec === 'rejected' ? 'cancel' : 'hourglass_top');
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs font-bold text-primary">{{ $proposal->proposal_code ?? '#EC-' . $proposal->id }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-900 line-clamp-1">{{ $proposal->title }}</p>
                                    <p class="text-[11px] text-slate-500">Kategori: {{ $proposal->review_type ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold">{{ strtoupper(substr($proposal->researcher->name ?? '-',0,2)) }}</div>
                                        <span class="text-sm">{{ $proposal->researcher->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ optional($proposal->submission_date)->format('d M Y') ?? '-' }}</td>
                                @php
                                    $recBadge = $rec === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($rec === 'revision' ? 'bg-blue-100 text-blue-700' : ($rec === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700'));
                                @endphp
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $recBadge }}">{{ $recLabel }}</span>
                                </td>
                                @php
                                    $statusClass = $rec === 'approved' ? 'text-emerald-700' : ($rec === 'rejected' ? 'text-red-700' : 'text-blue-700');
                                    $statusBg = $rec === 'approved' ? '' : ($rec === 'rejected' ? '' : '');
                                @endphp
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 text-sm font-bold {{ $statusClass }} {{ $statusBg }} px-3 py-1 rounded-full">
                                        <span class="material-symbols-outlined text-[16px] {{ $statusClass }}">{{ $statusIcon }}</span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('reviewer.riwayat-review.show', $review->id) }}" class="text-primary hover:text-blue-900 font-button text-sm flex items-center gap-1">
                                        View Details
                                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-slate-50 flex items-center justify-between border-t border-slate-200">
                <p class="text-sm text-slate-500">Menampilkan {{ $reviews->firstItem() ?? 0 }}-{{ $reviews->lastItem() ?? 0 }} dari {{ $reviews->total() }} proposal</p>
                <div>
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>

        <!-- Footer Meta -->
        <div class="mt-6 pt-4 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-emerald-700"></div><span class="text-xs uppercase text-slate-500">Final Cleared</span></div>
                <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-red-700"></div><span class="text-xs uppercase text-slate-500">Final Rejected</span></div>
                <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-blue-700"></div><span class="text-xs uppercase text-slate-500">Waiting Committee</span></div>
            </div>
        </div>
    </div>
</main>

@endsection