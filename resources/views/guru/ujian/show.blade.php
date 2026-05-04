@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('guru.ujian.index') }}" class="hover:text-green-600">Daftar Ujian</a>
        <span>/</span>
        <span class="text-gray-700 font-medium">{{ Str::limit($ujian->judul, 50) }}</span>
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

    {{-- ============================================================
         BAGIAN 1 — Info Ujian
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <h1 class="text-xl font-bold text-gray-800">{{ $ujian->judul }}</h1>
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $ujian->warna_badge_status }}">
                        {{ ucfirst($ujian->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-wide">Mata Pelajaran</p>
                        <p class="font-medium text-gray-700 mt-0.5">{{ $ujian->mataPelajaran->nama_mapel ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-wide">Kelas</p>
                        <p class="font-medium text-gray-700 mt-0.5">{{ $ujian->kelas->nama_kelas ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-wide">Durasi</p>
                        <p class="font-medium text-gray-700 mt-0.5">{{ $ujian->durasi_menit }} menit</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-wide">Jumlah Soal</p>
                        <p class="font-medium text-gray-700 mt-0.5">{{ $ujian->soal->count() }} soal</p>
                    </div>
                </div>

                @if($ujian->deskripsi)
                    <p class="mt-3 text-sm text-gray-500">{{ $ujian->deskripsi }}</p>
                @endif
            </div>

            {{-- Box Token --}}
            <div class="shrink-0 bg-green-50 border border-green-200 rounded-xl px-5 py-4 text-center min-w-36">
                <p class="text-xs text-green-600 font-medium uppercase tracking-wide mb-1">Token Ujian</p>
                <p class="text-2xl font-mono font-bold text-green-700">{{ $ujian->token }}</p>
                @if($ujian->isAktif())
                    <p class="text-xs text-green-500 mt-1">Bagikan ke siswa</p>
                @endif
            </div>
        </div>

        {{-- Tombol Aksi sesuai status --}}
        <div class="mt-5 pt-4 border-t border-gray-100 flex gap-3 flex-wrap" x-data>
            @if($ujian->isDraft())
                {{-- Buka Ujian --}}
                <form method="POST" action="{{ route('guru.ujian.buka', $ujian) }}">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Buka ujian sekarang? Siswa bisa mulai mengerjakan dengan token.')"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        ▶ Buka Ujian
                    </button>
                </form>
                <a href="{{ route('guru.ujian.edit', $ujian) }}"
                   class="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Edit
                </a>
                <form method="POST" action="{{ route('guru.ujian.destroy', $ujian) }}"
                      @submit.prevent="if(confirm('Hapus ujian ini? Semua soal yang sudah dipilih akan ikut terhapus.')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Hapus
                    </button>
                </form>
            @elseif($ujian->isAktif())
                {{-- Tutup Ujian --}}
                <form method="POST" action="{{ route('guru.ujian.tutup', $ujian) }}"
                      @submit.prevent="if(confirm('Tutup ujian sekarang? Semua siswa yang masih mengerjakan akan di-submit otomatis.')) $el.submit()">
                    @csrf
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        ■ Tutup Ujian
                    </button>
                </form>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Ujian sedang berlangsung — {{ $ujian->sesiUjian->where('status', 'sedang')->count() }} siswa mengerjakan
                </div>
            @elseif($ujian->isSelesai())
                <div class="text-sm text-blue-600 font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Ujian selesai pada {{ $ujian->ditutup_pada?->format('d M Y H:i') }}
                </div>
                {{-- Tombol rekap & export nilai untuk ujian yang sudah selesai --}}
                <a href="{{ route('guru.ujian.rekap-nilai', $ujian) }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Rekap Nilai
                </a>
                <a href="{{ route('guru.ujian.export-excel', $ujian) }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('guru.ujian.export-pdf', $ujian) }}"
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    PDF
                </a>
            @endif
        </div>
    </div>

    {{-- ============================================================
         BAGIAN 2 — Tambah Soal (hanya tampil saat Draft)
    ============================================================ --}}
    @if($ujian->isDraft())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6" x-data="{ tab: 'manual' }">
        <div class="border-b border-gray-200">
            <div class="flex">
                <button @click="tab = 'manual'"
                        :class="tab === 'manual' ? 'border-b-2 border-green-600 text-green-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="px-6 py-3 text-sm transition">
                    Pilih Manual
                </button>
                <button @click="tab = 'random'"
                        :class="tab === 'random' ? 'border-b-2 border-green-600 text-green-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="px-6 py-3 text-sm transition">
                    Ambil Random
                </button>
            </div>
        </div>

        {{-- Tab: Pilih Manual --}}
        <div x-show="tab === 'manual'" class="p-5">
            @if($soalTersedia->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">
                    Semua soal dari mapel ini sudah masuk ujian, atau belum ada soal.
                    <a href="{{ route('guru.bank-soal.create') }}" class="text-green-600 underline">Tambah soal baru</a>
                </p>
            @else
                <form method="POST" action="{{ route('guru.ujian.tambah-soal-manual', $ujian) }}"
                      x-data="{ selected: [] }">
                    @csrf

                    @if($errors->has('soal_ids'))
                        <p class="text-red-500 text-xs mb-3">{{ $errors->first('soal_ids') }}</p>
                    @endif

                    <div class="border border-gray-200 rounded-lg divide-y divide-gray-100 max-h-72 overflow-y-auto mb-4">
                        @foreach($soalTersedia as $soal)
                        <label class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="soal_ids[]" value="{{ $soal->id }}"
                                   x-model="selected"
                                   class="mt-0.5 text-green-600 rounded focus:ring-green-500">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-800">{{ Str::limit(strip_tags($soal->pertanyaan), 100) }}</p>
                                <div class="flex gap-2 mt-1">
                                    @if($soal->tipe_soal === 'pg')
                                        <span class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">PG</span>
                                    @elseif($soal->tipe_soal === 'bs')
                                        <span class="text-xs bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded">B/S</span>
                                    @else
                                        <span class="text-xs bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded">Essay</span>
                                    @endif
                                    <span class="text-xs {{ $soal->warna_badge_kesulitan }} px-1.5 py-0.5 rounded">{{ $soal->label_kesulitan }}</span>
                                    <span class="text-xs text-gray-400">Bobot: {{ $soal->bobot_nilai }}</span>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500" x-text="selected.length + ' soal dipilih'"></p>
                        <button type="submit"
                                :disabled="selected.length === 0"
                                :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700'"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                            Tambah Soal Terpilih
                        </button>
                    </div>
                </form>
            @endif
        </div>

        {{-- Tab: Ambil Random --}}
        <div x-show="tab === 'random'" class="p-5">
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
                <strong>Catatan:</strong> Mode random akan <strong>mengganti semua soal</strong> yang sudah ada di ujian ini.
                Tersedia <strong>{{ $totalSoalTersedia }}</strong> soal aktif dari mapel ini.
            </div>

            <form method="POST" action="{{ route('guru.ujian.tambah-soal-random', $ujian) }}">
                @csrf
                <div class="flex items-end gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Soal</label>
                        <input type="number" name="jumlah_soal" value="{{ old('jumlah_soal', min(10, $totalSoalTersedia)) }}"
                               min="1" max="{{ $totalSoalTersedia }}"
                               class="w-28 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                        @error('jumlah_soal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition"
                            onclick="return confirm('Ambil soal random? Ini akan mengganti soal yang sudah ada.')">
                        Ambil Soal Random
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ============================================================
         BAGIAN 3 — Daftar Soal dalam Ujian
    ============================================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-700">Soal dalam Ujian</h2>
            <div class="text-sm text-gray-500">
                {{ $ujian->soal->count() }} soal &bull; Total bobot: {{ $ujian->total_nilai_maks }}
            </div>
        </div>

        @if($ujian->soal->isEmpty())
            <div class="text-center py-10 text-gray-400 text-sm">
                Belum ada soal. Tambahkan soal di atas.
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 w-12">No</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Pertanyaan</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Tipe</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Bobot</th>
                        @if($ujian->isDraft())
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Hapus</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($ujian->soal as $soal)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $soal->pivot->nomor_urut }}</td>
                        <td class="px-4 py-3 text-gray-800 max-w-md">
                            {{ Str::limit(strip_tags($soal->pertanyaan), 100) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($soal->tipe_soal === 'pg')
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">PG</span>
                            @elseif($soal->tipe_soal === 'bs')
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">B/S</span>
                            @else
                                <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Essay</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700 font-medium">{{ $soal->pivot->bobot_nilai }}</td>
                        @if($ujian->isDraft())
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('guru.ujian.hapus-soal', [$ujian, $soal->id]) }}"
                                  x-data
                                  @submit.prevent="if(confirm('Hapus soal ini dari ujian?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Hapus</button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="{{ $ujian->isDraft() ? 3 : 3 }}" class="px-4 py-2 text-sm font-semibold text-gray-600 text-right">Total Bobot:</td>
                        <td class="px-4 py-2 text-center font-bold text-gray-800">{{ $ujian->total_nilai_maks }}</td>
                        @if($ujian->isDraft())<td></td>@endif
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

    {{-- Daftar Peserta (jika sudah ada sesi) --}}
    @if($ujian->sesiUjian->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-6">
        <div class="px-5 py-3 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700">Peserta ({{ $ujian->sesiUjian->count() }} siswa)</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-semibold text-gray-600">Nama Siswa</th>
                    <th class="px-4 py-2 text-center font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-2 text-center font-semibold text-gray-600">Mulai</th>
                    <th class="px-4 py-2 text-center font-semibold text-gray-600">Nilai</th>
                    <th class="px-4 py-2 text-center font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($ujian->sesiUjian as $sesi)
                <tr>
                    <td class="px-4 py-3 text-gray-800">{{ $sesi->siswa->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($sesi->status === 'selesai')
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Selesai</span>
                        @elseif($sesi->status === 'sedang')
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Mengerjakan</span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Belum Mulai</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-gray-500 text-xs">{{ $sesi->waktu_mulai?->format('H:i') ?? '-' }}</td>
                    <td class="px-4 py-3 text-center font-medium text-gray-800">
                        {{ $sesi->nilai_akhir !== null ? number_format($sesi->nilai_akhir, 1) : '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($sesi->status === 'selesai')
                            <a href="{{ route('guru.ujian.detail-siswa', [$ujian, $sesi]) }}"
                               class="text-xs text-green-600 hover:underline font-medium">
                                Lihat Detail
                            </a>
                        @else
                            <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
