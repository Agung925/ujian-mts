@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('guru.bank-soal.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Detail Soal</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        {{-- Badge info soal --}}
        <div class="flex flex-wrap gap-2 mb-5">
            {{-- Tipe soal --}}
            @if($bankSoal->tipe_soal === 'pg')
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">Pilihan Ganda</span>
            @elseif($bankSoal->tipe_soal === 'bs')
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-700">Benar / Salah</span>
            @else
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-700">Essay</span>
            @endif

            {{-- Kategori --}}
            <span class="px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-700">
                {{ $bankSoal->kategori ?? '-' }}
            </span>

            {{-- Sub Kategori --}}
            <span class="px-3 py-1 rounded-full text-sm font-medium bg-teal-100 text-teal-700">
                {{ $bankSoal->sub_kategori ?? '-' }}
            </span>

            {{-- Bobot nilai --}}
            <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
                Bobot: {{ $bankSoal->bobot_nilai }}
            </span>

            {{-- Mata pelajaran --}}
            <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                {{ $bankSoal->mataPelajaran->nama_mapel ?? '-' }}
            </span>
        </div>

        {{-- Teks pertanyaan --}}
        <div class="mb-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Pertanyaan</p>
            <p class="text-gray-800 leading-relaxed text-base">{{ $bankSoal->pertanyaan }}</p>
        </div>

        {{-- Gambar jika ada --}}
        @if($bankSoal->gambar)
            <div class="mb-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Gambar Soal</p>
                <img src="{{ $bankSoal->url_gambar }}" class="max-h-60 rounded-lg border border-gray-200">
            </div>
        @endif

        {{-- Pilihan Jawaban untuk PG --}}
        @if($bankSoal->tipe_soal === 'pg')
            <div class="mb-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Pilihan Jawaban</p>
                <div class="space-y-2">
                    @foreach($bankSoal->pilihanJawaban as $pilihan)
                        <div class="flex items-center gap-3 px-4 py-2 rounded-lg
                            {{ $pilihan->is_benar ? 'bg-green-50 border border-green-300' : 'bg-gray-50 border border-gray-200' }}">
                            <span class="font-bold w-6 flex-shrink-0
                                {{ $pilihan->is_benar ? 'text-green-700' : 'text-gray-500' }}">
                                {{ $pilihan->label }}
                            </span>
                            <span class="text-sm {{ $pilihan->is_benar ? 'text-green-800 font-medium' : 'text-gray-700' }}">
                                {{ $pilihan->teks_pilihan }}
                            </span>
                            @if($pilihan->is_benar)
                                <span class="ml-auto text-xs font-medium text-green-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Jawaban Benar
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Pilihan Jawaban untuk BS --}}
        @if($bankSoal->tipe_soal === 'bs')
            <div class="mb-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Jawaban yang Benar</p>
                @php $jawabanBenarBs = $bankSoal->jawabanBenar; @endphp
                <div class="flex gap-4">
                    <div class="px-6 py-3 rounded-lg border
                        {{ ($jawabanBenarBs?->label === 'Benar') ? 'bg-green-50 border-green-400' : 'bg-gray-50 border-gray-200' }}">
                        <span class="font-medium {{ ($jawabanBenarBs?->label === 'Benar') ? 'text-green-700' : 'text-gray-500' }}">
                            ✓ Benar
                        </span>
                    </div>
                    <div class="px-6 py-3 rounded-lg border
                        {{ ($jawabanBenarBs?->label === 'Salah') ? 'bg-green-50 border-green-400' : 'bg-gray-50 border-gray-200' }}">
                        <span class="font-medium {{ ($jawabanBenarBs?->label === 'Salah') ? 'text-green-700' : 'text-gray-500' }}">
                            ✗ Salah
                        </span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Jawaban benar: <strong>{{ $jawabanBenarBs?->label ?? '-' }}</strong>
                </p>
            </div>
        @endif

        {{-- Kunci jawaban untuk Essay --}}
        @if($bankSoal->tipe_soal === 'essay' && $bankSoal->kunci_essay)
            <div class="mb-5 bg-orange-50 border border-orange-200 rounded-lg p-4">
                <p class="text-xs font-semibold text-orange-600 uppercase tracking-wide mb-2">Kunci Jawaban / Pedoman Penilaian</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $bankSoal->kunci_essay }}</p>
            </div>
        @endif

        {{-- Info tambahan --}}
        <div class="text-xs text-gray-400 border-t border-gray-100 pt-4 mt-4">
            Dibuat: {{ $bankSoal->created_at->translatedFormat('d F Y, H:i') }}
            @if($bankSoal->updated_at->ne($bankSoal->created_at))
                &bull; Diperbarui: {{ $bankSoal->updated_at->translatedFormat('d F Y, H:i') }}
            @endif
        </div>
    </div>

    {{-- Tombol aksi --}}
    <div class="flex gap-3 mt-4"
         x-data>
        <a href="{{ route('guru.bank-soal.edit', $bankSoal) }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Edit Soal
        </a>
        <form method="POST" action="{{ route('guru.bank-soal.destroy', $bankSoal) }}"
              @submit.prevent="if(confirm('Hapus soal ini secara permanen?')) $el.submit()">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                Hapus Soal
            </button>
        </form>
    </div>
</div>
@endsection
