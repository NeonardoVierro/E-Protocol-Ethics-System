@extends('layouts.dashboard')

@section('title', 'Unggah Revisi Proposal')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    <div class="bg-white rounded-xl border border-outline-variant p-8">
        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-primary mb-2">Unggah Revisi untuk: {{ $proposal->title }}</h2>
            <p class="text-on-surface-variant">Unggah dokumen revisi sesuai permintaan reviewer.</p>
        </div>

        <form action="{{ route('pengajuan.submit-revisi', $proposal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih File Revisi (PDF)</label>
                <input type="file" name="revision_file" accept="application/pdf" required class="block w-full text-sm text-slate-700" />
                @error('revision_file') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Revisi (opsional)</label>
                <textarea name="revision_note" rows="4" class="block w-full border border-slate-200 rounded-md p-3 text-sm">{{ old('revision_note') }}</textarea>
                @error('revision_note') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('pengajuan.riwayat-pengajuan') }}" class="px-4 py-2 border rounded-lg text-sm">Batal</a>
                <button type="submit" class="px-6 py-2 bg-primary text-on-primary rounded-lg text-sm font-semibold">Unggah Revisi</button>
            </div>
        </form>
    </div>
</div>
@endsection
