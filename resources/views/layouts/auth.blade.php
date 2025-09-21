<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>@yield('title', 'Auth')</title>
</head>
<body class="min-h-screen bg-gradient-to-b from-emerald-300 to-green-900 font-sans">
    @yield('content')
    @stack('scripts')
</body>
</html>


