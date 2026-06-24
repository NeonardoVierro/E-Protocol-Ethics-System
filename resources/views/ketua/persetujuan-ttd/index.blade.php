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
            <th class="text-left px-4 py-3 text-[12px] font-bold text-slate-500">Preview</th>
            <th class="text-left px-4 py-3 text-[12px] font-bold text-slate-500">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($docs as $d)
          <tr class="hover:bg-slate-50/60 transition-colors align-top">
            <td class="px-6 py-4 font-bold">{{ $d->document_number ?: ('EC-'. $d->id) }}</td>
            <td class="px-4 py-4 max-w-[320px]">
              <div class="font-semibold text-slate-900">{{ $d->proposal?->title ?? $d->original_name ?? '-' }}</div>
              <div class="text-sm text-slate-500 mt-1">{{ $d->proposal?->researcher?->name ?? '-' }}</div>
            </td>
            <td class="px-4 py-4 text-sm text-slate-600">
              <div class="space-y-2">
                <div><span class="font-semibold">Judul:</span> {{ $d->preview_data['title'] ?? '-' }}</div>
                <div><span class="font-semibold">PI:</span> {{ $d->preview_data['principal_investigator'] ?? '-' }}</div>
                <div><span class="font-semibold">Institusi:</span> {{ $d->preview_data['institution'] ?? '-' }}</div>
                <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3">
                  <div class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-slate-500">Preview Sertifikat</div>
                  <div class="max-h-[320px] overflow-hidden rounded-md border border-slate-200 bg-white p-2">
                    <div class="origin-top-left scale-[0.42] w-[238%]">
                      @include('peneliti.pengajuan.partials.ethical-clearance-document', [
                        'certificatePreviewData' => [
                          'title' => $d->preview_data['title'] ?? '-',
                          'principal_investigator' => $d->preview_data['principal_investigator'] ?? '-',
                          'members' => $d->preview_data['members'] ?? '-',
                          'institution' => $d->preview_data['institution'] ?? '-',
                          'research_place' => $d->preview_data['research_place'] ?? '-',
                          'nomor_ec' => $d->preview_data['nomor_ec'] ?? ($d->document_number ?: '-'),
                          'chair_name' => $d->preview_data['chair_name'] ?? 'Ketua Komite Etik',
                        ],
                        'issuedAt' => \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y'),
                        'pdfMode' => false,
                      ])
                    </div>
                  </div>
                </div>
                <a href="{{ route('ketua.ethics-preview-download', $d->id) }}" target="_blank" class="mt-3 inline-flex items-center text-blue-600 hover:underline font-medium">Download PDF</a>
              </div>
            </td>
            <td class="px-4 py-4">
              <div class="flex flex-col gap-2">
                <label class="text-sm text-slate-600">Upload PDF TTD</label>
                <input type="file" accept="application/pdf" class="text-sm" id="signed-file-{{ $d->id }}">
                <button onclick="signDocument({{ $d->id }}, '{{ $d->proposal?->title ?? 'Dokumen' }}')" class="text-left text-blue-600 hover:underline font-medium">
                  Kirim
                </button>
              </div>
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
    if (!confirm('Apakah Anda yakin ingin mengirim dokumen "' + title + '"?')) {
        return;
    }

    const input = document.getElementById('signed-file-' + documentId);
    const formData = new FormData();
    formData.append('document_id', documentId);
    if (input && input.files[0]) {
        formData.append('signed_file', input.files[0]);
    }

    fetch('/ketua/sign-document', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        },
        body: formData,
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success) {
            alert('Dokumen berhasil dikirim!');
            location.reload();
        } else {
            alert(data.error || 'Gagal mengirim dokumen.');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan saat mengirim dokumen.');
    });
}
</script>
@endpush
