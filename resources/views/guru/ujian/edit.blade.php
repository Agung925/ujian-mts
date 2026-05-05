@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('guru.ujian.show', $ujian) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit Ujian</h1>
    </div>

    {{-- Info read-only --}}
    <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 rounded-lg grid grid-cols-2 gap-4 text-sm">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Mata Pelajaran</p>
            <p class="font-medium text-gray-700 dark:text-gray-300 mt-0.5">{{ $ujian->mataPelajaran->nama_mapel ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Kelas</p>
            <p class="font-medium text-gray-700 dark:text-gray-300 mt-0.5">{{ $ujian->kelas->nama_kelas ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Token</p>
            <p class="font-mono font-bold text-green-700 mt-0.5">{{ $ujian->token }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Status</p>
            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-xs font-medium {{ $ujian->warna_badge_status }}">
                {{ ucfirst($ujian->status) }}
            </span>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('guru.ujian.update', $ujian) }}"
              x-data="{
                acakSoal: {{ $ujian->acak_soal ? 'true' : 'false' }},
                acakJawaban: {{ $ujian->acak_jawaban ? 'true' : 'false' }}
              }">
            @csrf @method('PUT')

            {{-- Judul --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Judul Ujian <span class="text-red-500">*</span>
                </label>
                <input type="text" name="judul" value="{{ old('judul', $ujian->judul) }}"
                       class="w-full border {{ $errors->has('judul') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                @error('judul')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Durasi --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Durasi Ujian <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <input type="number" name="durasi_menit" value="{{ old('durasi_menit', $ujian->durasi_menit) }}"
                           min="5" max="240"
                           class="w-32 border {{ $errors->has('durasi_menit') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <span class="text-sm text-gray-500 dark:text-gray-400">menit</span>
                </div>
                @error('durasi_menit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Toggle Acak --}}
            <div class="mb-4 grid grid-cols-2 gap-4">
                {{-- Acak Soal --}}
                <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-900">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Acak Urutan Soal</p>
                        <p class="text-xs text-gray-400">Soal diacak per siswa</p>
                    </div>
                    <input type="hidden" name="acak_soal" :value="acakSoal ? '1' : '0'">
                    <div @click="acakSoal = !acakSoal"
                         :class="acakSoal ? 'bg-green-500' : 'bg-gray-300'"
                         class="w-10 h-5 rounded-full transition-colors duration-200 cursor-pointer">
                        <div :class="acakSoal ? 'translate-x-5' : 'translate-x-0'"
                             class="w-5 h-5 bg-white dark:bg-gray-800 rounded-full shadow transform transition-transform duration-200"></div>
                    </div>
                </label>

                {{-- Acak Jawaban --}}
                <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-900">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Acak Urutan Jawaban</p>
                        <p class="text-xs text-gray-400">Pilihan jawaban diacak</p>
                    </div>
                    <input type="hidden" name="acak_jawaban" :value="acakJawaban ? '1' : '0'">
                    <div @click="acakJawaban = !acakJawaban"
                         :class="acakJawaban ? 'bg-green-500' : 'bg-gray-300'"
                         class="w-10 h-5 rounded-full transition-colors duration-200 cursor-pointer">
                        <div :class="acakJawaban ? 'translate-x-5' : 'translate-x-0'"
                             class="w-5 h-5 bg-white dark:bg-gray-800 rounded-full shadow transform transition-transform duration-200"></div>
                    </div>
                </label>
            </div>

            {{-- Deskripsi --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Deskripsi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="deskripsi" rows="3"
                          class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none resize-none">{{ old('deskripsi', $ujian->deskripsi) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg text-sm font-semibold transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('guru.ujian.show', $ujian) }}"
                   class="flex-1 text-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 py-2.5 rounded-lg text-sm font-semibold transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
