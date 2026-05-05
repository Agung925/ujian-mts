@extends('layouts.app')

@section('title', 'Daftar Tahun Ajaran')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Tahun Ajaran</h1>
        <a href="{{ route('admin.tahun-ajaran.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            + Tambah Tahun Ajaran
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex justify-between">
        <span>{{ session('success') }}</span>
        <button @click="show = false" class="font-bold text-green-600">×</button>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">
        {{ session('error') }}
    </div>
    @endif

    {{-- Tabel Tahun Ajaran --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-green-600 text-white">
                <tr>
                    <th class="px-4 py-3 text-left w-8">No</th>
                    <th class="px-4 py-3 text-left">Tahun Ajaran</th>
                    <th class="px-4 py-3 text-left">Semester</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tahunAjaran as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item->nama }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        Semester {{ $item->semester }} ({{ $item->semester == '1' ? 'Ganjil' : 'Genap' }})
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($item->is_aktif)
                            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Aktif</span>
                        @else
                            <span class="bg-gray-100 text-gray-500 text-xs px-2 py-1 rounded-full">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2 justify-center flex-wrap">
                            {{-- Tombol Aktifkan (hanya jika belum aktif) --}}
                            @unless($item->is_aktif)
                            <form method="POST" action="{{ route('admin.tahun-ajaran.aktifkan', $item) }}">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('Aktifkan tahun ajaran {{ $item->label }}?')"
                                    class="text-xs bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded">
                                    Aktifkan
                                </button>
                            </form>
                            @endunless

                            {{-- Tombol Edit --}}
                            <a href="{{ route('admin.tahun-ajaran.edit', $item) }}"
                               class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">
                                Edit
                            </a>

                            {{-- Tombol Hapus (tidak bisa jika aktif) --}}
                            @unless($item->is_aktif)
                            <form method="POST" action="{{ route('admin.tahun-ajaran.destroy', $item) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Hapus tahun ajaran {{ $item->nama }}?')"
                                    class="text-xs bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                    Hapus
                                </button>
                            </form>
                            @endunless
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada data tahun ajaran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">{{ $tahunAjaran->links() }}</div>

</div>
@endsection
