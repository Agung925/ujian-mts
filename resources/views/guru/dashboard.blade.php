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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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

        {{-- Bank Soal — Statistik & Aksi Cepat --}}
        <h3 class="text-base font-semibold text-gray-600 mb-3">Bank Soal Saya</h3>
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-blue-700">{{ $statsSoal['pg'] }}</p>
                <p class="text-xs text-blue-600 font-medium mt-1">Pilihan Ganda</p>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-purple-700">{{ $statsSoal['bs'] }}</p>
                <p class="text-xs text-purple-600 font-medium mt-1">Benar / Salah</p>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-orange-700">{{ $statsSoal['essay'] }}</p>
                <p class="text-xs text-orange-600 font-medium mt-1">Essay</p>
            </div>
        </div>
        <div class="flex gap-3 mb-8">
            <a href="{{ route('guru.bank-soal.index') }}"
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                📋 Bank Soal Saya
            </a>
            <a href="{{ route('guru.bank-soal.create') }}"
               class="bg-white hover:bg-green-50 border border-green-600 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                ✚ Tambah Soal Baru
            </a>
        </div>

        {{-- Ujian — Statistik & Aksi Cepat --}}
        <h3 class="text-base font-semibold text-gray-600 mb-3">Ujian Saya</h3>
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-yellow-700">{{ $statsUjian['draft'] }}</p>
                <p class="text-xs text-yellow-600 font-medium mt-1">Draft</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-green-700">{{ $statsUjian['aktif'] }}</p>
                <p class="text-xs text-green-600 font-medium mt-1">Aktif / Berlangsung</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-blue-700">{{ $statsUjian['selesai'] }}</p>
                <p class="text-xs text-blue-600 font-medium mt-1">Selesai</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('guru.ujian.index') }}"
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                📝 Lihat Semua Ujian
            </a>
            <a href="{{ route('guru.ujian.create') }}"
               class="bg-white hover:bg-green-50 border border-green-600 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                ✚ Buat Ujian Baru
            </a>
        </div>
    </main>
</body>
</html>