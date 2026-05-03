<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-primary-600 text-white px-6 py-4 flex items-center justify-between shadow">
        <h1 class="font-bold text-lg">{{ config('app.name') }} &mdash; Siswa</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-white text-primary-700 text-sm font-semibold px-4 py-1.5 rounded hover:bg-gray-100">
                Keluar
            </button>
        </form>
    </nav>
    <main class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat datang, {{ auth()->user()->name }}!</h2>
        <p class="text-gray-500">Anda login sebagai <span class="font-semibold text-primary-600">Siswa</span></p>
    </main>
</body>
</html>