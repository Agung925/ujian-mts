@extends('layouts.app')

@section('title', 'Data Mata Pelajaran')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mata Pelajaran</h1>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 bg-yellow-50 border border-yellow-200 px-3 py-1.5 rounded inline-block">
            ℹ️ Mode baca saja — hubungi admin untuk perubahan data
        </p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">

        {{-- Mapel Umum --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="bg-blue-600 text-white px-4 py-3">
                <h2 class="font-semibold">Mata Pelajaran Umum</h2>
                <p class="text-blue-200 text-xs">{{ $umum->count() }} mapel</p>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400 dark:text-gray-500">Kode</th>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400 dark:text-gray-500">Nama Mapel</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($umum as $mapel)
                    <tr class="hover:bg-gray-50 dark:bg-gray-900">
                        <td class="px-3 py-2 font-mono text-xs font-bold text-blue-600">{{ $mapel->kode_mapel }}</td>
                        <td class="px-3 py-2 text-gray-800 dark:text-gray-100">{{ $mapel->nama_mapel }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mapel Keagamaan --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="bg-green-700 text-white px-4 py-3">
                <h2 class="font-semibold">Mata Pelajaran Keagamaan Islam</h2>
                <p class="text-green-200 text-xs">{{ $keagamaan->count() }} mapel</p>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-green-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400 dark:text-gray-500">Kode</th>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400 dark:text-gray-500">Nama Mapel</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($keagamaan as $mapel)
                    <tr class="hover:bg-gray-50 dark:bg-gray-900">
                        <td class="px-3 py-2 font-mono text-xs font-bold text-green-700">{{ $mapel->kode_mapel }}</td>
                        <td class="px-3 py-2 text-gray-800 dark:text-gray-100">{{ $mapel->nama_mapel }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
