<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Mapel ke Guru — ujian-mts</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-green-600 text-white px-6 py-4 flex items-center justify-between shadow">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="font-bold text-lg">ujian-mts</a>
        <span class="text-green-200">/</span>
        <span class="text-sm">Assign Mapel ke Guru</span>
    </div>
    <form method="POST" action="{{ route('logout') }}">@csrf
        <button class="text-sm bg-green-700 hover:bg-green-800 px-3 py-1 rounded">Keluar</button>
    </form>
</nav>

<div class="max-w-6xl mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Assign Mata Pelajaran ke Guru</h1>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex justify-between">
        <span>{{ session('success') }}</span>
        <button @click="show = false">×</button>
    </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">

        {{-- Form Assign --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Form Assign</h2>
            <form method="POST" action="{{ route('admin.guru-mapel.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Guru <span class="text-red-500">*</span></label>
                    <select name="user_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guru as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Mata Pelajaran <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal">(bisa pilih banyak)</span>
                    </label>
                    <p class="text-xs text-gray-400 mb-2">Pilihan ini akan menggantikan semua mapel yang sudah di-assign sebelumnya.</p>
                    <div class="border border-gray-200 rounded-lg max-h-60 overflow-y-auto divide-y divide-gray-100">
                        @foreach($allMapel as $mapel)
                        <label class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="mata_pelajaran_ids[]" value="{{ $mapel->id }}"
                                   class="text-green-600 rounded focus:ring-green-500">
                            <div>
                                <span class="text-sm text-gray-800">{{ $mapel->nama_mapel }}</span>
                                <span class="text-xs text-gray-400 ml-1">({{ $mapel->kode_mapel }})</span>
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
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="bg-green-600 text-white px-4 py-3">
                <h2 class="font-semibold">Daftar Guru & Mapelnya</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($guru as $g)
                <div class="px-4 py-3">
                    <p class="font-medium text-gray-800 text-sm">{{ $g->name }}</p>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @forelse($g->mataPelajaran as $mapel)
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">
                                {{ $mapel->kode_mapel }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-400 italic">Belum ada mapel</span>
                        @endforelse
                    </div>
                </div>
                @empty
                <div class="px-4 py-6 text-center text-gray-400 text-sm">Belum ada guru aktif.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
</body>
</html>
