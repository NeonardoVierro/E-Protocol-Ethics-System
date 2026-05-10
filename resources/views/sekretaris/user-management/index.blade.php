@extends('layouts.sekretaris')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('breadcrumb', 'Aktivasi Akun Peneliti')

@section('content')
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Institusi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($pendingUsers as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm">{{ $user->name }}</td>
                <td class="px-6 py-4 text-sm">{{ $user->email }}</td>
                <td class="px-6 py-4 text-sm">{{ $user->institution ?? '-' }}</td>
                <td class="px-6 py-4">
                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs">Pending</span>
                </td>
                <td class="px-6 py-4">
                    <form action="{{ route('sekretaris.user-management.activate', $user->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">Aktifkan</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada user pending aktivasi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection