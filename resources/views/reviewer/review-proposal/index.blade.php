@extends('layouts.reviewer')

@section('title', 'Review Proposal')
@section('page-title', 'Review Proposal')
@section('breadcrumb', 'Lihat file, isi feedback, dan submit review')

@section('content')
<div class="space-y-6">
    @if(isset($proposal) && $proposal)
        <div class="bg-indigo-50/40 p-5 rounded-xl border border-indigo-100">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Proposal yang sedang direview:</p>
            <p class="font-semibold text-gray-800 text-lg">{{ $proposal->title }}</p>
            <p class="text-sm text-gray-600">Pengaju: {{ $proposal->researcher->name ?? 'N/A' }}</p>
            <p class="text-sm text-gray-600">Tanggal masuk: {{ optional($proposal->submission_date)->format('d M Y') ?? '-' }}</p>
            <input type="hidden" id="selected_proposal_id" value="{{ $proposal->id }}">
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2 mb-4"><i class="fas fa-file-alt text-blue-500"></i> Lampiran Proposal</h3>
            <div class="flex flex-wrap gap-3">
                @forelse($proposal->files as $file)
                    <button type="button" onclick="featureInDevelopment('Lihat {{ $file->original_name }}')" class="border border-gray-300 bg-gray-50 hover:bg-gray-100 px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                        <i class="fas fa-file text-indigo-500"></i> {{ $file->original_name }}
                    </button>
                @empty
                    <div class="text-sm text-gray-500">Tidak ada file proposal aktif.</div>
                @endforelse
            </div>
        </div>

        <form action="{{ route('reviewer.review-proposal.store') }}" method="POST" class="bg-white rounded-xl shadow-sm p-6">
            @csrf
            <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Feedback Reviewer</label>
                    <textarea name="feedback" rows="5" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tuliskan saran, catatan etik, atau keputusan sementara...">{{ old('feedback') }}</textarea>
                    @error('feedback')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Keputusan</label>
                    <select name="status" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="diterima" {{ old('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="revisi" {{ old('status') == 'revisi' ? 'selected' : '' }}>Revisi</option>
                        <option value="ditolak" {{ old('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    @error('status')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="featureInDevelopment('Simpan draft')" class="border border-gray-300 bg-white px-5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50">Simpan Draft</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-sm">Submit Review</button>
                </div>
            </div>
        </form>
    @else
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <p class="text-gray-700">Belum ada proposal yang tersedia untuk direview saat ini.</p>
            <p class="text-sm text-gray-500">Silakan kembali setelah sekretaris menugaskan proposal.</p>
        </div>
    @endif
</div>
@endsection