@extends('layouts.app')

@section('title', 'Detail Jawaban — ' . $sesi->siswa->name)

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Breadcrumb --}}
    <nav class="text-xs text-gray-400 dark:text-gray-500 mb-4 flex items-center gap-1">
        <a href="{{ route('guru.ujian.index') }}" class="hover:text-gray-600 dark:text-gray-400 dark:text-gray-500">Ujian</a>
        <span>/</span>
        <a href="{{ route('guru.ujian.show', $ujian) }}" class="hover:text-gray-600 dark:text-gray-400 dark:text-gray-500">{{ $ujian->judul }}</a>
        <span>/</span>
        <span class="text-gray-600 dark:text-gray-400 dark:text-gray-500">{{ $sesi->siswa->name }}</span>
    </nav>

    {{-- ============================================================
         HEADER NILAI
    ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 shadow p-6 mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1">
            <h1 class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $sesi->siswa->name }}</h1>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                {{ $ujian->mataPelajaran->nama_mapel ?? '-' }}
                &bull; {{ $ujian->kelas->nama_kelas ?? '-' }}
                &bull; {{ $sesi->waktu_selesai?->format('d M Y H:i') ?? 'Belum selesai' }}
            </p>
        </div>
        <div class="text-center sm:text-right shrink-0">
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">Nilai Akhir</p>
            @if($sesi->nilai_akhir !== null)
                <p class="text-4xl font-extrabold
                    {{ $sesi->nilai_akhir >= 75 ? 'text-green-600' : ($sesi->nilai_akhir >= 60 ? 'text-yellow-500' : 'text-red-500') }}">
                    {{ number_format($sesi->nilai_akhir, 1) }}
                </p>
                <p class="text-xs {{ $sesi->nilai_akhir >= 75 ? 'text-green-600' : 'text-red-500' }} font-medium">
                    {{ $sesi->nilai_akhir >= 75 ? '✓ Lulus' : '✗ Belum Lulus' }}
                </p>
            @else
                <p class="text-sm text-yellow-600 font-medium">Menunggu koreksi essay</p>
            @endif
        </div>
    </div>

    {{-- ============================================================
         STATISTIK RINGKAS
    ============================================================ --}}
    @php
        $totalSoal   = $soalUrut->count();
        $totalBenar  = $jawabanMap->where('is_benar', true)->count();
        $totalSalah  = $jawabanMap->where('is_benar', false)->count();
        $totalKosong = $totalSoal - $jawabanMap->count();
    @endphp
    <div class="grid grid-cols-4 gap-3 mb-6 text-center">
        <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 rounded-xl p-3">
            <p class="text-xl font-bold text-gray-700 dark:text-gray-300">{{ $totalSoal }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">Total Soal</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-3">
            <p class="text-xl font-bold text-green-600">{{ $totalBenar }}</p>
            <p class="text-xs text-green-700">Benar</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-3">
            <p class="text-xl font-bold text-red-500">{{ $totalSalah }}</p>
            <p class="text-xs text-red-600">Salah</p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 rounded-xl p-3">
            <p class="text-xl font-bold text-gray-400 dark:text-gray-500">{{ $totalKosong }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">Tidak Dijawab</p>
        </div>
    </div>

    {{-- ============================================================
         PEMBAHASAN PER SOAL
    ============================================================ --}}
    <div class="space-y-4 mb-8">
        <h2 class="text-base font-semibold text-gray-700 dark:text-gray-300">Jawaban per Soal</h2>

        @foreach($soalUrut as $i => $soal)
        @php
            $jawaban        = $jawabanMap->get($soal->id);
            $benar          = $jawaban?->is_benar;
            $pilihanDipilih = $jawaban?->pilihan;
            $pilihanBenar   = $soal->jawabanBenar;

            if ($jawaban === null) {
                $borderKelas = 'border-gray-200';
                $badgeKelas  = 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 dark:text-gray-500';
                $badgeTeks   = 'Tidak Dijawab';
            } elseif ($benar === true) {
                $borderKelas = 'border-green-300';
                $badgeKelas  = 'bg-green-100 text-green-700';
                $badgeTeks   = '✓ Benar';
            } elseif ($benar === false) {
                $borderKelas = 'border-red-300';
                $badgeKelas  = 'bg-red-100 text-red-600';
                $badgeTeks   = '✗ Salah';
            } else {
                $borderKelas = 'border-yellow-200';
                $badgeKelas  = 'bg-yellow-100 text-yellow-700';
                $badgeTeks   = 'Menunggu Koreksi';
            }
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-xl border {{ $borderKelas }} p-5">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $i + 1 }}.</span>
                    @if($soal->tipe_soal === 'pg')
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">PG</span>
                    @elseif($soal->tipe_soal === 'bs')
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">B/S</span>
                    @else
                        <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Essay</span>
                    @endif
                    <span class="text-xs text-gray-400 dark:text-gray-500">Bobot: {{ $soal->pivot->bobot_nilai }}</span>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0 {{ $badgeKelas }}">
                    {{ $badgeTeks }}
                </span>
            </div>

            {{-- Pertanyaan --}}
            <p class="text-sm text-gray-800 dark:text-gray-100 leading-relaxed mb-4">
                {!! nl2br(e($soal->pertanyaan)) !!}
            </p>

            {{-- PG / BS --}}
            @if(in_array($soal->tipe_soal, ['pg', 'bs']))
                <div class="space-y-1.5">
                    @foreach($soal->pilihanJawaban as $pilihan)
                    @php
                        $dipilihSiswa = $pilihanDipilih?->id === $pilihan->id;
                        $iniBenar     = $pilihan->is_benar;

                        if ($iniBenar && $dipilihSiswa) {
                            $kelas = 'bg-green-100 border-green-400 text-green-800';
                        } elseif ($iniBenar) {
                            $kelas = 'bg-green-50 border-green-300 text-green-700';
                        } elseif ($dipilihSiswa) {
                            $kelas = 'bg-red-100 border-red-400 text-red-800';
                        } else {
                            $kelas = 'bg-gray-50 dark:bg-gray-900 border-gray-200 text-gray-600 dark:text-gray-400 dark:text-gray-500';
                        }
                    @endphp
                    <div class="flex items-center gap-2 px-3 py-2 border rounded-lg text-sm {{ $kelas }}">
                        <span class="font-medium w-5 shrink-0">{{ $pilihan->label }}.</span>
                        <span class="flex-1">{{ $pilihan->teks_pilihan }}</span>
                        @if($iniBenar)
                            <span class="text-xs font-bold text-green-600 shrink-0">✓ Kunci</span>
                        @endif
                        @if($dipilihSiswa && !$iniBenar)
                            <span class="text-xs font-bold text-red-500 shrink-0">← Pilihan siswa</span>
                        @elseif($dipilihSiswa && $iniBenar)
                            <span class="text-xs text-green-700 shrink-0">← Pilihan siswa</span>
                        @endif
                    </div>
                    @endforeach
                </div>

            {{-- Essay --}}
            @else
                <div class="mb-3">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 dark:text-gray-500 mb-1">Jawaban siswa:</p>
                    @if($jawaban?->jawaban_essay)
                        <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 dark:text-gray-100 whitespace-pre-wrap">{{ $jawaban->jawaban_essay }}</div>
                    @else
                        <p class="text-sm text-gray-400 dark:text-gray-500 italic">Tidak dijawab</p>
                    @endif
                </div>

                @if($soal->kunci_essay)
                    <div class="text-xs bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 text-blue-800 mb-2">
                        <strong>Kunci/panduan:</strong>
                        <p class="mt-0.5 whitespace-pre-wrap">{{ $soal->kunci_essay }}</p>
                    </div>
                @endif

                {{-- Form koreksi essay --}}
                @if($benar === null && $jawaban?->jawaban_essay)
                <form method="POST" action="{{ route('guru.ujian.koreksi-essay', [$ujian, $sesi]) }}" class="mt-2 flex gap-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="jawaban_id" value="{{ $jawaban->id }}">
                    <button type="submit" name="is_benar" value="1"
                            class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg transition">
                        ✓ Tandai Benar
                    </button>
                    <button type="submit" name="is_benar" value="0"
                            class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition">
                        ✗ Tandai Salah
                    </button>
                </form>
                @endif
            @endif
        </div>
        @endforeach
    </div>

    {{-- Tombol kembali --}}
    <div class="text-center">
        <a href="{{ route('guru.ujian.show', $ujian) }}"
           class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 font-semibold px-6 py-2.5 rounded-xl text-sm transition">
            ← Kembali ke Detail Ujian
        </a>
    </div>
</div>
@endsection
