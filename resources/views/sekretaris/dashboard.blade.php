@extends('layouts.sekretaris')

@section('title', 'Dashboard Sekretaris')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Statistik Proposal')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-8">
    <div class="bg-white p-5 rounded-xl shadow-sm border">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">New Proposal</p>
                <p class="text-2xl font-bold text-blue-600">{{ $new_proposal ?? 0 }}</p>
            </div>
            <i class="fas fa-star text-3xl text-blue-400"></i>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl shadow-sm border">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">On Review</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $on_review ?? 0 }}</p>
            </div>
            <i class="fas fa-clock text-3xl text-yellow-400"></i>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl shadow-sm border">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Approved</p>
                <p class="text-2xl font-bold text-green-600">{{ $approved ?? 0 }}</p>
            </div>
            <i class="fas fa-check-circle text-3xl text-green-400"></i>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl shadow-sm border">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Rejected</p>
                <p class="text-2xl font-bold text-red-600">{{ $rejected ?? 0 }}</p>
            </div>
            <i class="fas fa-times-circle text-3xl text-red-400"></i>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl shadow-sm border">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Total Proposal</p>
                <p class="text-2xl font-bold">{{ $total_proposal ?? 0 }}</p>
            </div>
            <i class="fas fa-file-alt text-3xl text-indigo-400"></i>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-5">
    <h3 class="font-semibold text-gray-800 mb-3">Aktivitas Terbaru</h3>
    <ul class="divide-y">
        @if(($new_proposal ?? 0) > 0)
            <li class="py-2 text-sm">Terdapat {{ $new_proposal }} proposal baru yang menunggu review.</li>
        @endif
        <li class="py-2 text-sm">Proposal P001 - Status dokumen lengkap</li>
        <li class="py-2 text-sm">Reviewer ditugaskan untuk proposal P002</li>
        <li class="py-2 text-sm">Draft Ethical Clearance EC001 dibuat</li>
    </ul>
</div>
@endsection