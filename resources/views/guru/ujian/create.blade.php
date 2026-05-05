@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('guru.ujian.index') }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Buat Ujian Baru</h1>
    </div>

    {{-- Info token --}}
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-700 flex items-start gap-2">
        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Token akses ujian akan digenerate otomatis setelah ujian dibuat (format: MAPEL-XXXX).</span>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('guru.ujian.store') }}">
            @csrf

            {{-- Judul Ujian --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Judul Ujian <span class="text-red-500">*</span>
                </label>
                <input type="text" name="judul" value="{{ old('judul') }}"
                       placeholder="cth: Ulangan Harian Bab 1 — PPKn"
                       class="w-full border {{ $errors->has('judul') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                @error('judul')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Mata Pelajaran --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Mata Pelajaran <span class="text-red-500">*</span>
                </label>
                @if($mapelGuru->isEmpty())
                    <p class="text-sm text-red-500">Kamu belum di-assign ke mata pelajaran apapun. Hubungi admin.</p>
                @else
                    <select name="mata_pelajaran_id"
                            class="w-full border {{ $errors->has('mata_pelajaran_id') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mapelGuru as $mapel)
                            <option value="{{ $mapel->id }}" {{ old('mata_pelajaran_id') == $mapel->id ? 'selected' : '' }}>
                                {{ $mapel->nama_mapel }} ({{ $mapel->kode_mapel }})
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('mata_pelajaran_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Kelas --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Kelas <span class="text-red-500">*</span>
                </label>
                @if($kelasList->isEmpty())
                    <p class="text-sm text-red-500">Belum ada kelas aktif. Hubungi admin.</p>
                @else
                    <select name="kelas_id"
                            class="w-full border {{ $errors->has('kelas_id') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('kelas_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Durasi --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Durasi Ujian <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <input type="number" name="durasi_menit" value="{{ old('durasi_menit', 60) }}"
                           min="5" max="240"
                           class="w-32 border {{ $errors->has('durasi_menit') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <span class="text-sm text-gray-500 dark:text-gray-400">menit (5–240 menit)</span>
                </div>
                @error('durasi_menit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Toggle Acak Soal & Jawaban --}}
            <div class="mb-4 grid grid-cols-2 gap-4" x-data="{
                acakSoal: {{ old('acak_soal', 1) ? 'true' : 'false' }},
                acakJawaban: {{ old('acak_jawaban', 1) ? 'true' : 'false' }}
            }">
                {{-- Acak Soal --}}
                <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-900">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Acak Urutan Soal</p>
                        <p class="text-xs text-gray-400">Soal diacak per siswa</p>
                    </div>
                    <div class="relative">
                        <input type="hidden" name="acak_soal" :value="acakSoal ? '1' : '0'">
                        <div @click="acakSoal = !acakSoal"
                             :class="acakSoal ? 'bg-green-500' : 'bg-gray-300'"
                             class="w-10 h-5 rounded-full transition-colors duration-200 cursor-pointer">
                            <div :class="acakSoal ? 'translate-x-5' : 'translate-x-0'"
                                 class="w-5 h-5 bg-white dark:bg-gray-800 rounded-full shadow transform transition-transform duration-200"></div>
                        </div>
                    </div>
                </label>

                {{-- Acak Jawaban --}}
                <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-900">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Acak Urutan Jawaban</p>
                        <p class="text-xs text-gray-400">Pilihan jawaban diacak</p>
                    </div>
                    <div class="relative">
                        <input type="hidden" name="acak_jawaban" :value="acakJawaban ? '1' : '0'">
                        <div @click="acakJawaban = !acakJawaban"
                             :class="acakJawaban ? 'bg-green-500' : 'bg-gray-300'"
                             class="w-10 h-5 rounded-full transition-colors duration-200 cursor-pointer">
                            <div :class="acakJawaban ? 'translate-x-5' : 'translate-x-0'"
                                 class="w-5 h-5 bg-white dark:bg-gray-800 rounded-full shadow transform transition-transform duration-200"></div>
                        </div>
                    </div>
                </label>
            </div>

            {{-- Deskripsi --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Deskripsi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="deskripsi" rows="3"
                          placeholder="Catatan atau petunjuk ujian untuk siswa..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none resize-none">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg text-sm font-semibold transition">
                    Buat Ujian
                </button>
                <a href="{{ route('guru.ujian.index') }}"
                   class="flex-1 text-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 py-2.5 rounded-lg text-sm font-semibold transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
