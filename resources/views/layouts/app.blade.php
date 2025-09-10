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
            <div class="flex items-center gap-4">
                <img src="{{ asset('assets/clean_saver_logo.png') }}" alt="Logo" class="h-12">
                <a href="{{ route('preview.customer') }}" class="border rounded-full border-white px-2 py-2 text-white hover:bg-white/10 hover:text-emerald-700">Overview</a>
                <a href="{{ route('customer.services') }}" class="border rounded-full border-white px-2 py-2 text-white hover:bg-white/10 hover:text-emerald-700">Request an Estimate</a>
                <a href="{{ route('customer.profile') }}" class="border rounded-full border-white px-2 py-2 text-white hover:bg-white/10 hover:text-emerald-700">Profile</a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf  
                <button class="text-xl px-3 py-1 rounded text-white cursor-pointer hover:bg-white/10 hover:text-emerald-700"> <i class="ri-logout-box-line"></i> Logout</button>
               
            </form>
        </div>
    </nav>
    <main class="py-8">
        @yield('content')
    </main>
</body>
@stack('scripts')
</html>


