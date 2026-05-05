@extends('layouts.app')

@section('title', 'Daftar Kelas')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Daftar Kelas</h1>
            @if($tahunAktif)
                <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 mt-1">Tahun Ajaran: <span class="font-medium text-green-700">{{ $tahunAktif->label }}</span></p>
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

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
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
                <tr class="hover:bg-gray-50 dark:bg-gray-900">
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-100">{{ $item->nama_kelas }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 dark:text-gray-500">Kelas {{ $item->tingkat }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded-full">
                            {{ $item->siswa->count() }} siswa
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($item->is_aktif)
                            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Aktif</span>
                        @else
                            <span class="bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 dark:text-gray-500 text-xs px-2 py-1 rounded-full">Nonaktif</span>
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
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">Belum ada kelas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $kelas->links() }}</div>
</div>
@endsection
