<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian Selesai</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-green-600 text-white px-6 py-4 flex items-center justify-between shadow">
        <h1 class="font-bold text-lg">{{ config('app.name') }}</h1>
        <a href="{{ route('siswa.dashboard') }}" class="bg-white text-green-700 text-sm font-semibold px-4 py-1.5 rounded hover:bg-gray-100">
            ← Dashboard
        </a>
    </nav>

    <div class="max-w-lg mx-auto px-4 py-16 text-center">

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="text-6xl mb-4">✅</div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Ujian Telah Dikumpulkan!</h2>
        <p class="text-gray-500 text-sm mb-1">{{ $sesi->ujian->judul }}</p>
        <p class="text-gray-400 text-xs mb-8">
            {{ $sesi->ujian->mataPelajaran->nama_mapel ?? '' }}
            &bull; Selesai pukul {{ $sesi->waktu_selesai?->format('H:i') ?? '-' }}
        </p>

        <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-5 text-sm text-blue-800 mb-8">
            <p class="font-semibold mb-1">📋 Hasil ujian akan diumumkan oleh guru.</p>
            <p class="text-xs text-blue-600">Nilai dan pembahasan hanya dapat dilihat oleh guru yang bersangkutan.</p>
        </div>

        <a href="{{ route('siswa.dashboard') }}"
           class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-xl text-sm transition">
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>