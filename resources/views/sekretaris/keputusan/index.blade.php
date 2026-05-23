@extends('layouts.sekretaris')

@section('title', 'Keputusan')
@section('page-title', 'Keputusan')
@section('breadcrumb', 'Approve / Revise / Reject')

@section('content')
<div class="space-y-4">
    @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @forelse($keputusan as $proposal)
    <form action="{{ route('sekretaris.keputusan.update') }}" method="POST" class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        @csrf
        <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">

        <div class="flex flex-wrap justify-between gap-4 items-center">
            <div class="flex-1 min-w-[240px]">
                <p class="font-semibold text-gray-800">{{ $proposal->id }} - {{ $proposal->title }}</p>
                <p class="text-sm text-gray-500">Pengaju: {{ $proposal->researcher->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">Status saat ini: <span class="font-semibold">
                    @if($proposal->status === 'approved')
                        <span class="text-green-600">Disetujui</span>
                    @elseif($proposal->status === 'revised')
                        <span class="text-yellow-600">Revisi</span>
                    @elseif($proposal->status === 'rejected')
                        <span class="text-red-600">Ditolak</span>
                    @elseif($proposal->status === 'on_review')
                        <span class="text-indigo-600">Sedang Direview</span>
                    @else
                        <span class="text-blue-600">Proposal Baru</span>
                    @endif
                </span></p>
            </div>

            <div class="flex gap-2 items-center">
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                    <option value="approved" {{ $proposal->status === 'approved' ? 'selected' : '' }}>Approve</option>
                    <option value="revised" {{ $proposal->status === 'revised' ? 'selected' : '' }}>Revisi</option>
                    <option value="rejected" {{ $proposal->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-blue-700">Simpan</button>
            </div>
        </div>
    </form>
    @empty
    <div class="text-center py-8 text-gray-500 bg-white rounded-xl shadow-sm border border-gray-100">
        Tidak ada data keputusan.
    </div>
    @endforelse
</div>
@endsection