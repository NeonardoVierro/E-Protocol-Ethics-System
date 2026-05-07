@extends('layouts.sekretaris')

@section('title', 'Draft Ethical Clearance')
@section('page-title', 'Draft Ethical Clearance')
@section('breadcrumb', 'Generate Dokumen')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-5">
    @forelse($drafts as $draft)
    <div class="border rounded-lg p-4 mb-3 flex justify-between items-center">
        <div>
            <p class="font-medium text-gray-800">{{ $draft['id'] }} - {{ $draft['judul'] }}</p>
            <p class="text-sm text-gray-500">Proposal: {{ $draft['proposal_id'] }} | Status: {{ $draft['status'] }} | Dibuat: {{ \Carbon\Carbon::parse($draft['tanggal'])->format('d M Y') }}</p>
        </div>
        <button onclick="featureInDevelopment('Generate final dokumen {{ $draft['id'] }}')" class="text-indigo-600 hover:underline text-sm">Generate Final</button>
    </div>
    @empty
    <div class="text-center py-8 text-gray-500">
        <i class="fas fa-file-alt text-4xl mb-2 opacity-50"></i>
        <p>Belum ada draft ethical clearance.</p>
    </div>
    @endforelse
    <div class="mt-4">
        <button onclick="featureInDevelopment('Buat draft baru')" class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-plus mr-1"></i> Buat Draft Baru
        </button>
    </div>
</div>
@endsection