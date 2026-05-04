@extends('layouts.admin')

@section('title', 'Dashboard - Admin')
@section('page-title', 'Admin Overview')

@section('header-action')
    <button class="inline-flex items-center gap-1.5 text-[12.5px] font-medium text-[#6b7280] cursor-pointer border-none bg-transparent p-0 hover:text-[#374151] transition-colors" 
            onclick="showToast('Date Filter sedang dalam pengembangan', 'info')">
        <i class="fas fa-calendar-days text-[12px] text-[#9ca3af]"></i>
        <span>Filter Tanggal</span>
    </button>
@endsection

@section('content')

@endsection

