@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- ============================================================
         BREADCRUMB
    ============================================================ --}}
    <nav class="text-sm text-gray-500 mb-4 flex items-center gap-1.5">
        <a href="{{ route('guru.dashboard') }}" class="hover:text-green-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('guru.ujian.index') }}" class="hover:text-green-600">Ujian</a>
        <span>/</span>
        <a href="{{ route('guru.ujian.show', $ujian) }}" class="hover:text-green-600">{{ Str::limit($ujian->judul, 30) }}</a>
        <span>/</span>
        <span class="text-gray-700 dark:text-gray-300 font-medium">Rekap Nilai</span>
    </nav>

    {{-- ============================================================
         HEADER
    ============================================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Rekap Nilai</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $ujian->judul }} &mdash;
                {{ $ujian->mataPelajaran->nama_mapel ?? '-' }} &mdash;
                Kelas {{ $ujian->kelas->nama_kelas ?? '-' }}
            </p>
        </div>
        {{-- Tombol export (hanya tampil jika ada peserta) --}}
        @if($totalPeserta > 0)
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('guru.ujian.export-excel', $ujian) }}"
               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Excel
            </a>
            <a href="{{ route('guru.ujian.export-pdf', $ujian) }}"
               class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                PDF
            </a>
        </div>
        @endif
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

    {{-- Peringatan: ada essay belum dikoreksi --}}
    @if($adaEssayBelumDikoreksi)
    <div class="mb-5 bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-lg text-sm flex items-start gap-2">
        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <span>Terdapat <strong>jawaban essay yang belum dikoreksi</strong>. Nilai beberapa siswa mungkin belum akurat sampai semua essay dikoreksi.</span>
    </div>
    @endif

    {{-- ============================================================
         KARTU STATISTIK
    ============================================================ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalPeserta }}</p>
            <p class="text-xs text-gray-500 mt-1">Peserta</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $rataRata }}</p>
            <p class="text-xs text-gray-500 mt-1">Rata-rata</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $nilaiTertinggi }}</p>
            <p class="text-xs text-gray-500 mt-1">Tertinggi</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-red-500">{{ $nilaiTerendah }}</p>
            <p class="text-xs text-gray-500 mt-1">Terendah</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $jumlahLulus }}</p>
            <p class="text-xs text-gray-500 mt-1">Lulus (≥75)</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-red-500">{{ $jumlahTidakLulus }}</p>
            <p class="text-xs text-gray-500 mt-1">Tidak Lulus</p>
        </div>
    </div>

    {{-- ============================================================
         TABEL NILAI
    ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($totalPeserta === 0)
            <div class="p-12 text-center text-gray-400 dark:text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="font-medium">Belum ada siswa yang menyelesaikan ujian ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-400 dark:text-gray-500 font-semibold w-10">No</th>
                            <th class="text-left px-4 py-3 text-gray-600 dark:text-gray-400 dark:text-gray-500 font-semibold">Nama Siswa</th>
                            <th class="text-center px-4 py-3 text-gray-600 dark:text-gray-400 dark:text-gray-500 font-semibold">Mulai</th>
                            <th class="text-center px-4 py-3 text-gray-600 dark:text-gray-400 dark:text-gray-500 font-semibold">Selesai</th>
                            <th class="text-center px-4 py-3 text-gray-600 dark:text-gray-400 dark:text-gray-500 font-semibold">Nilai</th>
                            <th class="text-center px-4 py-3 text-gray-600 dark:text-gray-400 dark:text-gray-500 font-semibold">Keterangan</th>
                            <th class="text-center px-4 py-3 text-gray-600 dark:text-gray-400 dark:text-gray-500 font-semibold">Pelanggaran</th>
                            <th class="text-center px-4 py-3 text-gray-600 dark:text-gray-400 dark:text-gray-500 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($sesiList as $i => $sesi)
                        @php
                            $nilaiAkhir = $sesi->nilai_akhir;
                            $lulus      = $nilaiAkhir !== null && $nilaiAkhir >= 75;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:bg-gray-900 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">
                                {{ $sesi->siswa?->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500 text-xs">
                                {{ $sesi->waktu_mulai?->format('H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500 text-xs">
                                {{ $sesi->waktu_selesai?->format('H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($nilaiAkhir !== null)
                                    <span class="font-bold text-lg
                                        {{ $lulus ? 'text-green-600' : 'text-red-500' }}">
                                        {{ number_format($nilaiAkhir, 1) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($nilaiAkhir !== null)
                                    <span class="text-xs px-2 py-1 rounded-full font-medium
                                        {{ $lulus ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                        {{ $lulus ? 'Lulus' : 'Tidak Lulus' }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($sesi->jumlah_pelanggaran > 0)
                                    <span class="text-xs font-medium text-orange-600">
                                        {{ $sesi->jumlah_pelanggaran }}x
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500">0</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('guru.ujian.detail-siswa', [$ujian, $sesi]) }}"
                                   class="text-xs text-blue-600 hover:text-blue-800 font-medium underline underline-offset-2">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Tombol kembali --}}
    <div class="mt-6">
        <a href="{{ route('guru.ujian.show', $ujian) }}"
           class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-300 flex items-center gap-1">
            ← Kembali ke Detail Ujian
        </a>
    </div>

</div>
@endsection
