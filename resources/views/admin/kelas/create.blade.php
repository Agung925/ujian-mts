@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Tambah Kelas</h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.kelas.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Ajaran <span class="text-red-500">*</span></label>
                <select name="tahun_ajaran_id"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 {{ $errors->has('tahun_ajaran_id') ? 'border-red-400' : 'border-gray-300' }}">
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    @foreach($tahunAjaran as $ta)
                        <option value="{{ $ta->id }}" {{ old('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                            {{ $ta->label }} {{ $ta->is_aktif ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('tahun_ajaran_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tingkat <span class="text-red-500">*</span></label>
                <select name="tingkat"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 {{ $errors->has('tingkat') ? 'border-red-400' : 'border-gray-300' }}">
                    <option value="">-- Pilih Tingkat --</option>
                    @foreach(['VII', 'VIII', 'IX'] as $t)
                        <option value="{{ $t }}" {{ old('tingkat') == $t ? 'selected' : '' }}>Kelas {{ $t }}</option>
                    @endforeach
                </select>
                @error('tingkat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kelas" value="{{ old('nama_kelas') }}"
                       placeholder="Contoh: VII-A"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 {{ $errors->has('nama_kelas') ? 'border-red-400' : 'border-gray-300' }}">
                @error('nama_kelas')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium">Simpan</button>
                <a href="{{ route('admin.kelas.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg text-sm font-medium">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
