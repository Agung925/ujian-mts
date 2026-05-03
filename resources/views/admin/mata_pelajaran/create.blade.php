<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mata Pelajaran — ujian-mts</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-green-600 text-white px-6 py-4 flex items-center justify-between shadow">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="font-bold text-lg">ujian-mts</a>
        <span class="text-green-200">/</span>
        <a href="{{ route('admin.mata-pelajaran.index') }}" class="text-green-200 hover:text-white text-sm">Mata Pelajaran</a>
        <span class="text-green-200">/</span>
        <span class="text-sm">Tambah</span>
    </div>
    <form method="POST" action="{{ route('logout') }}">@csrf
        <button class="text-sm bg-green-700 hover:bg-green-800 px-3 py-1 rounded">Keluar</button>
    </form>
</nav>

<div class="max-w-lg mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Mata Pelajaran</h1>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.mata-pelajaran.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mapel <span class="text-red-500">*</span></label>
                <input type="text" name="nama_mapel" value="{{ old('nama_mapel') }}"
                       placeholder="Contoh: Matematika"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 {{ $errors->has('nama_mapel') ? 'border-red-400' : 'border-gray-300' }}">
                @error('nama_mapel')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Mapel <span class="text-red-500">*</span></label>
                <input type="text" name="kode_mapel" value="{{ old('kode_mapel') }}"
                       placeholder="Contoh: MTK" maxlength="10"
                       class="w-full border rounded-lg px-3 py-2 text-sm font-mono uppercase focus:ring-2 focus:ring-green-500 {{ $errors->has('kode_mapel') ? 'border-red-400' : 'border-gray-300' }}">
                <p class="text-gray-400 text-xs mt-1">Kode singkat, maks 10 karakter. Harus unik.</p>
                @error('kode_mapel')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis <span class="text-red-500">*</span></label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jenis" value="umum" {{ old('jenis') == 'umum' ? 'checked' : '' }}
                               class="text-green-600 focus:ring-green-500">
                        <span class="text-sm">Umum</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jenis" value="keagamaan" {{ old('jenis') == 'keagamaan' ? 'checked' : '' }}
                               class="text-green-600 focus:ring-green-500">
                        <span class="text-sm">Keagamaan Islam</span>
                    </label>
                </div>
                @error('jenis')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium">Simpan</button>
                <a href="{{ route('admin.mata-pelajaran.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
