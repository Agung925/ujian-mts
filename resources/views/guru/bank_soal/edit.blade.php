@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('guru.bank-soal.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Soal</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Tipe soal:
                @if($bankSoal->tipe_soal === 'pg')
                    <span class="font-semibold text-blue-700">Pilihan Ganda</span>
                @elseif($bankSoal->tipe_soal === 'bs')
                    <span class="font-semibold text-purple-700">Benar / Salah</span>
                @else
                    <span class="font-semibold text-orange-700">Essay</span>
                @endif
                (tidak bisa diubah)
            </p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        {{-- Alpine.js: isi awal dari data soal yang ada --}}
        @php
            $pilihanLama = $bankSoal->pilihanJawaban;
            $jawabanBenarIndex = 0;
            if ($bankSoal->tipe_soal === 'pg') {
                foreach ($pilihanLama as $i => $p) {
                    if ($p->is_benar) { $jawabanBenarIndex = $i; break; }
                }
            }
            $jawabanBsLama = 'benar';
            if ($bankSoal->tipe_soal === 'bs') {
                $jawabanBsLama = $pilihanLama->firstWhere('is_benar', true)?->label === 'Benar' ? 'benar' : 'salah';
            }
            $pilihanJson = $bankSoal->tipe_soal === 'pg'
                ? $pilihanLama->map(fn($p) => ['teks' => $p->teks_pilihan])->values()->toJson()
                : '[]';
        @endphp

        <form method="POST" action="{{ route('guru.bank-soal.update', $bankSoal) }}"
              enctype="multipart/form-data"
              x-data="{
                  tipeSoal: '{{ $bankSoal->tipe_soal }}',
                  pilihan: {{ $pilihanJson }},
                  jawabanBenar: {{ old('jawaban_benar', $jawabanBenarIndex) }},
                  jawabanBs: '{{ old('jawaban_bs', $jawabanBsLama) }}',
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
            @method('PUT')

            {{-- Mata Pelajaran --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Mata Pelajaran <span class="text-red-500">*</span>
                </label>
                <select name="mata_pelajaran_id"
                        class="w-full border {{ $errors->has('mata_pelajaran_id') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($mapelGuru as $mapel)
                        <option value="{{ $mapel->id }}"
                            {{ old('mata_pelajaran_id', $bankSoal->mata_pelajaran_id) == $mapel->id ? 'selected' : '' }}>
                            {{ $mapel->nama_mapel }}
                        </option>
                    @endforeach
                </select>
                @error('mata_pelajaran_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Teks Pertanyaan --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Teks Pertanyaan <span class="text-red-500">*</span>
                </label>
                <textarea name="pertanyaan" rows="4"
                          class="w-full border {{ $errors->has('pertanyaan') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('pertanyaan', $bankSoal->pertanyaan) }}</textarea>
                @error('pertanyaan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Upload Gambar --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Gambar Soal <span class="text-gray-400 font-normal">(kosongkan jika tidak ingin mengubah)</span>
                </label>
                {{-- Tampilkan gambar lama jika ada --}}
                @if($bankSoal->gambar)
                    <div class="mb-2">
                        <img src="{{ $bankSoal->url_gambar }}" class="max-h-40 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 mt-1">Gambar saat ini</p>
                    </div>
                @endif
                <input type="file" name="gambar" accept="image/*"
                       @change="previewGambar = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                       class="w-full border {{ $errors->has('gambar') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm">
                <div x-show="previewGambar" class="mt-2">
                    <img :src="previewGambar" class="max-h-40 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mt-1">Preview gambar baru</p>
                </div>
                @error('gambar')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tingkat Kesulitan --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tingkat Kesulitan <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    @foreach(['mudah' => 'Mudah', 'sedang' => 'Sedang', 'sulit' => 'Sulit'] as $value => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tingkat_kesulitan" value="{{ $value }}"
                                   {{ old('tingkat_kesulitan', $bankSoal->tingkat_kesulitan) === $value ? 'checked' : '' }}
                                   class="text-green-600 focus:ring-green-500">
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('tingkat_kesulitan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bobot Nilai --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Bobot Nilai <span class="text-red-500">*</span>
                </label>
                <input type="number" name="bobot_nilai" min="1" max="100"
                       value="{{ old('bobot_nilai', $bankSoal->bobot_nilai) }}"
                       class="w-28 border {{ $errors->has('bobot_nilai') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('bobot_nilai')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Pilihan Ganda --}}
            @if($bankSoal->tipe_soal === 'pg')
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-800 mb-3">Pilihan Jawaban</h3>
                @error('pilihan')
                    <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                @enderror

                <template x-for="(item, index) in pilihan" :key="index">
                    <div class="flex items-center gap-3 mb-3">
                        <input type="radio" :value="index" x-model="jawabanBenar"
                               class="text-green-600 focus:ring-green-500 flex-shrink-0"
                               title="Tandai sebagai jawaban benar">
                        <span class="font-bold text-blue-700 w-5 flex-shrink-0"
                              x-text="['A','B','C','D','E'][index]"></span>
                        <input type="text" :name="'pilihan[' + index + '][teks]'"
                               x-model="item.teks"
                               class="flex-1 border border-blue-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <button type="button" @click="hapusPilihan(index)"
                                x-show="pilihan.length > 2"
                                class="text-red-400 hover:text-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>

                <input type="hidden" name="jawaban_benar" :value="jawabanBenar">

                <button type="button" @click="tambahPilihan"
                        x-show="pilihan.length < 5"
                        class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-medium">
                    + Tambah Pilihan
                </button>
                <p class="text-xs text-blue-600 mt-2">Klik lingkaran di kiri untuk menandai jawaban yang benar.</p>
                @error('jawaban_benar')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

            {{-- Benar / Salah --}}
            @if($bankSoal->tipe_soal === 'bs')
            <div class="mb-6 bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-purple-800 mb-3">Jawaban yang Benar</h3>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jawaban_bs" value="benar"
                               x-model="jawabanBs"
                               class="text-purple-600 focus:ring-purple-500">
                        <span class="text-sm font-medium text-green-700">✓ Benar</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jawaban_bs" value="salah"
                               x-model="jawabanBs"
                               class="text-purple-600 focus:ring-purple-500">
                        <span class="text-sm font-medium text-red-700">✗ Salah</span>
                    </label>
                </div>
                @error('jawaban_bs')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

            {{-- Essay --}}
            @if($bankSoal->tipe_soal === 'essay')
            <div class="mb-6 bg-orange-50 border border-orange-200 rounded-lg p-4">
                <label class="block text-sm font-semibold text-orange-800 mb-2">
                    Kunci Jawaban / Pedoman Penilaian
                    <span class="text-orange-500 font-normal">(opsional)</span>
                </label>
                <textarea name="kunci_essay" rows="4"
                          class="w-full border border-orange-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('kunci_essay', $bankSoal->kunci_essay) }}</textarea>
            </div>
            @endif

            {{-- Tombol aksi --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('guru.bank-soal.show', $bankSoal) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
