@extends('layouts.dashboard')

@section('title', 'Preview Sertifikat Ethical Clearance')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    <div class="bg-white rounded-xl border border-outline-variant p-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Preview Sertifikat Ethical Clearance</h1>
                <p class="text-slate-500 mt-1">Pratinjau tampilan sertifikat yang akan dihasilkan.</p>
            </div>
            <a href="{{ route('pengajuan.ethical-clearance') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                Kembali
            </a>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8">
            @include('peneliti.pengajuan.partials.ethical-clearance-document', [
                'certificatePreviewData' => $certificatePreviewData,
                'issuedAt' => \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y'),
                'pdfMode' => false,
            ])
        </div>
    </div>
</div>
@endsection