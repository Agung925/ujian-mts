@extends('layouts.app')

@section('title', 'Assign Siswa ke Kelas')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Assign Siswa ke Kelas {{ $kelas->nama_kelas }}</h1>
    <p class="text-sm text-gray-500 mb-6">Tahun Ajaran: {{ $kelas->tahunAjaran->label }}</p>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">

        {{-- Kolom Kiri: Siswa yang belum di-assign --}}
        <div x-data="assignSiswa()">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="bg-orange-500 text-white px-4 py-3 flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold">Siswa Belum di-Assign</h2>
                        <p class="text-orange-200 text-xs">{{ $siswaBelumAssign->count() }} siswa tersedia</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.kelas.simpan-assign-siswa', $kelas) }}">
                    @csrf

                    {{-- Filter pencarian --}}
                    <div class="px-4 py-3 border-b">
                        <input type="text" x-model="search" placeholder="Cari nama / NIS..."
                               class="w-full border border-gray-200 dark:border-gray-700 rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-green-500">
                    </div>

                    <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                        @forelse($siswaBelumAssign as $siswa)
                        <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:bg-gray-900 cursor-pointer"
                               x-show="!search || '{{ strtolower($siswa->name) }}'.includes(search.toLowerCase()) || '{{ $siswa->nis }}'.includes(search)">
                            <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}"
                                   class="text-green-600 rounded focus:ring-green-500">
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $siswa->name }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">NIS: {{ $siswa->nis ?? '-' }}</p>
                            </div>
                        </label>
                        @empty
                        <div class="px-4 py-6 text-center text-gray-400 dark:text-gray-500 text-sm">
                            Semua siswa sudah di-assign ke kelas.
                        </div>
                        @endforelse
                    </div>

                    @if($siswaBelumAssign->count() > 0)
                    <div class="px-4 py-3 border-t bg-gray-50 dark:bg-gray-900">
                        <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm font-medium">
                            Tambahkan Siswa yang Dipilih →
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Kolom Kanan: Siswa yang sudah ada di kelas ini --}}
        <div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="bg-green-600 text-white px-4 py-3">
                    <h2 class="font-semibold">Siswa di Kelas {{ $kelas->nama_kelas }}</h2>
                    <p class="text-green-200 text-xs">{{ $siswaKelas->count() }} siswa terdaftar</p>
                </div>

                <div class="max-h-96 overflow-y-auto divide-y divide-gray-100">
                    @forelse($siswaKelas as $siswa)
                    <div class="flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 dark:bg-gray-900">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $siswa->name }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">NIS: {{ $siswa->nis ?? '-' }}</p>
                        </div>
                        {{-- Hapus dari kelas via delete SiswaKelas record --}}
                        <form method="POST" action="{{ route('admin.kelas.index') }}">
                            {{-- Fitur remove siswa dari kelas bisa ditambah di Step selanjutnya --}}
                        </form>
                    </div>
                    @empty
                    <div class="px-4 py-6 text-center text-gray-400 dark:text-gray-500 text-sm">
                        Belum ada siswa di kelas ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <div class="mt-6">
        <a href="{{ route('admin.kelas.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-300">
            ← Kembali ke Daftar Kelas
        </a>
    </div>
</div>

<script>
function assignSiswa() {
    return { search: '' };
}
</script>
@endsection
