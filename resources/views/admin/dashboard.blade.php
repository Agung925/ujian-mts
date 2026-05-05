@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <main class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Selamat datang, {{ \Illuminate\Support\Facades\Auth::user()->name }}!</h2>
        <p class="text-gray-500 dark:text-gray-400 dark:text-gray-500 mb-1">Anda login sebagai <span class="font-semibold text-green-600">Super Admin</span></p>
        @if($tahunAktif)
        <p class="text-sm text-green-600 mb-6">Tahun Ajaran Aktif: <strong>{{ $tahunAktif->label }}</strong></p>
        @else
        <p class="text-sm text-red-500 mb-6">⚠️ Belum ada tahun ajaran yang aktif</p>
        @endif

        {{-- Kartu Statistik --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow border border-gray-100 dark:border-gray-700 text-center">
                <p class="text-3xl font-bold text-green-600">{{ $stats['total_guru'] }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 mt-1">Guru Aktif</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow border border-gray-100 dark:border-gray-700 text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_siswa'] }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 mt-1">Siswa Aktif</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow border border-gray-100 dark:border-gray-700 text-center">
                <p class="text-3xl font-bold text-orange-500">{{ $stats['total_kelas'] }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 mt-1">Kelas Aktif</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow border border-gray-100 dark:border-gray-700 text-center">
                <p class="text-3xl font-bold text-purple-600">{{ $stats['total_mapel'] }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 mt-1">Mata Pelajaran</p>
            </div>
        </div>

        {{-- Menu Navigasi --}}
        <h3 class="text-base font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500 mb-3">Menu</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Manajemen User --}}
            <a href="{{ route('admin.users.index') }}"
               class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow hover:shadow-md transition border border-gray-200 dark:border-gray-700 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-xl">👥</div>
                <div>
                    <p class="font-semibold text-gray-700 dark:text-gray-300">Manajemen User</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Kelola akun guru dan siswa</p>
                </div>
            </a>

            {{-- Tahun Ajaran --}}
            <a href="{{ route('admin.tahun-ajaran.index') }}"
               class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow hover:shadow-md transition border border-gray-200 dark:border-gray-700 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-xl">📅</div>
                <div>
                    <p class="font-semibold text-gray-700 dark:text-gray-300">Tahun Ajaran</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Atur periode belajar aktif</p>
                </div>
            </a>

            {{-- Mata Pelajaran --}}
            <a href="{{ route('admin.mata-pelajaran.index') }}"
               class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow hover:shadow-md transition border border-gray-200 dark:border-gray-700 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center text-xl">📚</div>
                <div>
                    <p class="font-semibold text-gray-700 dark:text-gray-300">Mata Pelajaran</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Umum & keagamaan Islam</p>
                </div>
            </a>

            {{-- Kelas --}}
            <a href="{{ route('admin.kelas.index') }}"
               class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow hover:shadow-md transition border border-gray-200 dark:border-gray-700 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center text-xl">🏫</div>
                <div>
                    <p class="font-semibold text-gray-700 dark:text-gray-300">Kelas</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">VII, VIII, IX — assign siswa</p>
                </div>
            </a>

            {{-- Guru Mapel --}}
            <a href="{{ route('admin.guru-mapel.index') }}"
               class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow hover:shadow-md transition border border-gray-200 dark:border-gray-700 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-xl">🎓</div>
                <div>
                    <p class="font-semibold text-gray-700 dark:text-gray-300">Assign Mapel ke Guru</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Tentukan guru pengampu</p>
                </div>
            </a>

            {{-- Bank Soal --}}
            <a href="{{ route('admin.bank-soal.index') }}"
               class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow hover:shadow-md transition border border-gray-200 dark:border-gray-700 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center text-xl">📝</div>
                <div>
                    <p class="font-semibold text-gray-700 dark:text-gray-300">Bank Soal</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Monitor semua soal dari guru</p>
                </div>
            </a>

        </div>
    </main>
@endsection
