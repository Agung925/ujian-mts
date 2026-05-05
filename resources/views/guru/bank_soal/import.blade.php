@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Breadcrumb --}}
    <div class="text-sm text-gray-500 mb-6">
        <a href="{{ route('guru.bank-soal.index') }}" class="hover:text-green-600">Bank Soal</a>
        <span class="mx-2">›</span>
        <span class="text-gray-700">Import Soal via Excel</span>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-1">Import Soal via Excel</h1>
    <p class="text-gray-500 text-sm mb-6">
        Upload file Excel berisi soal untuk dimasukkan ke bank soal kamu sekaligus.
    </p>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-4 text-sm">
            ⚠️ {{ session('warning') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Error log detail baris yang gagal --}}
    @if(session('import_errors'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="font-semibold text-red-700 text-sm mb-2">Detail baris yang gagal:</p>
            <ul class="text-xs text-red-600 space-y-1 max-h-40 overflow-y-auto">
                @foreach(session('import_errors') as $err)
                    <li>• {{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Panduan Format Excel --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
        <h3 class="font-semibold text-blue-800 mb-3">📋 Panduan Format Excel</h3>
        <div class="overflow-x-auto">
            <table class="text-xs w-full border-collapse">
                <thead>
                    <tr class="bg-blue-100">
                        <th class="border border-blue-200 px-2 py-1 text-left">Kolom</th>
                        <th class="border border-blue-200 px-2 py-1 text-left">Keterangan</th>
                        <th class="border border-blue-200 px-2 py-1 text-left">Contoh</th>
                    </tr>
                </thead>
                <tbody class="text-blue-700">
                    <tr>
                        <td class="border border-blue-200 px-2 py-1 font-mono">tipe_soal</td>
                        <td class="border border-blue-200 px-2 py-1">pg / bs / essay</td>
                        <td class="border border-blue-200 px-2 py-1">pg</td>
                    </tr>
                    <tr>
                        <td class="border border-blue-200 px-2 py-1 font-mono">pertanyaan</td>
                        <td class="border border-blue-200 px-2 py-1">Teks soal lengkap</td>
                        <td class="border border-blue-200 px-2 py-1">Pancasila terdiri dari...</td>
                    </tr>
                    <tr>
                        <td class="border border-blue-200 px-2 py-1 font-mono">kategori</td>
                        <td class="border border-blue-200 px-2 py-1">Lihat daftar di template Excel</td>
                        <td class="border border-blue-200 px-2 py-1">Penilaian Harian</td>
                    </tr>
                    <tr>
                        <td class="border border-blue-200 px-2 py-1 font-mono">sub_kategori</td>
                        <td class="border border-blue-200 px-2 py-1">Sub kategori dari kategori di atas</td>
                        <td class="border border-blue-200 px-2 py-1">Asesmen Formatif (Harian)</td>
                    </tr>
                    <tr>
                        <td class="border border-blue-200 px-2 py-1 font-mono">bobot_nilai</td>
                        <td class="border border-blue-200 px-2 py-1">Angka 1–100</td>
                        <td class="border border-blue-200 px-2 py-1">1</td>
                    </tr>
                    <tr>
                        <td class="border border-blue-200 px-2 py-1 font-mono">pilihan_a s/d e</td>
                        <td class="border border-blue-200 px-2 py-1">Teks pilihan (PG saja, e opsional)</td>
                        <td class="border border-blue-200 px-2 py-1">5 Sila</td>
                    </tr>
                    <tr>
                        <td class="border border-blue-200 px-2 py-1 font-mono">jawaban_benar</td>
                        <td class="border border-blue-200 px-2 py-1">PG: A/B/C/D/E — BS: Benar/Salah</td>
                        <td class="border border-blue-200 px-2 py-1">C</td>
                    </tr>
                    <tr>
                        <td class="border border-blue-200 px-2 py-1 font-mono">kunci_essay</td>
                        <td class="border border-blue-200 px-2 py-1">Pedoman penilaian (Essay, opsional)</td>
                        <td class="border border-blue-200 px-2 py-1">—</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-3 flex items-center gap-3">
            <a href="{{ route('guru.bank-soal.template-excel') }}"
               class="inline-flex items-center gap-1.5 text-sm text-green-700 hover:text-green-800 font-medium border border-green-300 bg-white px-3 py-1.5 rounded-lg hover:bg-green-50 transition">
                ⬇️ Download Template Excel
            </a>
            <span class="text-xs text-blue-500">Sudah berisi contoh soal PG, BS, dan Essay</span>
        </div>
    </div>

    {{-- Referensi Kategori & Sub Kategori --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-6"
         x-data="{ terbuka: false }">
        <button type="button"
                @click="terbuka = !terbuka"
                class="w-full flex items-center justify-between text-left">
            <div>
                <span class="font-semibold text-amber-800">📚 Daftar Kategori &amp; Sub Kategori</span>
                <span class="text-xs text-amber-600 ml-2">— salin teks persis seperti tertulis ke kolom Excel</span>
            </div>
            <svg class="w-4 h-4 text-amber-600 transition-transform" :class="terbuka ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="terbuka" x-transition class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
            @php $daftarKategori = \App\Models\BankSoal::daftarKategori(); @endphp

            @foreach($daftarKategori as $kategori => $subList)
            <div class="bg-white border border-amber-200 rounded-lg overflow-hidden">
                {{-- Header kategori --}}
                <div class="bg-amber-100 px-3 py-2">
                    <p class="text-xs font-bold text-amber-900 uppercase tracking-wide">Kolom: kategori</p>
                    <p class="text-sm font-semibold text-amber-800 mt-0.5 select-all cursor-pointer"
                       title="Klik untuk menyorot teks">{{ $kategori }}</p>
                </div>
                {{-- Daftar sub kategori --}}
                <ul class="px-3 py-2 space-y-1">
                    <p class="text-xs font-semibold text-gray-500 mb-1">Pilihan sub_kategori:</p>
                    @foreach($subList as $sub)
                    <li class="flex items-start gap-1.5">
                        <span class="text-amber-400 mt-0.5">›</span>
                        <span class="text-xs text-gray-700 select-all cursor-pointer"
                              title="Klik untuk menyorot teks">{{ $sub }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>

        <p x-show="!terbuka" class="text-xs text-amber-600 mt-2">
            Klik untuk melihat 4 kategori dan 12 sub kategori yang tersedia.
        </p>
    </div>

    {{-- Form Upload --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
         x-data="{ namaFile: '' }">
        <h2 class="font-semibold text-gray-700 mb-4">Upload File Excel</h2>

        <form method="POST"
              action="{{ route('guru.bank-soal.proses-import') }}"
              enctype="multipart/form-data">
            @csrf

            {{-- Pilih Mata Pelajaran --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Mata Pelajaran <span class="text-red-500">*</span>
                </label>
                @if($mapelGuru->isEmpty())
                    <p class="text-sm text-red-500">Kamu belum di-assign ke mata pelajaran apapun. Hubungi admin.</p>
                @else
                    <select name="mata_pelajaran_id" required
                            class="w-full px-3 py-2 border {{ $errors->has('mata_pelajaran_id') ? 'border-red-400' : 'border-gray-300' }} rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mapelGuru as $mapel)
                            <option value="{{ $mapel->id }}" {{ old('mata_pelajaran_id') == $mapel->id ? 'selected' : '' }}>
                                {{ $mapel->nama_mapel }} ({{ $mapel->kode_mapel }})
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('mata_pelajaran_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Upload File dengan drag area --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    File Excel <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed rounded-lg p-6 text-center transition cursor-pointer"
                     :class="namaFile ? 'border-green-400 bg-green-50' : 'border-gray-300 hover:border-green-400'"
                     @click="$refs.fileInput.click()">
                    <input type="file"
                           name="file_excel"
                           accept=".xlsx,.xls"
                           required
                           x-ref="fileInput"
                           class="hidden"
                           @change="namaFile = $event.target.files[0]?.name || ''">
                    <div x-show="!namaFile">
                        <p class="text-gray-500 text-sm">Klik untuk pilih file Excel</p>
                        <p class="text-gray-400 text-xs mt-1">Format: .xlsx atau .xls — Maks 5MB</p>
                    </div>
                    <div x-show="namaFile" class="text-green-700">
                        <p class="font-medium text-sm">📄 <span x-text="namaFile"></span></p>
                        <p class="text-xs mt-1 text-gray-500">Klik untuk ganti file</p>
                    </div>
                </div>
                @error('file_excel')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex gap-3">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition">
                    ⬆️ Mulai Import
                </button>
                <a href="{{ route('guru.bank-soal.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-6 py-2.5 rounded-lg text-sm transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
