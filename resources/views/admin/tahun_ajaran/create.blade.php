@extends('layouts.app')

@section('title', 'Tambah Tahun Ajaran')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Tahun Ajaran</h1>

    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.tahun-ajaran.store') }}">
            @csrf

            {{-- Nama Tahun Ajaran --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tahun Ajaran <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama" value="{{ old('nama') }}"
                       placeholder="Contoh: 2025/2026"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('nama') ? 'border-red-400' : 'border-gray-300' }}">
                @error('nama')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Semester --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Semester <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="semester" value="1"
                               {{ old('semester') == '1' ? 'checked' : '' }}
                               class="text-green-600 focus:ring-green-500">
                        <span class="text-sm">Semester 1 (Ganjil)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="semester" value="2"
                               {{ old('semester') == '2' ? 'checked' : '' }}
                               class="text-green-600 focus:ring-green-500">
                        <span class="text-sm">Semester 2 (Genap)</span>
                    </label>
                </div>
                @error('semester')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                    Simpan
                </button>
                <a href="{{ route('admin.tahun-ajaran.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
