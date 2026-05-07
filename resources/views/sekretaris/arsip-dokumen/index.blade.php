@extends('layouts.sekretaris')

@section('title', 'Arsip Dokumen')
@section('page-title', 'Arsip Dokumen')
@section('breadcrumb', 'Semua Dokumen Tersimpan')

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Dokumen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ukuran</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($arsip as $doc)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm">{{ $doc['nama'] }}</td>
                <td class="px-6 py-4 text-sm">{{ $doc['tipe'] }}</td>
                <td class="px-6 py-4 text-sm">{{ $doc['ukuran'] }}</td>
                <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($doc['tanggal'])->format('d M Y') }}</td>
                <td class="px-6 py-4">
                    <button onclick="featureInDevelopment('Download {{ $doc['nama'] }}')" class="text-blue-600 hover:underline text-sm">
                        <i class="fas fa-download"></i> Download
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada arsip dokumen.</td>
            </tr>
            @endforelse
        </tbody>
    <tr>
    <div class="bg-gray-50 px-6 py-3 text-right border-t">
        <button onclick="featureInDevelopment('Arsip lengkap')" class="text-sm text-gray-600 hover:text-indigo-700">Lihat semua arsip →</button>
    </div>
</div>
@endsection