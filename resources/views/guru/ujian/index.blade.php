@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Ujian Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Total: {{ $ujian->total() }} ujian</p>
        </div>
        <a href="{{ route('guru.ujian.create') }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Ujian Baru
        </a>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabel ujian --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($ujian->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-sm">Belum ada ujian. <a href="{{ route('guru.ujian.create') }}" class="text-green-600 underline">Buat ujian pertama</a></p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-8">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Judul Ujian</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Mapel</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Kelas</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Durasi</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Token</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Soal</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($ujian as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $ujian->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3 text-gray-800 font-medium max-w-xs">
                            {{ Str::limit($item->judul, 60) }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $item->mataPelajaran->kode_mapel ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $item->kelas->nama_kelas ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $item->durasi_menit }} mnt</td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded text-gray-700">{{ $item->token }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $item->warna_badge_status }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700 font-medium">{{ $item->soal_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('guru.ujian.show', $item) }}"
                                   class="text-green-600 hover:text-green-800 text-xs font-medium">Detail</a>
                                @if($item->isDraft())
                                    <a href="{{ route('guru.ujian.edit', $item) }}"
                                       class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                                    <form method="POST" action="{{ route('guru.ujian.destroy', $item) }}"
                                          x-data
                                          @submit.prevent="if(confirm('Hapus ujian ini?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($ujian->hasPages())
                <div class="px-4 py-4 border-t border-gray-100">
                    {{ $ujian->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
