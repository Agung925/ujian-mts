@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Bank Soal — Semua Guru</h1>

    {{-- Statistik per tipe soal --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-blue-700">{{ $stats['total_pg'] }}</p>
            <p class="text-sm text-blue-600 font-medium mt-1">Pilihan Ganda</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-purple-700">{{ $stats['total_bs'] }}</p>
            <p class="text-sm text-purple-600 font-medium mt-1">Benar / Salah</p>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-orange-700">{{ $stats['total_essay'] }}</p>
            <p class="text-sm text-orange-600 font-medium mt-1">Essay</p>
        </div>
    </div>

    {{-- Form filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('admin.bank-soal.index') }}" class="flex flex-wrap gap-3 items-end">
            {{-- Filter Guru --}}
            <div class="flex-1 min-w-44">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 dark:text-gray-500 mb-1">Guru</label>
                <select name="guru_id" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">-- Semua Guru --</option>
                    @foreach($semuaGuru as $guru)
                        <option value="{{ $guru->id }}" {{ request('guru_id') == $guru->id ? 'selected' : '' }}>
                            {{ $guru->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Mata Pelajaran --}}
            <div class="flex-1 min-w-44">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 dark:text-gray-500 mb-1">Mata Pelajaran</label>
                <select name="mapel_id" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">-- Semua Mapel --</option>
                    @foreach($semuaMapel as $mapel)
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

            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                Filter
            </button>
            @if(request()->hasAny(['guru_id','mapel_id','tipe_soal']))
                <a href="{{ route('admin.bank-soal.index') }}" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel soal --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">Total: <strong>{{ $soal->total() }}</strong> soal</p>
        </div>

        @if($soal->isEmpty())
            <div class="text-center py-14 text-gray-400 dark:text-gray-500 text-sm">
                Belum ada soal yang tersedia.
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500 w-8">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Pertanyaan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Mapel</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Guru</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Tipe</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Kesulitan</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-400 dark:text-gray-500">Bobot</th>
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
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 dark:text-gray-500">{{ $item->guru->name ?? '-' }}</td>
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
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $item->warna_badge_kesulitan }}">
                                {{ $item->label_kesulitan }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300 font-medium">{{ $item->bobot_nilai }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($soal->hasPages())
                <div class="px-4 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $soal->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
