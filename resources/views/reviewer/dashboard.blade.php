@extends('layouts.dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Dashboard Reviewer</h2>
        <p class="text-gray-600">Selamat datang, {{ Auth::user()->name }}!</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="bg-blue-50 p-4 rounded">
                <h3 class="font-semibold">Tugas Review</h3>
                <p class="text-2xl">0</p>
            </div>
            <div class="bg-green-50 p-4 rounded">
                <h3 class="font-semibold">Review Selesai</h3>
                <p class="text-2xl">0</p>
            </div>
        </div>
    </div>
</div>
@endsection
