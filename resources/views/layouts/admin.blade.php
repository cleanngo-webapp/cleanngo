<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>@yield('title','Admin')</title>
    {{-- Tailwind classes are applied inline on links for reliability --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-emerald-100 font-sans">
    <header class="h-12 md:h-14 bg-emerald-900 text-white flex items-center justify-between px-4">
        <div class="flex items-center gap-4">
            <img src="{{ asset('assets/clean_saver_logo.png') }}" alt="Logo" class="h-12">
            <span class="font-semibold justify-center">CLEANSAVER NAGA</span>
        </div>
        <div class="flex items-center gap-4 text-white">
             <a class="hover:text-emerald-700 cursor-pointer"><i class="ri-notification-3-line"></i></a>
        </div>
    </header>
    <div class="flex">
        <aside class="w-56 bg-emerald-700 text-white min-h-[calc(100vh-3.5rem)]">
            <div class="bg-brand-green flex items-center gap-2 px-4 py-4 w-full">
                <i class="ri-admin-line"></i>
                <span class="text-white font-semibold">Hi, {{ auth()->user()->first_name }}!</span>
            </div>
            <nav class="px-2 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-800/50 cursor-pointer transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-home-3-line"></i> <span>Dashboard</span></a>
                <a href="{{ route('admin.bookings') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-800/50 cursor-pointer transition-colors {{ request()->routeIs('admin.bookings') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-calendar-2-line"></i> <span>Bookings</span></a>
                <a href="{{ route('admin.employees') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-800/50 cursor-pointer transition-colors {{ request()->routeIs('admin.employees') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-team-line"></i> <span>Employees</span></a>
                <a href="{{ route('admin.payroll') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-800/50 cursor-pointer transition-colors {{ request()->routeIs('admin.payroll') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-money-dollar-circle-line"></i> <span>Payroll</span></a>
                <a href="{{ route('admin.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-800/50 cursor-pointer transition-colors {{ request()->routeIs('admin.inventory') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-archive-2-line"></i> <span>Inventory</span></a>
                <a href="{{ route('admin.customers') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-800/50 cursor-pointer transition-colors {{ request()->routeIs('admin.customers') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-user-star-line"></i> <span>Customers</span></a>
                <a href="{{ route('admin.gallery') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-800/50 cursor-pointer transition-colors {{ request()->routeIs('admin.gallery') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-image-2-line"></i> <span>Gallery</span></a>
                <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-800/50 cursor-pointer transition-colors {{ request()->routeIs('admin.settings') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-settings-3-line"></i> <span>Settings</span></a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left flex items-center gap-2 px-3 py-2 rounded hover:bg-emerald-800/50 cursor-pointer transition-colors"><i class="ri-logout-box-line"></i> <span>Logout</span></button>
                </form>
            </nav>
        </aside>
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</body>
@stack('scripts')
</html>


