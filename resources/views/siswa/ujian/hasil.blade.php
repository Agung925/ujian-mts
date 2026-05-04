<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Ujian — {{ $sesi->ujian->judul }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-green-600 text-white px-6 py-4 flex items-center justify-between shadow">
        <h1 class="font-bold text-lg">{{ config('app.name') }}</h1>
        <a href="{{ route('siswa.dashboard') }}" class="bg-white text-green-700 text-sm font-semibold px-4 py-1.5 rounded hover:bg-gray-100">
            ← Dashboard
        </a>
    </nav>

    <div class="max-w-2xl mx-auto px-4 py-8">

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- Kartu hasil utama --}}
        <div class="bg-white rounded-2xl shadow border border-gray-200 p-8 text-center mb-6">
            <div class="text-5xl mb-3">
                @if($sesi->nilai_akhir >= 75) 🎉 @elseif($sesi->nilai_akhir >= 60) 😊 @else 😔 @endif
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-1">Ujian Selesai!</h2>
            <p class="text-gray-500 text-sm mb-5">{{ $sesi->ujian->judul }}</p>

            @if($sesi->nilai_akhir !== null)
                <div class="inline-block">
                    <p class="text-xs text-gray-400 mb-1">Nilai Akhir</p>
                    <p class="text-6xl font-extrabold {{ $sesi->nilai_akhir >= 75 ? 'text-green-600' : ($sesi->nilai_akhir >= 60 ? 'text-yellow-500' : 'text-red-500') }}">
                        {{ number_format($sesi->nilai_akhir, 1) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">dari 100</p>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-700">
                    Nilaimu sedang diproses/menunggu koreksi guru untuk soal essay.
                </div>
            @endif
        </div>

        {{-- Info tambahan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Mata Pelajaran</p>
                <p class="font-medium text-gray-700 mt-0.5">{{ $sesi->ujian->mataPelajaran->nama_mapel ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Kelas</p>
                <p class="font-medium text-gray-700 mt-0.5">{{ $sesi->ujian->kelas->nama_kelas ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Mulai</p>
                <p class="font-medium text-gray-700 mt-0.5">{{ $sesi->waktu_mulai?->format('d M Y H:i') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Selesai</p>
                <p class="font-medium text-gray-700 mt-0.5">{{ $sesi->waktu_selesai?->format('d M Y H:i') ?? '-' }}</p>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('siswa.dashboard') }}"
               class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-xl text-sm transition">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
