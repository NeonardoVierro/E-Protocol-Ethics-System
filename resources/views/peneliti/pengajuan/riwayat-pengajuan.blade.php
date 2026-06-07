@extends('layouts.dashboard')

@section('title', 'Riwayat Pengajuan')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    @auth
        @if(auth()->user()->hasRole('peneliti') && auth()->user()->status === 'active')
            <div class="bg-white rounded-xl border border-outline-variant p-8">
                <div class="text-center mb-8">
                    <div class="w-20 h-20 mx-auto bg-surface-container-low rounded-full flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-primary text-4xl">history</span>
                    </div>
                    <h2 class="text-xl font-semibold text-primary mb-2">Riwayat Pengajuan</h2>
                    <p class="text-on-surface-variant">Lihat status pengajuan ethical clearance Anda di bawah ini.</p>
                </div>

                @if(isset($proposals) && $proposals->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-surface-variant">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Judul Proposal</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Pengajuan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @foreach($proposals as $proposal)
                                    <tr>
                                        <td class="px-4 py-4 text-sm text-slate-700">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-700">{{ $proposal->title }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-700">{{ optional($proposal->submission_date)->format('d M Y') }}</td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $proposal->status_badge }}">
                                                {{ $proposal->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if($proposal->status === 'revised' && $proposal->submitted_review_feedbacks->isNotEmpty())
                                        <tr class="bg-slate-50">
                                            <td colspan="4" class="px-4 py-4">
                                                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                                                        <div>
                                                            <p class="text-sm font-semibold text-slate-900">Feedback Reviewer</p>
                                                            <p class="text-xs text-slate-500">Silakan unggah revisi berdasarkan komentar reviewer.</p>
                                                        </div>
                                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $proposal->submitted_review_feedbacks->first()->recommendation_badge }}">
                                                            {{ $proposal->submitted_review_feedbacks->first()->recommendation_label }}
                                                        </span>
                                                    </div>

                                                    @foreach($proposal->submitted_review_feedbacks as $feedback)
                                                        <div class="mb-4 last:mb-0">
                                                            <div class="flex items-center justify-between mb-2 gap-4">
                                                                <span class="text-sm font-medium text-slate-700">Reviewer: {{ $feedback->reviewer_name ?? 'N/A' }}</span>
                                                                <span class="text-xs text-slate-500">{{ $feedback->submitted_at ?? '-' }}</span>
                                                            </div>
                                                            <div class="grid gap-3 sm:grid-cols-3 mb-3">
                                                                @if(!empty($feedback->feedback['autonomy']))
                                                                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                                                                        <p class="text-xs uppercase tracking-wide text-slate-500">Autonomy</p>
                                                                        <p class="text-sm text-slate-700">{{ $feedback->feedback['autonomy'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if(!empty($feedback->feedback['beneficence']))
                                                                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                                                                        <p class="text-xs uppercase tracking-wide text-slate-500">Beneficence</p>
                                                                        <p class="text-sm text-slate-700">{{ $feedback->feedback['beneficence'] }}</p>
                                                                    </div>
                                                                @endif
                                                                @if(!empty($feedback->feedback['justice']))
                                                                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                                                                        <p class="text-xs uppercase tracking-wide text-slate-500">Justice</p>
                                                                        <p class="text-sm text-slate-700">{{ $feedback->feedback['justice'] }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            @if(!empty($feedback->feedback['general_comments']))
                                                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 mb-2">
                                                                    <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Komentar Umum</p>
                                                                    <p class="text-sm text-slate-700 leading-relaxed">{{ $feedback->feedback['general_comments'] }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach

                                                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                                        <a href="{{ route('pengajuan.upload-revisi', $proposal->id) }}" class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark transition">
                                                            Unggah Revisi
                                                        </a>
                                                        <p class="text-sm text-slate-500">Setelah revisi diunggah, sekretariat akan menugaskan ulang ke reviewer sesuai alur.</p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center">
                        <p class="text-on-surface-variant">Belum ada data pengajuan.</p>
                    </div>
                    <div class="max-w-2xl mx-auto mt-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-on-surface">Status: <span class="text-primary">Belum Ada Pengajuan</span></span>
                        </div>
                        <div class="w-full bg-surface-container-high rounded-full h-2.5">
                            <div class="bg-primary h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between mt-3 text-xs text-on-surface-variant">
                            <span class="text-center flex-1">New</span>
                            <span class="text-center flex-1">Review</span>
                            <span class="text-center flex-1">Approved</span>
                        </div>
                    </div>

                    <div class="text-center mt-8 p-6 bg-surface-container-low rounded-lg">
                        <span class="material-symbols-outlined text-outline text-3xl mb-2">inbox</span>
                        <p class="text-on-surface-variant text-sm">Anda belum memiliki riwayat pengajuan ethical clearance.</p>
                        <a href="{{ route('pengajuan.upload-proposal') }}" class="inline-block mt-3 text-primary text-sm font-medium hover:underline">
                            Mulai Pengajuan Baru →
                        </a>
                    </div>
                @endif
            </div>
        @elseif(auth()->user()->status === 'pending')
            <div class="bg-amber-50 rounded-xl border border-amber-200 p-8 text-center">
                <div class="w-20 h-20 mx-auto bg-amber-100 rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-amber-600 text-4xl">pending</span>
                </div>
                <h2 class="text-xl font-semibold text-amber-700 mb-2">Akun Belum Diaktivasi</h2>
                <p class="text-amber-600 mb-4">Akun Anda sedang menunggu aktivasi oleh sekretariat Komisi Etik Penelitian.</p>
                <p class="text-sm text-amber-600">Setelah akun diaktivasi, Anda dapat mengajukan ethical clearance dan melihat riwayat pengajuan.</p>
            </div>
        @endif
    @else
        <!-- Konten untuk user yang belum login -->
        <div class="bg-gradient-to-r from-primary/5 to-surface-container-low rounded-xl border border-primary/20 p-8 lg:p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-primary text-5xl">lock</span>
                </div>
                <h2 class="text-2xl font-bold text-primary mb-3">Akses Diperlukan</h2>
                <p class="text-on-surface-variant mb-6">
                    Untuk melihat riwayat pengajuan dan tracking proposal, Anda harus login terlebih dahulu.<br>
                    Login untuk melihat status pengajuan ethical clearance Anda.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('login') }}" class="px-6 py-3 bg-primary text-on-primary rounded-lg font-semibold hover:bg-primary-container transition-all duration-300 shadow-md hover:shadow-lg">
                        Login Sekarang
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-3 border-2 border-primary text-primary rounded-lg font-semibold hover:bg-primary/5 transition-all duration-300">
                        Daftar Akun Baru
                    </a>
                </div>
                <div class="mt-6 pt-6 border-t border-outline-variant">
                    <p class="text-sm text-on-surface-variant">
                        Lacak status pengajuan Anda: <strong>New Proposal → On Review → Approved</strong>
                    </p>
                </div>
            </div>
        </div>
    @endauth
</div>
@endsection