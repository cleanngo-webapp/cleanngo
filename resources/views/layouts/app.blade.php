<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>@yield('title','Dashboard')</title>
</head>
<body class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="font-bold">Clean N' Go</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm px-3 py-1 rounded bg-gray-800 text-white">Logout</button>
            </form>
        </div>
    </nav>
    <main class="py-8">
        @yield('content')
    </main>
</body>
</html>


