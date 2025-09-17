<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>@yield('title','Employee')</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-emerald-100 font-sans">
    <header class="h-12 md:h-14 bg-emerald-900 text-white flex items-center justify-between px-4 fixed top-0 left-0 right-0 z-20">
        <div class="flex items-center gap-4">
                <img src="{{ asset('assets/clean_saver_logo.png') }}" alt="Logo" class="h-12">
                <span class="font-semibold">CLEANSAVER NAGA</span>
        </div>
        <div class="flex items-center gap-4 text-white">
            <a href="{{ route('employee.notifications') }}" class="hover:text-emerald-700 cursor-pointer"><i class="ri-notification-3-line"></i></a>
            <a href="{{ route('employee.profile.show') }}" class="hover:text-emerald-700 cursor-pointer"><i class="ri-user-3-fill"></i></a>
        </div>
    </header>
    <div class="flex">
        <aside class="w-56 bg-emerald-700 text-white fixed left-0 top-14 h-[calc(100vh-3.5rem)] overflow-y-auto z-10">
            <div class="bg-brand-green flex items-center gap-2 px-4 py-4 w-full">
                <i class="ri-user-line"></i>
                <span class=" text-white font-semibold">Hi, {{ auth()->user()->first_name }}!</span>
            </div>
            <nav class="mt-2 px-2 space-y-1">
                <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('employee.dashboard') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-home-3-line"></i> <span>Dashboard</span></a>
                <a href="{{ route('employee.jobs') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('employee.jobs') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-briefcase-2-line"></i> <span>My Jobs</span></a>
                <a href="{{ route('employee.payroll') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('employee.payroll') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-money-dollar-circle-line"></i> <span>Payroll</span></a>
                <a href="{{ route('employee.notifications') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('employee.notifications') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-notification-3-line"></i> <span>Notifications</span></a>
                <a href="{{ route('employee.profile.show') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('employee.profile.*') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-user-3-fill"></i> <span>My Profile</span></a>
                <a href="{{ route('employee.settings') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('employee.settings') ? 'bg-white/10 font-semibold' : '' }}"><i class="ri-settings-3-line"></i> <span>Settings</span></a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors"><i class="ri-logout-box-line"></i> <span>Logout</span></button>
                </form>
            </nav>
        </aside>
        <main class="flex-1 p-6 ml-56 mt-14">
            @yield('content')
        </main>
    </div>
</body>
@stack('scripts')
</html>


