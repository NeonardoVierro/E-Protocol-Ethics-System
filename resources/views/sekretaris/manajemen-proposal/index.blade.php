@extends('layouts.sekretaris')

@section('title', 'Manajemen Proposal')
@section('page-title', 'Manajemen Proposal')
@section('breadcrumb', 'Cek Kelengkapan Dokumen')

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Dokumen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($proposals as $proposal)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm">{{ $proposal['id'] }}</td>
                <td class="px-6 py-4 text-sm">{{ $proposal['judul'] }}</td>
                <td class="px-6 py-4">
                    @if($proposal['status'] == 'lengkap')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs">Lengkap</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs">Kurang</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm">{{ $proposal['tanggal'] }}</td>
                <td class="px-6 py-4">
                    <button onclick="featureInDevelopment('Approve proposal {{ $proposal['id'] }}')" class="text-indigo-600 hover:underline text-sm">Approve Lanjut Review</button>
                </table>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada proposal.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection