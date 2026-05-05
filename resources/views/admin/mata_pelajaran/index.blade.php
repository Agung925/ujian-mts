@extends('layouts.app')

@section('title', 'Daftar Mata Pelajaran')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mata Pelajaran</h1>
        <a href="{{ route('admin.mata-pelajaran.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            + Tambah Mapel
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex justify-between">
        <span>{{ session('success') }}</span>
        <button @click="show = false" class="font-bold text-green-600">×</button>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

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
                        <th class="px-3 py-2 text-center text-gray-600 dark:text-gray-400 dark:text-gray-500">Status</th>
                        <th class="px-3 py-2 text-center text-gray-600 dark:text-gray-400 dark:text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($umum as $mapel)
                    <tr class="hover:bg-gray-50 dark:bg-gray-900">
                        <td class="px-3 py-2 font-mono text-xs font-bold text-blue-600">{{ $mapel->kode_mapel }}</td>
                        <td class="px-3 py-2 text-gray-800 dark:text-gray-100">{{ $mapel->nama_mapel }}</td>
                        <td class="px-3 py-2 text-center">
                            @if($mapel->is_aktif)
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Aktif</span>
                            @else
                                <span class="bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 dark:text-gray-500 text-xs px-2 py-0.5 rounded-full">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex gap-1 justify-center">
                                <a href="{{ route('admin.mata-pelajaran.edit', $mapel) }}"
                                   class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-0.5 rounded">Edit</a>
                                <form method="POST" action="{{ route('admin.mata-pelajaran.destroy', $mapel) }}">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Hapus {{ $mapel->nama_mapel }}?')"
                                            class="text-xs bg-red-500 hover:bg-red-600 text-white px-2 py-0.5 rounded">Hapus</button>
                                </form>
                            </div>
                        </td>
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
                        <th class="px-3 py-2 text-center text-gray-600 dark:text-gray-400 dark:text-gray-500">Status</th>
                        <th class="px-3 py-2 text-center text-gray-600 dark:text-gray-400 dark:text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($keagamaan as $mapel)
                    <tr class="hover:bg-gray-50 dark:bg-gray-900">
                        <td class="px-3 py-2 font-mono text-xs font-bold text-green-700">{{ $mapel->kode_mapel }}</td>
                        <td class="px-3 py-2 text-gray-800 dark:text-gray-100">{{ $mapel->nama_mapel }}</td>
                        <td class="px-3 py-2 text-center">
                            @if($mapel->is_aktif)
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Aktif</span>
                            @else
                                <span class="bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 dark:text-gray-500 text-xs px-2 py-0.5 rounded-full">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex gap-1 justify-center">
                                <a href="{{ route('admin.mata-pelajaran.edit', $mapel) }}"
                                   class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-0.5 rounded">Edit</a>
                                <form method="POST" action="{{ route('admin.mata-pelajaran.destroy', $mapel) }}">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Hapus {{ $mapel->nama_mapel }}?')"
                                            class="text-xs bg-red-500 hover:bg-red-600 text-white px-2 py-0.5 rounded">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
