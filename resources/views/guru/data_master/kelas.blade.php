<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kelas — ujian-mts</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-green-600 text-white px-6 py-4 flex items-center justify-between shadow">
    <div class="flex items-center gap-3">
        <a href="{{ route('guru.dashboard') }}" class="font-bold text-lg">ujian-mts</a>
        <span class="text-green-200">/</span>
        <span class="text-sm">Data Master</span>
        <span class="text-green-200">/</span>
        <span class="text-sm">Kelas</span>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('guru.data-master.mata-pelajaran') }}" class="text-sm text-green-200 hover:text-white">Mata Pelajaran</a>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="text-sm bg-green-700 hover:bg-green-800 px-3 py-1 rounded">Keluar</button>
        </form>
    </div>
</nav>

<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Kelas</h1>
        @if($tahunAktif)
            <p class="text-sm text-gray-500 mt-1">Tahun Ajaran Aktif: <span class="font-medium text-green-700">{{ $tahunAktif->label }}</span></p>
        @endif
        <p class="text-xs text-gray-400 mt-2 bg-yellow-50 border border-yellow-200 px-3 py-1.5 rounded inline-block">
            ℹ️ Mode baca saja — hubungi admin untuk perubahan data kelas
        </p>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-green-600 text-white">
                <tr>
                    <th class="px-4 py-3 text-left w-8">No</th>
                    <th class="px-4 py-3 text-left">Nama Kelas</th>
                    <th class="px-4 py-3 text-left">Tingkat</th>
                    <th class="px-4 py-3 text-center">Jumlah Siswa</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($kelas as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800">{{ $item->nama_kelas }}</td>
                    <td class="px-4 py-3 text-gray-600">Kelas {{ $item->tingkat }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded-full">
                            {{ $item->siswa->count() }} siswa
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($item->is_aktif)
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Aktif</span>
                        @else
                            <span class="bg-gray-100 text-gray-400 text-xs px-2 py-1 rounded-full">Nonaktif</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada kelas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
