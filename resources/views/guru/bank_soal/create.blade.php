@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('guru.bank-soal.index') }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Tambah Soal Baru</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">

        {{-- Form dengan Alpine.js untuk dinamis berdasarkan tipe soal --}}
        <form method="POST" action="{{ route('guru.bank-soal.store') }}" enctype="multipart/form-data"
              x-data="{
                  tipeSoal: '{{ old('tipe_soal', '') }}',
                  pilihan: [
                      { teks: '{{ old('pilihan.0.teks', '') }}' },
                      { teks: '{{ old('pilihan.1.teks', '') }}' },
                      { teks: '{{ old('pilihan.2.teks', '') }}' },
                      { teks: '{{ old('pilihan.3.teks', '') }}' }
                  ],
                  jawabanBenar: {{ old('jawaban_benar', 0) }},
                  jawabanBs: '{{ old('jawaban_bs', 'benar') }}',
                  previewGambar: null,
                  tambahPilihan() {
                      if (this.pilihan.length < 5) {
                          this.pilihan.push({ teks: '' });
                      }
                  },
                  hapusPilihan(index) {
                      if (this.pilihan.length > 2) {
                          this.pilihan.splice(index, 1);
                          if (this.jawabanBenar >= this.pilihan.length) {
                              this.jawabanBenar = 0;
                          }
                      }
                  }
              }">
            @csrf

            {{-- Mata Pelajaran --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Mata Pelajaran <span class="text-red-500">*</span>
                </label>
                <select name="mata_pelajaran_id"
                        class="w-full border {{ $errors->has('mata_pelajaran_id') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($mapelGuru as $mapel)
                        <option value="{{ $mapel->id }}" {{ old('mata_pelajaran_id') == $mapel->id ? 'selected' : '' }}>
                            {{ $mapel->nama_mapel }}
                        </option>
                    @endforeach
                </select>
                @error('mata_pelajaran_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipe Soal --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tipe Soal <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe_soal" value="pg"
                               x-model="tipeSoal"
                               class="text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Pilihan Ganda</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe_soal" value="bs"
                               x-model="tipeSoal"
                               class="text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Benar / Salah</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe_soal" value="essay"
                               x-model="tipeSoal"
                               class="text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Essay</span>
                    </label>
                </div>
                @error('tipe_soal')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Teks Pertanyaan --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Teks Pertanyaan <span class="text-red-500">*</span>
                </label>
                <textarea name="pertanyaan" rows="4"
                          placeholder="Tulis pertanyaan di sini..."
                          class="w-full border {{ $errors->has('pertanyaan') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('pertanyaan') }}</textarea>
                @error('pertanyaan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Upload Gambar (opsional) --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Gambar Soal <span class="text-gray-400 font-normal">(opsional, maks. 2MB)</span>
                </label>
                <input type="file" name="gambar" accept="image/*"
                       @change="previewGambar = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                       class="w-full border {{ $errors->has('gambar') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                {{-- Preview gambar --}}
                <div x-show="previewGambar" class="mt-2">
                    <img :src="previewGambar" class="max-h-48 rounded-lg border border-gray-200 dark:border-gray-700">
                </div>
                @error('gambar')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tingkat Kesulitan --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tingkat Kesulitan <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    @foreach(['mudah' => 'Mudah', 'sedang' => 'Sedang', 'sulit' => 'Sulit'] as $value => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tingkat_kesulitan" value="{{ $value }}"
                                   {{ old('tingkat_kesulitan', 'sedang') === $value ? 'checked' : '' }}
                                   class="text-green-600 focus:ring-green-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('tingkat_kesulitan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bobot Nilai --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Bobot Nilai <span class="text-red-500">*</span>
                </label>
                <input type="number" name="bobot_nilai" min="1" max="100"
                       value="{{ old('bobot_nilai', 1) }}"
                       class="w-28 border {{ $errors->has('bobot_nilai') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('bobot_nilai')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- BAGIAN DINAMIS: Pilihan Ganda --}}
            <div x-show="tipeSoal === 'pg'" x-cloak class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-800 mb-3">Pilihan Jawaban</h3>
                @error('pilihan')
                    <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                @enderror

                <template x-for="(item, index) in pilihan" :key="index">
                    <div class="flex items-center gap-3 mb-3">
                        {{-- Radio pilih jawaban benar --}}
                        <input type="radio" :name="'jawaban_benar_temp'" :value="index"
                               x-model="jawabanBenar"
                               class="text-green-600 focus:ring-green-500 flex-shrink-0"
                               title="Tandai sebagai jawaban benar">
                        {{-- Label huruf --}}
                        <span class="font-bold text-blue-700 w-5 flex-shrink-0"
                              x-text="['A','B','C','D','E'][index]"></span>
                        {{-- Input teks pilihan --}}
                        <input type="text" :name="'pilihan[' + index + '][teks]'"
                               x-model="item.teks"
                               placeholder="Teks pilihan jawaban..."
                               class="flex-1 border border-blue-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        {{-- Tombol hapus pilihan --}}
                        <button type="button" @click="hapusPilihan(index)"
                                x-show="pilihan.length > 2"
                                class="text-red-400 hover:text-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>

                {{-- Input hidden untuk nilai jawaban_benar --}}
                <input type="hidden" name="jawaban_benar" :value="jawabanBenar">

                {{-- Tombol tambah pilihan (maks 5) --}}
                <button type="button" @click="tambahPilihan"
                        x-show="pilihan.length < 5"
                        class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-medium">
                    + Tambah Pilihan
                </button>
                <p class="text-xs text-blue-600 mt-2">
                    Klik lingkaran di kiri untuk menandai jawaban yang benar.
                </p>
                @error('jawaban_benar')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- BAGIAN DINAMIS: Benar / Salah --}}
            <div x-show="tipeSoal === 'bs'" x-cloak class="mb-6 bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-purple-800 mb-3">Jawaban yang Benar</h3>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jawaban_bs" value="benar"
                               x-model="jawabanBs"
                               {{ old('jawaban_bs') === 'benar' ? 'checked' : '' }}
                               class="text-purple-600 focus:ring-purple-500">
                        <span class="text-sm font-medium text-green-700">✓ Benar</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jawaban_bs" value="salah"
                               x-model="jawabanBs"
                               {{ old('jawaban_bs') === 'salah' ? 'checked' : '' }}
                               class="text-purple-600 focus:ring-purple-500">
                        <span class="text-sm font-medium text-red-700">✗ Salah</span>
                    </label>
                </div>
                @error('jawaban_bs')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- BAGIAN DINAMIS: Essay --}}
            <div x-show="tipeSoal === 'essay'" x-cloak class="mb-6 bg-orange-50 border border-orange-200 rounded-lg p-4">
                <label class="block text-sm font-semibold text-orange-800 mb-2">
                    Kunci Jawaban / Pedoman Penilaian
                    <span class="text-orange-500 font-normal">(opsional)</span>
                </label>
                <textarea name="kunci_essay" rows="4"
                          placeholder="Tulis kunci jawaban atau pedoman penilaian untuk guru..."
                          class="w-full border border-orange-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('kunci_essay') }}</textarea>
            </div>

            {{-- Tombol aksi --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                    Simpan Soal
                </button>
                <a href="{{ route('guru.bank-soal.index') }}"
                   class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg text-sm font-medium transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
