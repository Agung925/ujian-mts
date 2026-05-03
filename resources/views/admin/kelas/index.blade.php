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
        <a href="{{ route('admin.dashboard') }}" class="font-bold text-lg">ujian-mts</a>
        <span class="text-green-200">/</span>
        <span class="text-sm">Kelas</span>
    </div>
    <form method="POST" action="{{ route('logout') }}">@csrf
        <button class="text-sm bg-green-700 hover:bg-green-800 px-3 py-1 rounded">Keluar</button>
    </form>
</nav>

<div class="max-w-5xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Kelas</h1>
            @if($tahunAktif)
                <p class="text-sm text-gray-500 mt-1">Tahun Ajaran: <span class="font-medium text-green-700">{{ $tahunAktif->label }}</span></p>
            @else
                <p class="text-sm text-red-500 mt-1">Belum ada tahun ajaran aktif.</p>
            @endif
        </div>
        <a href="{{ route('admin.kelas.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            + Tambah Kelas
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex justify-between">
        <span>{{ session('success') }}</span>
        <button @click="show = false" class="font-bold">×</button>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-green-600 text-white">
                <tr>
                    <th class="px-4 py-3 text-left w-8">No</th>
                    <th class="px-4 py-3 text-left">Nama Kelas</th>
                    <th class="px-4 py-3 text-left">Tingkat</th>
                    <th class="px-4 py-3 text-center">Jumlah Siswa</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
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
                            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Aktif</span>
                        @else
                            <span class="bg-gray-100 text-gray-500 text-xs px-2 py-1 rounded-full">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1 justify-center flex-wrap">
                            <a href="{{ route('admin.kelas.assign-siswa', $item) }}"
                               class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                Assign Siswa
                            </a>
                            <a href="{{ route('admin.kelas.edit', $item) }}"
                               class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.kelas.destroy', $item) }}">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Hapus kelas {{ $item->nama_kelas }}? Data siswa di kelas ini juga akan dihapus.')"
                                        class="text-xs bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada kelas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $kelas->links() }}</div>
</div>
</body>
</html>
