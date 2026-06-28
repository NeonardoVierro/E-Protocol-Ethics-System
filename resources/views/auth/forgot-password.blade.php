<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EthicsClear') }} - Forgot Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="min-h-screen bg-[#eef5ff] font-sans text-[#0f172a]">
    <main class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-6 flex justify-start">
                <a href="{{ route('login') }}" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#c3c6d4] bg-white text-[#0f172a] shadow-sm transition hover:border-[#94a3b8] hover:bg-[#f8fafc]">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
            </div>
            <div class="rounded-[32px] border border-[#dbeafe] bg-white/95 p-8 shadow-[0_20px_60px_-35px_rgba(15,23,42,0.4)]">
                <div class="mb-8 text-center">
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-[18px] bg-[#0f3e8e] text-white shadow-md">
                        <span class="material-symbols-outlined text-3xl">lock_reset</span>
                    </div>
                    <h1 class="text-xl font-semibold text-[#0f172a]">Forgot Password</h1>
                    <p class="mt-2 text-sm text-[#64748b]">Submit your email to receive a password reset link.</p>
                </div>

                @if (session('status'))
                    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 p-4 text-sm text-emerald-700 mb-6">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="rounded-2xl bg-rose-50 border border-rose-100 p-4 text-sm text-rose-700 mb-6">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-2">
                        <label class="block text-[10px] font-semibold uppercase tracking-[0.25em] text-[#475569]" for="email">Email</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#94a3b8]">mail</span>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                class="w-full rounded-[16px] border border-[#cbd5e1] bg-[#f8fafc] py-3 pl-12 pr-4 text-sm text-[#0f172a] outline-none transition focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20" placeholder="your@email.com" />
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-[16px] bg-[#003178] py-3 text-sm font-semibold text-white transition hover:bg-[#002359]">Send Reset Link</button>

                </form>
            </div>
        </div>
    </main>
</body>
</html>
