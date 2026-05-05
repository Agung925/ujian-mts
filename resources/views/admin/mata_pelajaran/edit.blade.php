@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Edit Mata Pelajaran</h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.mata-pelajaran.update', $mataPelajaran) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Mapel <span class="text-red-500">*</span></label>
                <input type="text" name="nama_mapel" value="{{ old('nama_mapel', $mataPelajaran->nama_mapel) }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 {{ $errors->has('nama_mapel') ? 'border-red-400' : 'border-gray-300' }}">
                @error('nama_mapel')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Mapel <span class="text-red-500">*</span></label>
                <input type="text" name="kode_mapel" value="{{ old('kode_mapel', $mataPelajaran->kode_mapel) }}"
                       maxlength="10"
                       class="w-full border rounded-lg px-3 py-2 text-sm font-mono uppercase focus:ring-2 focus:ring-green-500 {{ $errors->has('kode_mapel') ? 'border-red-400' : 'border-gray-300' }}">
                @error('kode_mapel')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jenis <span class="text-red-500">*</span></label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jenis" value="umum"
                               {{ old('jenis', $mataPelajaran->jenis) == 'umum' ? 'checked' : '' }}
                               class="text-green-600 focus:ring-green-500">
                        <span class="text-sm">Umum</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jenis" value="keagamaan"
                               {{ old('jenis', $mataPelajaran->jenis) == 'keagamaan' ? 'checked' : '' }}
                               class="text-green-600 focus:ring-green-500">
                        <span class="text-sm">Keagamaan Islam</span>
                    </label>
                </div>
                @error('jenis')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="is_aktif" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    <option value="1" {{ old('is_aktif', $mataPelajaran->is_aktif) ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !old('is_aktif', $mataPelajaran->is_aktif) ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium">Perbarui</button>
                <a href="{{ route('admin.mata-pelajaran.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg text-sm font-medium">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
