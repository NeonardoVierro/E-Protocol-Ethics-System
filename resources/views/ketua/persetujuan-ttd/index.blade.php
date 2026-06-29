@extends('layouts.ketua')

@section('title', 'Persetujuan & TTD')
@section('page-title', 'Persetujuan & TTD')
@section('breadcrumb', 'Persetujuan & TTD')

@section('content')
<div>
  <div class="max-w-container-max mx-auto">
    <div class="mb-8">
      <h2 class="font-h2 text-h2 text-slate-900">Persetujuan & TTD</h2>
      <p class="font-body-md text-on-surface-variant mt-2">Daftar draft yang perlu ditandatangani oleh Ketua.</p>
    </div>

    <div class="space-y-6">
      @forelse($docs as $d)
      <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
        <!-- Header Section -->
        <div class="border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50 p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Document Info -->
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-2">ID Dokumen</p>
              <p class="text-lg font-bold text-slate-900">{{ $d->document_number ?: ('EC-'. $d->id) }}</p>
            </div>
            
            <!-- Proposal Info -->
            <div class="md:col-span-2">
              <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-2">Proposal</p>
              <div class="space-y-1">
                <p class="font-semibold text-slate-900">{{ $d->proposal?->title ?? $d->original_name ?? '-' }}</p>
                <p class="text-sm text-slate-600">{{ $d->proposal?->researcher?->name ?? '-' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Content Section -->
        <div class="p-6">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Preview Section -->
            <div class="lg:col-span-2">
              <div class="space-y-4">
                <div>
                  <p class="text-sm font-semibold text-slate-700 mb-2">Detail Dokumen</p>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-lg bg-slate-50 p-4 border border-slate-200">
                      <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">Judul</p>
                      <p class="text-sm text-slate-900 font-medium">{{ $d->preview_data['title'] ?? '-' }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4 border border-slate-200">
                      <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">PI (Peneliti Utama)</p>
                      <p class="text-sm text-slate-900 font-medium">{{ $d->preview_data['principal_investigator'] ?? '-' }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4 border border-slate-200">
                      <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">Institusi</p>
                      <p class="text-sm text-slate-900 font-medium">{{ $d->preview_data['institution'] ?? '-' }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4 border border-slate-200">
                      <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 mb-1">Tempat Penelitian</p>
                      <p class="text-sm text-slate-900 font-medium">{{ $d->preview_data['research_place'] ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <!-- Certificate Preview -->
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                  <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold text-slate-700">Preview Sertifikat</p>
                    <a href="{{ route('ketua.ethics-preview-download', $d->id) }}" target="_blank" 
                       class="inline-flex items-center gap-2 text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                      <span class="material-symbols-outlined text-sm">download</span>
                      Download PDF
                    </a>
                  </div>
                  <div class="rounded-lg border border-slate-300 bg-white overflow-hidden" style="max-height: 500px;">
                    <div style="width: 100%; height: 100%; overflow-y: auto; overflow-x: hidden;">
                      <div style="width: 100%; max-width: 100%; zoom: 0.65; transform-origin: top center; margin: 0; padding: 0;">
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
                </div>
              </div>
            </div>

            <!-- Action Section -->
            <div class="lg:col-span-1">
              <div class="rounded-xl border border-slate-200 bg-blue-50 p-6 h-full flex flex-col justify-between">
                <div>
                  <p class="text-sm font-semibold text-slate-900 mb-4">Tanda Tangan Digital</p>
                  <div class="space-y-4">
                    <div>
                      <label for="signed-file-{{ $d->id }}" class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-600 block mb-2">
                        Upload PDF yang Sudah Ditandatangani
                      </label>
                      <input type="file" accept="application/pdf" 
                             class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                             id="signed-file-{{ $d->id }}"
                             placeholder="Pilih file PDF">
                      <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">info</span>
                        Format: PDF saja, max 10MB
                      </p>
                    </div>
                  </div>
                </div>
                <button type="button" 
                        onclick="signDocument({{ $d->id }}, '{{ addslashes($d->proposal?->title ?? 'Dokumen') }}')"
                        class="w-full mt-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-3 transition-colors flex items-center justify-center gap-2 shadow-sm">
                  <span class="material-symbols-outlined text-base">check_circle</span>
                  Kirim & Tandatangani
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      @empty
      <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-12 text-center">
        <div class="flex justify-center mb-4">
          <div class="rounded-full bg-slate-200 p-4">
            <span class="material-symbols-outlined text-3xl text-slate-400">document_scanner</span>
          </div>
        </div>
        <p class="text-slate-600 font-medium">Tidak ada dokumen untuk ditandatangani</p>
        <p class="text-sm text-slate-500 mt-1">Semua dokumen sudah selesai ditandatangani atau sedang dalam proses.</p>
      </div>
      @endforelse
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function signDocument(documentId, title) {
  Swal.fire({
    title: 'Kirim dokumen?',
    text: 'Apakah Anda yakin ingin mengirim dokumen "' + title + '"?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, kirim',
    cancelButtonText: 'Batal',
    customClass: {
      confirmButton: 'swal2-confirm bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl px-4 py-2',
      cancelButton: 'swal2-cancel bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl px-4 py-2'
    }
  }).then((result) => {
    if (!result.isConfirmed) {
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
        await Swal.fire({
          icon: 'success',
          title: 'Berhasil dikirim',
          text: 'Dokumen berhasil dikirim.',
          confirmButtonText: 'OK',
          customClass: {
            confirmButton: 'swal2-confirm bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl px-4 py-2'
          }
        });
        location.reload();
      } else {
        await Swal.fire({
          icon: 'error',
          title: 'Gagal mengirim dokumen',
          text: data.error || 'Terjadi kesalahan saat mengirim dokumen.',
          confirmButtonText: 'OK',
          customClass: {
            confirmButton: 'swal2-confirm bg-red-600 hover:bg-red-700 text-white rounded-2xl px-4 py-2'
          }
        });
      }
    })
    .catch(async err => {
      console.error('Error:', err);
      await Swal.fire({
        icon: 'error',
        title: 'Gagal mengirim dokumen',
        text: 'Terjadi kesalahan saat mengirim dokumen.',
        confirmButtonText: 'OK',
        customClass: {
          confirmButton: 'swal2-confirm bg-red-600 hover:bg-red-700 text-white rounded-2xl px-4 py-2'
        }
      });
    });
  });
}
</script>
@endpush
