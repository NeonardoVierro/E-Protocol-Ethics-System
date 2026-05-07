@extends('layouts.sekretaris')

@section('title', 'Hasil Review')
@section('page-title', 'Hasil Review')
@section('breadcrumb', 'Feedback dari Reviewer')

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Proposal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reviewer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Feedback</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($reviews as $review)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm">{{ $review['proposal_id'] }}</td>
                <td class="px-6 py-4 text-sm">{{ $review['judul'] }}</td>
                <td class="px-6 py-4 text-sm">{{ $review['reviewer'] }}</td>
                <td class="px-6 py-4 text-sm">{{ $review['feedback'] }}</td>
                <td class="px-6 py-4 text-sm">{{ isset($review['tanggal']) ? \Carbon\Carbon::parse($review['tanggal'])->format('d M Y') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada hasil review.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection