<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EthicsClear') }} - Register</title>
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
        <div class="w-full max-w-2xl space-y-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('peneliti.dashboard') }}" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#c3c6d4] bg-white text-[#0f172a] shadow-sm transition hover:border-[#94a3b8] hover:bg-[#f8fafc]">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
            </div>
            <div class="rounded-[28px] border border-[#dbeafe] bg-white p-8 shadow-[0_20px_60px_-35px_rgba(15,23,42,0.4)]">
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-semibold text-[#0f172a]">Create Your Account</h1>
                    <p class="mt-3 text-sm text-[#64748b]">Join the EthicsPortal for institutional clearance and compliance management.</p>
                </div>

                @if ($errors->any())
                    <div class="rounded-2xl bg-rose-50 border border-rose-100 p-4 text-sm text-rose-700 mb-6">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#475569]" for="name">Full name</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                                class="w-full h-11 rounded-[14px] border border-[#c3c6d4] bg-[#f8fafc] px-4 text-sm text-[#0f172a] outline-none transition focus:border-[#003178] focus:ring-2 focus:ring-[#003178]/20" placeholder="Entry Your Name" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#475569]" for="email">Institutional email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                class="w-full h-11 rounded-[14px] border border-[#c3c6d4] bg-[#f8fafc] px-4 text-sm text-[#0f172a] outline-none transition focus:border-[#003178] focus:ring-2 focus:ring-[#003178]/20" placeholder="nama@university.edu" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#475569]" for="department">Department / Faculty</label>
                            <input id="department" name="department" type="text" value="{{ old('department') }}"
                                class="w-full h-11 rounded-[14px] border border-[#c3c6d4] bg-[#f8fafc] px-4 text-sm text-[#0f172a] outline-none transition focus:border-[#003178] focus:ring-2 focus:ring-[#003178]/20" placeholder="Faculty of Science" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#475569]" for="institution">Institution</label>
                            <input id="institution" name="institution" type="text" value="{{ old('institution') }}"
                                class="w-full h-11 rounded-[14px] border border-[#c3c6d4] bg-[#f8fafc] px-4 text-sm text-[#0f172a] outline-none transition focus:border-[#003178] focus:ring-2 focus:ring-[#003178]/20" placeholder="University name" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#475569]" for="password">Password</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" required
                                    class="w-full h-11 rounded-[14px] border border-[#c3c6d4] bg-[#f8fafc] px-4 pr-12 text-sm text-[#0f172a] outline-none transition focus:border-[#003178] focus:ring-2 focus:ring-[#003178]/20" placeholder="Entry your password" />
                                <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#64748b] focus:outline-none">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[#475569]" for="password_confirmation">Confirm password</label>
                            <div class="relative">
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    class="w-full h-11 rounded-[14px] border border-[#c3c6d4] bg-[#f8fafc] px-4 pr-12 text-sm text-[#0f172a] outline-none transition focus:border-[#003178] focus:ring-2 focus:ring-[#003178]/20" placeholder="Entry your password" />
                                <button type="button" id="togglePasswordConfirmation" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#64748b] focus:outline-none">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <input id="terms" name="terms" type="checkbox" class="mt-1 h-4 w-4 rounded border-[#c3c6d4] text-[#003178] focus:ring-[#003178]" />
                        <label for="terms" class="text-sm text-[#475569]">I agree to the Terms of Service and Privacy Policy</label>
                    </div>

                    <button type="submit" class="w-full h-12 rounded-[16px] bg-[#003178] text-sm font-semibold text-white transition hover:bg-[#002359]">Create Account</button>

                    <div class="text-center text-sm text-[#475569]">
                        <a href="{{ route('login') }}" class="font-semibold text-[#003178] hover:underline">Already have an account? Sign in</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('span').textContent = type === 'password' ? 'visibility' : 'visibility_off';
            });
        }

        if (togglePasswordConfirmation && passwordConfirmationInput) {
            togglePasswordConfirmation.addEventListener('click', function () {
                const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmationInput.setAttribute('type', type);
                this.querySelector('span').textContent = type === 'password' ? 'visibility' : 'visibility_off';
            });
        }
    </script>
</body>
</html>
