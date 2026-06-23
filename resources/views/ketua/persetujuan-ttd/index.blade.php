@extends('layouts.ketua')

@section('title', 'Persetujuan & TTD')
@section('page-title', 'Persetujuan & TTD')
@section('breadcrumb', 'Persetujuan & TTD')

@section('content')
<div>
  <div class="max-w-container-max mx-auto">
    <div class="mb-6">
      <h2 class="font-h2 text-h2">Persetujuan & TTD</h2>
      <p class="font-body-md text-on-surface-variant">Daftar draft yang perlu ditandatangani oleh Ketua.</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="text-left px-6 py-3 text-[12px] font-bold text-slate-500">ID Dokumen</th>
            <th class="text-left px-4 py-3 text-[12px] font-bold text-slate-500">Proposal</th>
            <th class="text-left px-4 py-3 text-[12px] font-bold text-slate-500">Status</th>
            <th class="text-left px-4 py-3 text-[12px] font-bold text-slate-500">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($docs as $d)
          <tr class="hover:bg-slate-50/60 transition-colors">
            <td class="px-6 py-4 font-bold">{{ $d->document_number ?: ('EC-'. $d->id) }}</td>
            <td class="px-4 py-4">{{ $d->proposal?->title ?? $d->original_name ?? '-' }}</td>
            <td class="px-4 py-4">{{ $d->status_label }}</td>
            <td class="px-4 py-4">
              <button onclick="signDocument({{ $d->id }}, '{{ $d->proposal?->title ?? 'Dokumen' }}')"
                      class="text-blue-600 hover:underline font-medium">
                  Tandatangani
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Tidak ada dokumen untuk ditandatangani.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function signDocument(documentId, title) {
    if (!confirm('Apakah Anda yakin ingin menandatangani dokumen "' + title + '"?')) {
        return;
    }

    fetch('/ketua/sign-document', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        },
        body: JSON.stringify({ document_id: documentId }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Dokumen berhasil ditandatangani!');
            location.reload();
        } else {
            alert(data.error || 'Gagal menandatangani dokumen.');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan saat menandatangani dokumen.');
    });
}
</script>
@endpush
