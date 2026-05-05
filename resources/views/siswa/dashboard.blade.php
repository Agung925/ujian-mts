@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">
            Selamat datang, {{ \Illuminate\Support\Facades\Auth::user()->name }}!
        </h2>
        <p class="text-gray-500 text-sm mb-6">
            Kelas: <span class="font-semibold text-green-600">
                {{ \Illuminate\Support\Facades\Auth::user()->kelas->pluck('nama_kelas')->join(', ') ?: 'Belum ditentukan' }}
            </span>
        </p>

        {{-- Flash message --}}
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mb-4 bg-blue-50 border border-blue-300 text-blue-700 px-4 py-3 rounded-lg text-sm">
                ℹ️ {{ session('info') }}
            </div>
        @endif

        {{-- Sesi aktif yang masih berjalan --}}
        @if($sesiAktif)
        <div class="mb-6 bg-yellow-50 border border-yellow-300 rounded-xl p-4 flex items-center justify-between">
            <div>
                <p class="font-semibold text-yellow-800">⚠️ Kamu masih ada ujian yang sedang berjalan!</p>
                <p class="text-sm text-yellow-700 mt-0.5">{{ $sesiAktif->ujian->judul }}</p>
            </div>
            <a href="{{ route('siswa.ujian.ruang', $sesiAktif->id) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                Lanjutkan Ujian →
            </a>
        </div>
        @endif

        {{-- ============================================================
             UJIAN AKTIF — yang tersedia untuk kelas siswa ini
        ============================================================ --}}
        <div class="mb-8">
            <h3 class="text-base font-semibold text-gray-700 mb-3">Ujian Tersedia</h3>

            @if($ujianAktif->isEmpty())
                <div class="bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-400">
                    <div class="text-4xl mb-2">📭</div>
                    <p class="text-sm">Belum ada ujian aktif untuk kelasmu saat ini.</p>
                    <p class="text-xs text-gray-300 mt-1">Tanya guru kapan ujian akan dibuka.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($ujianAktif as $ujian)
                    @php
                        // Cek apakah siswa sudah pernah ikut ujian ini
                        $sesiSiswa = $ujian->sesiUjian->where('siswa_id', \Illuminate\Support\Facades\Auth::id())->first();
                    @endphp
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition"
                         x-data="{ buka: false }">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800 text-sm leading-tight">{{ $ujian->judul }}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $ujian->mataPelajaran->nama_mapel ?? '-' }} &bull;
                                    {{ $ujian->kelas->nama_kelas ?? '-' }}
                                </p>
                            </div>
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium ml-2 shrink-0">
                                Aktif
                            </span>
                        </div>

                        <div class="flex gap-4 text-xs text-gray-500 mb-4">
                            <span>⏱ {{ $ujian->durasi_menit }} menit</span>
                            <span>📝 {{ $ujian->soal->count() }} soal</span>
                            @if($ujian->dibuka_pada)
                                <span>🕐 {{ $ujian->dibuka_pada->format('H:i') }}</span>
                            @endif
                        </div>

                        @if($sesiSiswa?->status === 'selesai')
                            <div class="text-center py-2 bg-blue-50 rounded-lg text-xs text-blue-600 font-medium">
                                ✅ Sudah dikerjakan
                            </div>
                        @elseif($sesiSiswa?->status === 'sedang')
                            <a href="{{ route('siswa.ujian.ruang', $sesiSiswa->id) }}"
                               class="block text-center bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold py-2 rounded-lg transition">
                                Lanjutkan Ujian →
                            </a>
                        @else
                            {{-- Tombol masuk ujian dengan modal token --}}
                            <button @click="buka = true"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 rounded-lg transition">
                                Masuk Ujian →
                            </button>

                            {{-- Modal input token --}}
                            <div x-show="buka" x-cloak
                                 class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                                <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6"
                                     @click.outside="buka = false">
                                    <h3 class="font-bold text-gray-800 mb-1">Masuk Ujian</h3>
                                    <p class="text-xs text-gray-500 mb-4">{{ $ujian->judul }}</p>

                                    @if($errors->has('token'))
                                        <p class="text-red-500 text-xs mb-3">{{ $errors->first('token') }}</p>
                                    @endif

                                    <form method="POST" action="{{ route('siswa.ujian.masuk') }}">
                                        @csrf
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Masukkan Token Ujian
                                        </label>
                                        <input type="text"
                                               name="token"
                                               placeholder="{{ substr($ujian->mataPelajaran->kode_mapel ?? 'CBT', 0, 3) }}-XXXX"
                                               value="{{ old('token') }}"
                                               maxlength="20"
                                               autocomplete="off"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono text-center tracking-widest uppercase focus:ring-2 focus:ring-green-500 focus:outline-none mb-4">
                                        <button type="submit"
                                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                                            Mulai Ujian
                                        </button>
                                    </form>
                                    <button @click="buka = false"
                                            class="w-full mt-2 text-sm text-gray-400 hover:text-gray-600">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ============================================================
             RIWAYAT UJIAN
        ============================================================ --}}
        @if($riwayatUjian->isNotEmpty())
        <div>
            <h3 class="text-base font-semibold text-gray-700 mb-3">Riwayat Ujian</h3>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Ujian</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Mapel</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Waktu Selesai</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($riwayatUjian as $sesi)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-800 font-medium">{{ $sesi->ujian->judul }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $sesi->ujian->mataPelajaran->nama_mapel ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-gray-500 text-xs">
                                {{ $sesi->waktu_selesai?->format('d M Y H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Selesai</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </main>
@endsection
