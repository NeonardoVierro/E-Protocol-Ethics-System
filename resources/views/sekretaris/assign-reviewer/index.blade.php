@extends('layouts.sekretaris')

@section('title', 'Assign Reviewer')
@section('page-title', 'Assign Reviewer')
@section('breadcrumb', 'Pilih Reviewer untuk Proposal')

@section('content')
<div class="space-y-4">
    @forelse($proposals as $proposal)
    <div class="bg-white p-5 rounded-xl shadow-sm border flex justify-between items-center">
        <div>
            <p class="font-semibold text-gray-800">{{ $proposal['id'] }} - {{ $proposal['judul'] }}</p>
            <p class="text-sm text-gray-500">Reviewer saat ini: {{ $proposal['reviewer_terpilih'] ?? 'Belum dipilih' }}</p>
        </div>
        <div class="flex gap-2">
            <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Pilih Reviewer</option>
                @foreach($reviewers as $reviewer)
                    <option value="{{ $reviewer }}">{{ $reviewer }}</option>
                @endforeach
            </select>
            <button onclick="featureInDevelopment('Assign reviewer untuk {{ $proposal['id'] }}')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Assign</button>
        </div>
    </div>
    @empty
    <div class="bg-white p-5 rounded-xl shadow-sm text-center text-gray-500">
        Tidak ada proposal yang perlu diassign reviewer.
    </div>
    @endforelse
</div>
<div class="mt-4 text-sm text-gray-500 text-center">
    <i class="fas fa-info-circle"></i> Reviewer akan menerima notifikasi setelah diassign.
</div>
@endsection