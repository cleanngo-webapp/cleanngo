<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>@yield('title', 'Auth')</title>
</head>
<body class="min-h-screen bg-gradient-to-b from-emerald-300 to-green-900">
    @yield('content')
</body>
</html>


