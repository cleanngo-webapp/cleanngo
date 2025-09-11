<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>@yield('title','Dashboard')</title>
</head>
<body class="min-h-screen bg-gray-100 font-sans flex flex-col">
    <nav class="bg-brand-green fixed top-0 left-0 right-0 z-9999">
        <div class="max-w-7xl mx-auto h-16 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex gap-4">
                <img src="{{ asset('assets/clean_saver_logo.png') }}" alt="Logo" class="h-12">
            </div>
    
            <!-- Nav Links + Logout -->
            <div class="flex items-center gap-4">
                <a href="{{ route('preview.customer') }}" class="border rounded-full border-white px-3 py-2 {{ Route::currentRouteName() === 'preview.customer' ? 'bg-white text-emerald-700' : 'text-white hover:bg-white hover:text-emerald-700' }}">Overview</a>
                <a href="{{ route('customer.allservices') }}" class="border rounded-full border-white px-3 py-2 {{ Route::currentRouteName() === 'customer.allservices' ? 'bg-white text-emerald-700' : 'text-white hover:bg-white hover:text-emerald-700' }}">Services</a>
                <a href="{{ route('customer.services') }}" class="border rounded-full border-white px-3 py-2 {{ Route::currentRouteName() === 'customer.services' ? 'bg-white text-emerald-700' : 'text-white hover:bg-white hover:text-emerald-700' }}">Request an Estimate</a>
                <a href="{{ route('customer.gallery') }}" class="border rounded-full border-white px-3 py-2 {{ Route::currentRouteName() === 'customer.gallery' ? 'bg-white text-emerald-700' : 'text-white hover:bg-white hover:text-emerald-700' }}">Gallery</a>
                <a href="{{ route('preview.customer') }}#about-us" class="border rounded-full border-white px-3 py-2 text-white hover:bg-white hover:text-emerald-700">About Us</a>
                <a href="{{ route('customer.profile') }}" class="border rounded-full border-white px-3 py-2 {{ Route::currentRouteName() === 'customer.profile' ? 'bg-white text-emerald-700' : 'text-white hover:bg-white hover:text-emerald-700' }}">Profile</a>
    
                <form method="POST" action="{{ route('logout') }}" class="ml-2">
                    @csrf  
                    <button class="text-xl px-3 py-1 rounded text-white cursor-pointer hover:bg-white hover:text-emerald-700">
                        <i class="ri-logout-box-line"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>
    <main class="py-8 flex-1">
        @yield('content')
    </main>
    <footer class="bg-brand-green text-white">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-2 text-sm font-semibold">
                <div class="flex items-center gap-2"><span>📘</span><span>@cleansavernaga</span></div>
                <div class="flex items-center gap-2"><span>📸</span><span>@cleansavernaga_ph</span></div>
                <div class="flex items-center gap-2"><span>📱</span><span>(+63) 995 112 0443</span></div>
            </div>
            <div class="mt-1 text-center text-xs opacity-90">
                <span>📍</span>
                <span>0772 Maescoy Compound, San Felipe Naga City</span>
            </div>
        </div>
    </footer>
</body>
@stack('scripts')
</html>


