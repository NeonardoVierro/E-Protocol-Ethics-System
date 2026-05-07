@extends('layouts.sekretaris')

@section('title', 'Keputusan')
@section('page-title', 'Keputusan')
@section('breadcrumb', 'Approve / Revise / Reject')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-5">
    @forelse($keputusan as $item)
    <div class="border-b border-gray-100 py-4 flex flex-wrap justify-between items-center">
        <div class="flex-1">
            <p class="font-semibold text-gray-800">{{ $item['proposal_id'] }} - {{ $item['judul'] }}</p>
            <p class="text-sm text-gray-500">Status saat ini: 
                @if($item['status'] == 'approved') 
                    <span class="text-green-600">Disetujui</span>
                @elseif($item['status'] == 'revisi') 
                    <span class="text-yellow-600">Revisi</span>
                @else 
                    <span class="text-gray-500">Pending</span> 
                @endif
            </p>
            @if(isset($item['tenggat']) && $item['tenggat'])
                <p class="text-xs text-gray-400">Tenggat revisi: {{ \Carbon\Carbon::parse($item['tenggat'])->format('d M Y') }}</p>
            @endif
        </div>
        <div class="flex gap-2 mt-2 sm:mt-0">
            <select class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                <option value="approved" {{ $item['status'] == 'approved' ? 'selected' : '' }}>Approve</option>
                <option value="revisi" {{ $item['status'] == 'revisi' ? 'selected' : '' }}>Revise</option>
                <option value="reject" {{ $item['status'] == 'rejected' ? 'selected' : '' }}>Reject</option>
            </select>
            <button onclick="featureInDevelopment('Simpan keputusan untuk {{ $item['proposal_id'] }}')" class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-blue-700">Simpan</button>
        </div>
    </div>
    @empty
    <div class="text-center py-8 text-gray-500">
        Tidak ada data keputusan.
    </div>
    @endforelse
</div>
@endsection