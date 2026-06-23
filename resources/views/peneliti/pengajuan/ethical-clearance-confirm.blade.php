@extends('layouts.dashboard')

@section('title', 'Konfirmasi Ethical Clearance')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-0 to-slate-100 py-8 lg:py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-3xl shadow-lg border border-slate-200 overflow-hidden">
            <div class="px-8 py-8 border-b border-slate-200">
                <h1 class="text-3xl font-bold text-slate-900">Konfirmasi Dokumen Ethical Clearance</h1>
                <p class="text-slate-600 mt-2">Periksa kembali detail pengajuan dan nomor EC. Setelah Anda konfirmasi, dokumen akan dikirim ke ketua untuk tanda tangan.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-slate-50 rounded-3xl border border-slate-200 p-6">
                        <h2 class="text-xl font-semibold text-slate-900 mb-4">Informasi Proposal</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400 mb-2">Judul Proposal</p>
                                <p class="text-sm text-slate-800 font-semibold">{{ $proposal->title }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400 mb-2">Nomor EC</p>
                                <p class="text-sm text-slate-800 font-semibold">{{ $proposal->nomor_ec ?? 'Belum terisi' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400 mb-2">Peneliti</p>
                                <p class="text-sm text-slate-800 font-semibold">{{ optional($proposal->researcher)->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400 mb-2">Ketua yang ditetapkan</p>
                                <p class="text-sm text-slate-800 font-semibold">{{ optional($assignment->assignedTo)->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 p-6">
                        <h2 class="text-xl font-semibold text-slate-900 mb-4">Dokumen Proposal</h2>
                        <div class="space-y-3">
                            @foreach($proposalFiles as $file)
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $file->original_name }}</p>
                                        <p class="text-xs text-slate-500">{{ ucfirst($file->file_type) }}</p>
                                    </div>
                                    @if($file->file_path)
                                        <a href="{{ route('pengajuan.riwayat-pengajuan.revision-file.view', ['proposal' => $proposal->id, 'file' => $file->id]) }}" class="text-blue-600 text-sm hover:underline">Lihat</a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-slate-50 rounded-3xl border border-slate-200 p-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-3">Status Saat Ini</h3>
                        <div class="rounded-2xl bg-white border border-slate-200 p-4">
                            <p class="text-sm text-slate-500 mb-2">Status proposal</p>
                            <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-xs font-semibold">{{ $proposal->status_label }}</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 p-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-3">Petunjuk</h3>
                        <ol class="list-decimal list-inside space-y-3 text-sm text-slate-600">
                            <li>Periksa kembali seluruh data proposal dan nomor EC.</li>
                            <li>Pastikan Ketua yang ditetapkan sudah sesuai.</li>
                            <li>Jika sudah benar, klik tombol konfirmasi untuk melanjutkan ke tanda tangan Ketua.</li>
                        </ol>
                    </div>

                    <form action="{{ route('pengajuan.ethical-clearance.confirm.submit', $proposal->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">Konfirmasi dan Kirim ke Ketua</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
