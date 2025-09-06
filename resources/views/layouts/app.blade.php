<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>@yield('title','Dashboard')</title>
</head>
<body class="min-h-screen bg-gray-100 font-sans">
    <nav class="bg-brand-green fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto h-16 flex justify-between items-center">
            <img src="{{ asset('assets/clean_saver_logo.png') }}" alt="Logo" class="h-12">
            <form method="POST" action="{{ route('logout') }}">
                @csrf  
                <button class="text-xl px-3 py-1 rounded text-white cursor-pointer"> <i class="ri-logout-box-line"></i> Logout</button>
               
            </form>
        </div>
    </nav>
    <main class="py-8">
        @yield('content')
    </main>
</body>
</html>


