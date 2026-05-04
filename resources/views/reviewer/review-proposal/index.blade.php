@extends('layouts.reviewer')

@section('title', 'Review Proposal')
@section('page-title', 'Review Proposal')
@section('breadcrumb', 'Lihat file, isi feedback, dan submit review')

@section('content')
<div class="space-y-6">
    <!-- Pilihan proposal aktif -->
    <div class="bg-indigo-50/40 p-5 rounded-xl border border-indigo-100">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Proposal yang sedang direview:</p>
        <p class="font-semibold text-gray-800 text-lg">Analisis Dampak Etis AI di Sektor Kesehatan</p>
        <input type="hidden" id="selected_proposal_id" value="EC-101">
    </div>

    <!-- Lihat file -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 flex items-center gap-2 mb-4"><i class="fas fa-file-alt text-blue-500"></i> Lampiran Proposal</h3>
        <div class="flex flex-wrap gap-3">
            <button onclick="featureInDevelopment('Lihat Proposal PDF')" class="border border-gray-300 bg-gray-50 hover:bg-gray-100 px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-eye text-indigo-500"></i> Lihat Proposal (PDF)
            </button>
            <button onclick="featureInDevelopment('Surat Pernyataan')" class="border border-gray-300 bg-gray-50 hover:bg-gray-100 px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-file-signature"></i> Surat Pernyataan
            </button>
            <button onclick="featureInDevelopment('Informed Consent')" class="border border-gray-300 bg-gray-50 hover:bg-gray-100 px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-file-word"></i> Informed Consent
            </button>
        </div>
    </div>

    <!-- Isi feedback -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 flex items-center gap-2 mb-4"><i class="fas fa-comment-dots text-amber-500"></i> Feedback Reviewer</h3>
        <textarea rows="5" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tuliskan saran, catatan etik, atau keputusan sementara..."></textarea>
    </div>

    <!-- Submit -->
    <div class="flex justify-end gap-3">
        <button onclick="featureInDevelopment('Simpan draft')" class="border border-gray-300 bg-white px-5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50">Simpan Draft</button>
        <button onclick="featureInDevelopment('Submit review')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-sm">Submit Review</button>
    </div>
</div>
@endsection