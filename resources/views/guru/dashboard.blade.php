<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-primary-600 text-white px-6 py-4 flex items-center justify-between shadow">
        <h1 class="font-bold text-lg">{{ config('app.name') }} &mdash; Guru</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-white text-primary-700 text-sm font-semibold px-4 py-1.5 rounded hover:bg-gray-100">
                Keluar
            </button>
        </form>
    </nav>
    <main class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Selamat datang, {{ \Illuminate\Support\Facades\Auth::user()->name }}!</h2>
        <p class="text-gray-500 mb-6">Anda login sebagai <span class="font-semibold text-green-600">Guru</span></p>

        {{-- Daftar Mata Pelajaran yang Diampu --}}
        <div class="bg-white rounded-xl shadow p-5 mb-6">
            <h3 class="font-semibold text-gray-700 mb-3">Mata Pelajaran yang Anda Ampu</h3>
            @if($mapelDiajar->count() > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($mapelDiajar as $mapel)
                    <span class="bg-green-100 text-green-700 text-sm font-medium px-3 py-1 rounded-full">
                        {{ $mapel->kode_mapel }} — {{ $mapel->nama_mapel }}
                    </span>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 italic">Belum ada mata pelajaran yang di-assign. Hubungi admin.</p>
            @endif
        </div>

        {{-- Menu Data Master (Read-Only) --}}
        <h3 class="text-base font-semibold text-gray-600 mb-3">Data Master</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('guru.data-master.kelas') }}"
               class="bg-white rounded-xl p-5 shadow hover:shadow-md transition border border-gray-200 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center text-xl">🏫</div>
                <div>
                    <p class="font-semibold text-gray-700">Daftar Kelas</p>
                    <p class="text-xs text-gray-400">Lihat data kelas aktif</p>
                </div>
            </a>
            <a href="{{ route('guru.data-master.mata-pelajaran') }}"
               class="bg-white rounded-xl p-5 shadow hover:shadow-md transition border border-gray-200 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center text-xl">📚</div>
                <div>
                    <p class="font-semibold text-gray-700">Mata Pelajaran</p>
                    <p class="text-xs text-gray-400">Lihat semua mata pelajaran</p>
                </div>
            </a>
        </div>
    </main>
</body>
</html>