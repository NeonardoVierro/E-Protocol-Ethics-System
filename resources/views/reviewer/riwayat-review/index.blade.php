@extends('layouts.reviewer')

@section('title', 'Riwayat Review')
@section('page-title', 'Riwayat Review')
@section('breadcrumb', 'Proposal yang sudah direview')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Proposal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Review</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keputusan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td class="px-6 py-4 text-sm font-medium">#EC-098</td>
                    <td class="px-6 py-4 text-sm">Etika Penggunaan AI dalam Rekrutmen</td>
                    <td class="px-6 py-4 text-sm">15 Okt 2025</td>
                    <td class="px-6 py-4"><span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">✅ Diterima</span></td>
                    <td class="px-6 py-4"><button onclick="featureInDevelopment('Detail review')" class="text-indigo-600 text-sm hover:underline">Detail</button></td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-sm font-medium">#EC-097</td>
                    <td class="px-6 py-4 text-sm">Vaksinasi & Informed Consent Digital</td>
                    <td class="px-6 py-4 text-sm">10 Okt 2025</td>
                    <td class="px-6 py-4"><span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-1 rounded-full">✏️ Revisi Minor</span></td>
                    <td class="px-6 py-4"><button onclick="featureInDevelopment('Lihat feedback')" class="text-indigo-600 text-sm hover:underline">Lihat Feedback</button></td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-sm font-medium">#EC-095</td>
                    <td class="px-6 py-4 text-sm">Penelitian Migrasi Data Pasien</td>
                    <td class="px-6 py-4 text-sm">2 Okt 2025</td>
                    <td class="px-6 py-4"><span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full">❌ Ditolak</span></td>
                    <td class="px-6 py-4"><button onclick="featureInDevelopment('Detail penolakan')" class="text-indigo-600 text-sm hover:underline">Detail</button></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="bg-gray-50 px-6 py-3 text-right border-t">
        <button onclick="featureInDevelopment('Arsip riwayat')" class="text-sm text-gray-600 hover:text-indigo-700">Lihat semua riwayat →</button>
    </div>
</div>
@endsection