@extends('layouts.app')

@section('title', 'Assign Mapel ke Guru')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Assign Mata Pelajaran ke Guru</h1>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex justify-between">
        <span>{{ session('success') }}</span>
        <button @click="show = false">×</button>
    </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">

        {{-- Form Assign --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 dark:text-gray-300 mb-4">Form Assign</h2>
            <form method="POST" action="{{ route('admin.guru-mapel.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Guru <span class="text-red-500">*</span></label>
                    <select name="user_id"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guru as $g)
                            <option value="{{ $g->id }}" {{ (old('user_id', $selectedGuruId) == $g->id) ? 'selected' : '' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih Mata Pelajaran <span class="text-red-500">*</span>
                        <span class="text-gray-400 dark:text-gray-500 font-normal">(bisa pilih banyak)</span>
                    </label>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-2">Pilihan ini akan menggantikan semua mapel yang sudah di-assign sebelumnya.</p>
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg max-h-60 overflow-y-auto divide-y divide-gray-100">
                        @foreach($allMapel as $mapel)
                        <label class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 dark:bg-gray-900 cursor-pointer">
                            <input type="checkbox" name="mata_pelajaran_ids[]" value="{{ $mapel->id }}"
                                   class="text-green-600 rounded focus:ring-green-500"
                                   {{ in_array($mapel->id, old('mata_pelajaran_ids', $selectedMapelIds)) ? 'checked' : '' }}>
                            <div>
                                <span class="text-sm text-gray-800 dark:text-gray-100">{{ $mapel->nama_mapel }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">({{ $mapel->kode_mapel }})</span>
                                @if($mapel->jenis == 'keagamaan')
                                    <span class="text-xs text-green-600 ml-1">• Keagamaan</span>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('mata_pelajaran_ids')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm font-medium">
                    Simpan Assign
                </button>
            </form>
        </div>

        {{-- Daftar Guru + Mapel yang Diajar --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="bg-green-600 text-white px-4 py-3">
                <h2 class="font-semibold">Daftar Guru & Mapelnya</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($guru as $g)
                <div class="px-4 py-3 flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 dark:text-gray-100 text-sm">{{ $g->name }}</p>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @forelse($g->mataPelajaran as $mapel)
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">
                                    {{ $mapel->kode_mapel }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ada mapel</span>
                            @endforelse
                        </div>
                    </div>
                    {{-- Tombol Edit: arahkan ke form dengan pre-fill guru ini --}}
                    <a href="{{ route('admin.guru-mapel.index', ['guru_id' => $g->id]) }}"
                       class="shrink-0 text-xs bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 px-2 py-1 rounded">
                        Edit
                    </a>
                </div>
                @empty
                <div class="px-4 py-6 text-center text-gray-400 dark:text-gray-500 text-sm">Belum ada guru aktif.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
