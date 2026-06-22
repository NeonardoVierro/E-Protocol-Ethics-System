@extends('layouts.ketua')

@section('title', 'Dashboard - Ketua')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Selamat datang, Ketua')

@section('content')
<div>
  <div class="max-w-container-max mx-auto">
    <div class="mb-6">
      <h2 class="font-h2 text-h2">Dashboard Ketua</h2>
      <p class="font-body-md text-on-surface-variant">Ringkasan aktivitas penandatanganan dokumen Ethical Clearance.</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 gap-5 mb-5">
      <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fas fa-clock text-orange-500 text-lg"></i>
        </div>
        <div>
          <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Menunggu Tanda Tangan</p>
          <p class="text-[28px] font-bold text-slate-900 leading-none tracking-tight">{{ $pendingDocuments }}</p>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
        </div>
        <div>
          <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Sudah Ditandatangani</p>
          <p class="text-[28px] font-bold text-slate-900 leading-none tracking-tight">{{ $signedDocuments }}</p>
        </div>
      </div>
    </div>

    {{-- Quick Action --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
      <h3 class="text-[15px] font-bold text-slate-900 mb-3">Aksi Cepat</h3>
      <a href="{{ route('ketua.persetujuan-ttd') }}" class="inline-flex items-center gap-2 bg-[#1e3a5f] hover:bg-[#162d4a] text-white text-[13px] font-bold px-5 py-2.5 rounded-xl transition-colors cursor-pointer">
        <i class="fas fa-file-signature text-sm"></i>
        Lihat Dokumen Menunggu Tanda Tangan
      </a>
    </div>
  </div>
</div>
@endsection
