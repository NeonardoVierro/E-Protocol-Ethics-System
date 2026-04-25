{{-- resources/views/layouts/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Ethical Clearance System' }} | {{ $roleName ?? '' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold">Ethical Clearance System</h1>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-gray-700">{{ Auth::user()->name }}</span>
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                            {{ $roleName ?? Auth::user()->roles->first()->name ?? 'No Role' }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Login</a>
                        <a href="{{ route('register') }}" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    <main class="py-6">
        @yield('content')
    </main>
</body>
</html>