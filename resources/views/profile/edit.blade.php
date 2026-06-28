@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="min-h-screen bg-[#f3f6fb] py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-[32px] border border-[#dce3ec] bg-white p-8 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
            
                <div class="flex items-center gap-4 justify-between">          
                    <div>
                        <h1 class="text-4xl font-semibold text-slate-950">Edit Profil</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Perbarui data akun dan informasi kontak Anda dengan desain yang rapi dan konsisten.</p>
                    </div>

                    <a href="{{ route('profile.show') }}" class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-slate-50 hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                            <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L6.414 9H16a1 1 0 110 2H6.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.8fr_1fr]">
            <div class="space-y-6">
                <div class="rounded-[32px] border border-[#dce3ec] bg-white p-8 shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                    <div class="space-y-5">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="rounded-[32px] border border-[#dce3ec] bg-white p-8 shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                    <div class="space-y-5">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[32px] border border-[#dce3ec] bg-white p-8 shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                    <div class="mt-8 rounded-3xl bg-[#fff3f5] p-5 text-sm text-slate-700">
                        <div class="mt-4">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
