@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
    <main class="max-w-7xl mx-auto px-4 py-8">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
                <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">&times;</button>
            </div>
        @endif
        @if (session('warning'))
            <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded-lg px-4 py-3 mb-6">
                <svg class="w-5 h-5 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 110 18A9 9 0 0112 3z"/>
                </svg>
                {{ session('warning') }}
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Header & Tombol Aksi --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Daftar User</h1>
            <div class="flex flex-wrap gap-2">
                {{-- Tombol Import Siswa --}}
                <button onclick="document.getElementById('modal-import').classList.remove('hidden')"
                    class="flex items-center gap-2 bg-white dark:bg-gray-800 border border-primary-600 text-primary-600 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-primary-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    Import Siswa Excel
                </button>
                {{-- Tombol Tambah User --}}
                <a href="{{ route('admin.users.create') }}"
                    class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah User
                </a>
            </div>
        </div>

        {{-- Filter & Pencarian --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau NIS..."
                class="flex-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <select name="role" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Semua Role</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="guru" {{ request('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                Cari
            </button>
            @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 dark:text-gray-300 text-sm font-semibold px-5 py-2 rounded-lg transition text-center">
                    Reset
                </a>
            @endif
        </form>

        {{-- Tabel User --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-primary-600 text-white">
                        <tr>
                            <th class="px-4 py-3 font-semibold w-10">No</th>
                            <th class="px-4 py-3 font-semibold">Nama</th>
                            <th class="px-4 py-3 font-semibold">NIS / Email</th>
                            <th class="px-4 py-3 font-semibold">Role</th>
                            <th class="px-4 py-3 font-semibold text-center">Status</th>
                            <th class="px-4 py-3 font-semibold text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 dark:bg-gray-900 transition">
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $users->firstItem() + $loop->index }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $user->name }}</div>
                                    @if ($user->jenis_kelamin)
                                        <div class="text-xs text-gray-400">{{ $user->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $user->nis ?? $user->email ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $roleColor = match($user->role) {
                                            'super_admin' => 'bg-purple-100 text-purple-700',
                                            'guru'        => 'bg-blue-100 text-blue-700',
                                            'siswa'       => 'bg-green-100 text-green-700',
                                            default       => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                        };
                                        $roleLabel = match($user->role) {
                                            'super_admin' => 'Super Admin',
                                            'guru'        => 'Guru',
                                            'siswa'       => 'Siswa',
                                            default       => $user->role,
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $roleColor }}">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($user->is_aktif)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        {{-- Tombol Hapus —tidak tampil untuk diri sendiri --}}
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('Yakin hapus user {{ $user->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Tidak ada data user ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer tabel: info jumlah + pagination --}}
            @if ($users->hasPages() || $users->total() > 0)
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} user
                    </p>
                    <div class="text-sm">
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        </div>
    </main>

    {{-- Modal Import Siswa --}}
    <div id="modal-import" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
        x-data>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Import Siswa dari Excel</h2>
                <button onclick="document.getElementById('modal-import').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Download Template --}}
            <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-3 mb-5 flex items-center gap-3">
                <svg class="w-8 h-8 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Unduh template Excel terlebih dahulu</p>
                    <a href="{{ route('admin.users.template-excel') }}"
                        class="text-xs text-primary-600 hover:underline font-medium">
                        Download template_import_siswa.xlsx
                    </a>
                </div>
            </div>

            {{-- Kolom yang diharapkan --}}
            <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                <p class="font-medium text-gray-600 dark:text-gray-400 mb-1">Kolom yang diperlukan dalam file Excel:</p>
                <div class="flex flex-wrap gap-1">
                    @foreach(['nis', 'name', 'jenis_kelamin', 'no_telp'] as $col)
                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded font-mono">{{ $col }}</span>
                    @endforeach
                </div>
                <p class="mt-1">* NIS wajib diisi. Password default = NIS siswa.</p>
            </div>

            {{-- Form Upload --}}
            <form method="POST" action="{{ route('admin.users.import-siswa') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih File Excel (.xlsx / .xls)</label>
                    <input type="file" name="file" accept=".xlsx,.xls" required
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary-50 file:text-primary-700 file:text-xs file:font-semibold hover:file:bg-primary-100">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold py-2 rounded-lg transition">
                        Import Sekarang
                    </button>
                    <button type="button" onclick="document.getElementById('modal-import').classList.add('hidden')"
                        class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 text-sm font-semibold py-2 rounded-lg transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
