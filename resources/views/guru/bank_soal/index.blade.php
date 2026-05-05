@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header halaman --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Bank Soal Saya</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 mt-1">Total: {{ $soal->total() }} soal</p>
        </div>
        <a href="{{ route('guru.bank-soal.form-import') }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Import Soal dari Excel
        </a>
    </div>

    {{-- Flash message sukses --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('guru.bank-soal.index') }}" class="flex flex-wrap gap-3 items-end">
            {{-- Filter Mata Pelajaran --}}
            <div class="flex-1 min-w-36">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 dark:text-gray-500 mb-1">Mata Pelajaran</label>
                <select name="mapel_id" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">-- Semua --</option>
                    @foreach($mapelGuru as $mapel)
                        <option value="{{ $mapel->id }}" {{ request('mapel_id') == $mapel->id ? 'selected' : '' }}>
                            {{ $mapel->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Tipe Soal --}}
            <div class="flex-1 min-w-36">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 dark:text-gray-500 mb-1">Tipe Soal</label>
                <select name="tipe_soal" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">-- Semua --</option>
                    <option value="pg"    {{ request('tipe_soal') === 'pg'    ? 'selected' : '' }}>Pilihan Ganda</option>
                    <option value="bs"    {{ request('tipe_soal') === 'bs'    ? 'selected' : '' }}>Benar / Salah</option>
                    <option value="essay" {{ request('tipe_soal') === 'essay' ? 'selected' : '' }}>Essay</option>
                </select>
            </div>

            {{-- Filter Kategori --}}
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 dark:text-gray-500 mb-1">Kategori</label>
                <select name="kategori" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">-- Semua --</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>
                            {{ $kat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                Filter
            </button>
            @if(request()->hasAny(['mapel_id','tipe_soal','kategori']))
                <a href="{{ route('guru.bank-soal.index') }}" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel soal --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($soal->isEmpty())
            <div class="text-center py-16 text-gray-400 dark:text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm">Belum ada soal. <a href="{{ route('guru.bank-soal.form-import') }}" class="text-green-600 underline">Import soal dari Excel</a></p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500 w-8">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Pertanyaan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Mapel</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Tipe</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Kategori</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Bobot</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($soal as $item)
                    <tr class="hover:bg-gray-50 dark:bg-gray-900 transition">
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $soal->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-100 max-w-xs">
                            {{ Str::limit(strip_tags($item->pertanyaan), 80) }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 dark:text-gray-500">{{ $item->mataPelajaran->nama_mapel ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($item->tipe_soal === 'pg')
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">PG</span>
                            @elseif($item->tipe_soal === 'bs')
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">B/S</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">Essay</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="text-xs">
                                <div class="font-medium text-gray-700 dark:text-gray-300">{{ $item->kategori ?? '-' }}</div>
                                <div class="text-gray-500 dark:text-gray-400 dark:text-gray-500 mt-0.5">{{ $item->sub_kategori ?? '-' }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300 font-medium">{{ $item->bobot_nilai }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($item->is_aktif)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 dark:text-gray-500">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('guru.bank-soal.show', $item) }}"
                                   class="text-green-600 hover:text-green-800 text-xs font-medium">Detail</a>
                                <a href="{{ route('guru.bank-soal.edit', $item) }}"
                                   class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                                {{-- Tombol hapus dengan konfirmasi Alpine.js --}}
                                <form method="POST" action="{{ route('guru.bank-soal.destroy', $item) }}"
                                      x-data
                                      @submit.prevent="if(confirm('Hapus soal ini? Tindakan tidak bisa dibatalkan.')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($soal->hasPages())
                <div class="px-4 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $soal->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
