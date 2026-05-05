@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Edit Kelas {{ $kelas->nama_kelas }}</h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.kelas.update', $kelas) }}">
            @csrf @method('PUT')

            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg text-sm text-gray-600 dark:text-gray-400 dark:text-gray-500">
                Tahun Ajaran: <strong>{{ $kelas->tahunAjaran->label }}</strong>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Tahun ajaran tidak bisa diubah.</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tingkat <span class="text-red-500">*</span></label>
                <select name="tingkat" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    @foreach(['VII', 'VIII', 'IX'] as $t)
                        <option value="{{ $t }}" {{ old('tingkat', $kelas->tingkat) == $t ? 'selected' : '' }}>Kelas {{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="is_aktif" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    <option value="1" {{ old('is_aktif', $kelas->is_aktif) ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !old('is_aktif', $kelas->is_aktif) ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium">Perbarui</button>
                <a href="{{ route('admin.kelas.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg text-sm font-medium">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
