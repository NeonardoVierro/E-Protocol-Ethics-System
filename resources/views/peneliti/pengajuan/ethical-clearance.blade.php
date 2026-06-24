@extends('layouts.dashboard')

@section('title', 'Ethical Clearance')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    <div class="bg-white rounded-xl border border-outline-variant p-8">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-slate-900">Ethical Clearance</h1>
            <p class="text-slate-500 mt-1">Lihat draft dokumen ethical clearance yang telah diterbitkan oleh admin untuk proposal Anda.</p>
        </div>

        @if($documents->isEmpty())
            <div class="rounded-3xl border border-slate-200 bg-surface-container-low p-10 text-center">
                <span class="material-symbols-outlined text-primary text-5xl mb-4">badge</span>
                <p class="text-slate-700 text-lg font-semibold mb-2">Belum ada draft sertifikat</p>
                <p class="text-slate-500">Draft sertifikat akan muncul di halaman ini setelah admin menyimpan assignment untuk proposal Anda.</p>
            </div>
        @else
            <div class="space-y-8">
                @foreach($documents as $document)
                    @php
                        $notes = [];
                        if (!empty($document->notes)) {
                            $decoded = json_decode($document->notes, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $notes = $decoded;
                            } else {
                                $notes = ['notes' => (string) $document->notes];
                            }
                        }

                        $displayTitle = trim((string)($notes['title'] ?? '')) !== '' ? ($notes['title'] ?? '') : ($document->proposal->title ?? '');
                        $displayPI = trim((string)($notes['principal_investigator'] ?? '')) !== '' ? ($notes['principal_investigator'] ?? '') : (optional($document->proposal->researcher)->name ?? $document->proposal->nama_peneliti ?? '');
                        $displayMembers = trim((string)($notes['members'] ?? '')) !== '' ? ($notes['members'] ?? '') : '';
                        $displayInstitution = trim((string)($notes['institution'] ?? '')) !== '' ? ($notes['institution'] ?? '') : (optional($document->proposal->researcher)->institution ?? $document->proposal->asal_instansi ?? '');
                        $displayPlace = trim((string)($notes['research_place'] ?? '')) !== '' ? ($notes['research_place'] ?? '') : '';
                        $displayNotes = trim((string)($notes['notes'] ?? '')) !== '' ? ($notes['notes'] ?? '') : 'Tidak ada catatan tambahan dari admin.';
                    @endphp

                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-2">Draft Sertifikat</p>
                                <h2 class="text-xl font-semibold text-slate-900">{{ $document->proposal->title ?? 'Proposal tidak tersedia' }}</h2>
                                <p class="text-sm text-slate-500 mt-2">Nomor EC: <span class="font-medium text-slate-900">{{ $document->document_number ?? '-' }}</span></p>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $document->status_badge }}">
                                    {{ $document->status_label }}
                                </span>
                                @if($document->proposal)
                                    <a href="{{ route('pengajuan.riwayat-pengajuan.show', $document->proposal->id) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Lihat Proposal</a>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6">
                            <p class="text-sm text-slate-500 mb-4">Ini adalah draft sertifikat yang disiapkan oleh admin. Silakan periksa semua data berikut sebelum melakukan konfirmasi.</p>

                            <div class="rounded-3xl border border-slate-200 bg-surface-container-low p-5" data-ethical-clearance-card data-route="{{ route('pengajuan.ethical-clearance.save-preview-data', $document->proposal->id) }}">
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Detail Sertifikat</p>
                                    <div class="flex gap-2">
                                        <a href="{{ route('pengajuan.ethical-clearance.preview', $document->proposal->id) }}" target="_blank" class="text-xs text-green-600 hover:text-green-700 font-semibold">Preview</a>
                                        <button type="button" data-edit-toggle class="text-xs text-blue-600 hover:text-blue-700 font-semibold">Edit</button>
                                        <button type="button" data-cancel-toggle class="hidden text-xs text-red-600 hover:text-red-700 font-semibold">Batal</button>
                                    </div>
                                </div>

                                <div class="grid gap-4 lg:grid-cols-2">
                                    <div data-detail-view class="space-y-3 text-sm text-slate-700">
                                        <div>
                                            <p class="font-semibold text-slate-900">Judul</p>
                                            <p data-detail-title>{{ $displayTitle ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">Peneliti Utama</p>
                                            <p data-detail-pi>{{ $displayPI ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">Anggota Peneliti</p>
                                            <p class="whitespace-pre-wrap" data-detail-members>{{ $displayMembers ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">Institusi</p>
                                            <p data-detail-institution>{{ $displayInstitution ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">Tempat Penelitian</p>
                                            <p data-detail-place>{{ $displayPlace ?: '-' }}</p>
                                        </div>
                                    </div>

                                    <div data-edit-view class="hidden space-y-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Judul</label>
                                            <input type="text" value="{{ $displayTitle }}" data-edit-title class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Peneliti Utama</label>
                                            <input type="text" value="{{ $displayPI }}" data-edit-pi class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Anggota Peneliti</label>
                                            <textarea rows="3" data-edit-members class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">{{ $displayMembers }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Institusi</label>
                                            <input type="text" value="{{ $displayInstitution }}" data-edit-institution class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tempat Penelitian</label>
                                            <input type="text" value="{{ $displayPlace }}" data-edit-place class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                                        </div>
                                        <button type="button" data-save-button class="w-full py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                                            Simpan Perubahan
                                        </button>
                                    </div>

                                    <div class="rounded-3xl border border-slate-200 bg-white p-5">
                                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-3">Catatan Admin</p>
                                        <p class="whitespace-pre-wrap text-sm text-slate-700">{{ $displayNotes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-500 mb-1">Tanggal Draft</p>
                                <p class="text-sm text-slate-700">{{ isset($notes['assigned_at']) ? \Illuminate\Support\Carbon::parse($notes['assigned_at'])->format('d M Y H:i') : '-' }}</p>
                            </div>
                            <div class="flex gap-3">
                                @if($document->status === \App\Models\EthicsDocument::STATUS_PUBLISHED)
                                    <a href="{{ route('pengajuan.riwayat-pengajuan.download-ethics-document', $document->proposal->id) }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                                        Unduh Sertifikat Final
                                    </a>
                                @elseif($document->proposal && $document->proposal->status === \App\Models\Proposal::STATUS_WAITING_FOR_CONFIRMATION)
                                    <form action="{{ route('pengajuan.ethical-clearance.confirm.submit', $document->proposal->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 transition">
                                            Kirim Konfirmasi
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-ethical-clearance-card]').forEach(function (card) {
            const editToggle = card.querySelector('[data-edit-toggle]');
            const cancelToggle = card.querySelector('[data-cancel-toggle]');
            const detailView = card.querySelector('[data-detail-view]');
            const editView = card.querySelector('[data-edit-view]');
            const saveButton = card.querySelector('[data-save-button]');
            const route = card.dataset.route;

            const titleEl = card.querySelector('[data-detail-title]');
            const piEl = card.querySelector('[data-detail-pi]');
            const membersEl = card.querySelector('[data-detail-members]');
            const institutionEl = card.querySelector('[data-detail-institution]');
            const placeEl = card.querySelector('[data-detail-place]');

            const editTitle = card.querySelector('[data-edit-title]');
            const editPi = card.querySelector('[data-edit-pi]');
            const editMembers = card.querySelector('[data-edit-members]');
            const editInstitution = card.querySelector('[data-edit-institution]');
            const editPlace = card.querySelector('[data-edit-place]');

            const showEdit = function () {
                if (!detailView || !editView) return;
                detailView.classList.add('hidden');
                editView.classList.remove('hidden');
                if (editToggle) editToggle.classList.add('hidden');
                if (cancelToggle) cancelToggle.classList.remove('hidden');
            };

            const showDetail = function () {
                if (!detailView || !editView) return;
                detailView.classList.remove('hidden');
                editView.classList.add('hidden');
                if (editToggle) editToggle.classList.remove('hidden');
                if (cancelToggle) cancelToggle.classList.add('hidden');
            };

            editToggle && editToggle.addEventListener('click', showEdit);
            cancelToggle && cancelToggle.addEventListener('click', function () {
                if (editTitle) editTitle.value = titleEl?.textContent?.trim() || '';
                if (editPi) editPi.value = piEl?.textContent?.trim() || '';
                if (editMembers) editMembers.value = membersEl?.textContent?.trim() || '';
                if (editInstitution) editInstitution.value = institutionEl?.textContent?.trim() || '';
                if (editPlace) editPlace.value = placeEl?.textContent?.trim() || '';
                showDetail();
            });

            saveButton && saveButton.addEventListener('click', async function () {
                try {
                    const payload = {
                        title: editTitle ? editTitle.value : '',
                        principal_investigator: editPi ? editPi.value : '',
                        members: editMembers ? editMembers.value : '',
                        institution: editInstitution ? editInstitution.value : '',
                        research_place: editPlace ? editPlace.value : '',
                    };

                    const response = await fetch(route, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const responseText = await response.text();
                    let data = null;
                    try {
                        data = responseText ? JSON.parse(responseText) : null;
                    } catch (parseError) {
                        console.error(parseError);
                    }

                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menyimpan perubahan.');
                    }

                    if (data?.success) {
                        const preview = data?.data || {};
                        if (titleEl) titleEl.textContent = preview.title || payload.title || '-';
                        if (piEl) piEl.textContent = preview.principal_investigator || payload.principal_investigator || '-';
                        if (membersEl) membersEl.textContent = preview.members || payload.members || '-';
                        if (institutionEl) institutionEl.textContent = preview.institution || payload.institution || '-';
                        if (placeEl) placeEl.textContent = preview.research_place || payload.research_place || '-';
                        showDetail();
                    } else {
                        throw new Error(data?.message || 'Gagal menyimpan perubahan.');
                    }
                } catch (error) {
                    console.error(error);
                    alert(error.message || 'Terjadi kesalahan saat menyimpan perubahan.');
                }
            });
        });
    });
</script>

@endsection