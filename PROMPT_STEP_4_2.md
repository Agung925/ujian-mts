# 📥 PROMPT STEP 4 TAMBAHAN — Import Soal via Excel

> Prompt ini adalah TAMBAHAN untuk Step 4 (Bank Soal).
> Pastikan Step 4 sudah selesai sebelum menjalankan prompt ini.
> Paste ke GitHub Copilot Chat (Agent Mode) di VS Code.

---

## ▶️ PROMPT STEP 4 TAMBAHAN (Copy dari garis bawah ini):

---

Kamu adalah AI developer assistant untuk project **ujian-mts**.
Sebelum mulai, baca file `SKILL.md` di root project.

Kita menambahkan fitur **Import Soal via Excel** ke modul Bank Soal (Step 4).
Fitur ini memungkinkan guru mengupload file Excel berisi banyak soal sekaligus.

---

### 📋 KONTEKS PENTING:

Format Excel yang akan digunakan — **1 sheet, semua tipe soal dalam 1 file**:

| Kolom | Keterangan | Contoh |
|-------|-----------|--------|
| `tipe_soal` | pg / bs / essay | pg |
| `pertanyaan` | Teks soal lengkap | Pancasila terdiri dari... |
| `tingkat_kesulitan` | mudah / sedang / sulit | mudah |
| `bobot_nilai` | Angka 1-100 | 1 |
| `pilihan_a` | Teks pilihan A (PG saja) | 3 sila |
| `pilihan_b` | Teks pilihan B (PG saja) | 4 sila |
| `pilihan_c` | Teks pilihan C (PG saja) | 5 sila |
| `pilihan_d` | Teks pilihan D (PG saja) | 6 sila |
| `pilihan_e` | Teks pilihan E (opsional, PG saja) | 7 sila |
| `jawaban_benar` | Untuk PG: A/B/C/D/E, untuk BS: Benar/Salah | C |
| `kunci_essay` | Pedoman penilaian (Essay saja, opsional) | - |

Aturan import:
- Kolom `pilihan_a` s/d `pilihan_e` dikosongkan untuk tipe BS dan Essay
- Kolom `jawaban_benar` untuk BS diisi: `Benar` atau `Salah`
- Kolom `kunci_essay` hanya diisi untuk tipe Essay
- Baris yang error di-skip, tidak menghentikan seluruh import
- Guru hanya bisa import soal untuk **mapel yang diajarnya**
- `mata_pelajaran_id` ditentukan dari parameter yang dipilih sebelum upload

---

### 🗂️ TUGAS 1 — Buat Import Class

```bash
php artisan make:import SoalImport --model=BankSoal
```

**Isi `app/Imports/SoalImport.php`:**
```php
<?php

namespace App\Imports;

use App\Models\BankSoal;
use App\Models\PilihanJawaban;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SoalImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    // Simpan hasil import untuk ditampilkan ke guru
    public int $berhasil  = 0;
    public int $gagal     = 0;
    public array $errorLog = [];

    public function __construct(
        private int $guruId,
        private int $mataPelajaranId
    ) {}

    /**
     * Proses setiap chunk data dari Excel.
     * Menggunakan ToCollection agar bisa validasi per baris secara manual.
     */
    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $nomorBaris = $index + 2; // +2 karena baris 1 = header

            // Konversi row ke array biasa
            $data = $row->toArray();

            // Normalisasi: lowercase semua key, trim spasi
            $data = array_map('trim', $data);
            $tipeSoal = strtolower($data['tipe_soal'] ?? '');

            // Validasi dasar per baris
            $validator = Validator::make($data, [
                'tipe_soal'          => 'required|in:pg,bs,essay',
                'pertanyaan'         => 'required|string|min:5',
                'tingkat_kesulitan'  => 'required|in:mudah,sedang,sulit',
                'bobot_nilai'        => 'required|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                $this->gagal++;
                $this->errorLog[] = "Baris {$nomorBaris}: " . implode(', ', $validator->errors()->all());
                continue;
            }

            // Validasi tambahan per tipe soal
            if ($tipeSoal === 'pg') {
                if (empty($data['pilihan_a']) || empty($data['pilihan_b'])) {
                    $this->gagal++;
                    $this->errorLog[] = "Baris {$nomorBaris}: Soal PG minimal harus punya Pilihan A dan B.";
                    continue;
                }
                $jawaban = strtoupper(trim($data['jawaban_benar'] ?? ''));
                if (!in_array($jawaban, ['A', 'B', 'C', 'D', 'E'])) {
                    $this->gagal++;
                    $this->errorLog[] = "Baris {$nomorBaris}: Jawaban benar PG harus A, B, C, D, atau E.";
                    continue;
                }
            }

            if ($tipeSoal === 'bs') {
                $jawaban = ucfirst(strtolower(trim($data['jawaban_benar'] ?? '')));
                if (!in_array($jawaban, ['Benar', 'Salah'])) {
                    $this->gagal++;
                    $this->errorLog[] = "Baris {$nomorBaris}: Jawaban benar BS harus 'Benar' atau 'Salah'.";
                    continue;
                }
            }

            try {
                // Simpan soal utama
                $soal = BankSoal::create([
                    'guru_id'           => $this->guruId,
                    'mata_pelajaran_id' => $this->mataPelajaranId,
                    'pertanyaan'        => $data['pertanyaan'],
                    'tipe_soal'         => $tipeSoal,
                    'tingkat_kesulitan' => $data['tingkat_kesulitan'],
                    'bobot_nilai'       => (int) $data['bobot_nilai'],
                    'kunci_essay'       => $data['kunci_essay'] ?? null,
                    'is_aktif'          => true,
                ]);

                // Simpan pilihan jawaban berdasarkan tipe
                if ($tipeSoal === 'pg') {
                    $this->simpanPilihanPG($soal, $data);
                }

                if ($tipeSoal === 'bs') {
                    $this->simpanPilihanBS($soal, $data);
                }

                // Essay tidak perlu pilihan jawaban

                $this->berhasil++;

            } catch (\Exception $e) {
                $this->gagal++;
                $this->errorLog[] = "Baris {$nomorBaris}: Gagal disimpan — " . $e->getMessage();
            }
        }
    }

    /**
     * Simpan pilihan jawaban untuk soal Pilihan Ganda
     */
    private function simpanPilihanPG(BankSoal $soal, array $data): void
    {
        $labelPilihan = ['A', 'B', 'C', 'D', 'E'];
        $kolomPilihan = ['pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'pilihan_e'];
        $jawabanBenar = strtoupper(trim($data['jawaban_benar']));

        foreach ($labelPilihan as $i => $label) {
            $kolom = $kolomPilihan[$i];
            $teks  = trim($data[$kolom] ?? '');

            // Skip jika pilihan kosong (misal tidak ada pilihan E)
            if (empty($teks)) continue;

            PilihanJawaban::create([
                'soal_id'      => $soal->id,
                'label'        => $label,
                'teks_pilihan' => $teks,
                'is_benar'     => ($label === $jawabanBenar),
            ]);
        }
    }

    /**
     * Simpan pilihan jawaban untuk soal Benar/Salah
     */
    private function simpanPilihanBS(BankSoal $soal, array $data): void
    {
        $jawabanBenar = ucfirst(strtolower(trim($data['jawaban_benar'])));

        PilihanJawaban::create([
            'soal_id'      => $soal->id,
            'label'        => 'Benar',
            'teks_pilihan' => 'Benar',
            'is_benar'     => ($jawabanBenar === 'Benar'),
        ]);

        PilihanJawaban::create([
            'soal_id'      => $soal->id,
            'label'        => 'Salah',
            'teks_pilihan' => 'Salah',
            'is_benar'     => ($jawabanBenar === 'Salah'),
        ]);
    }

    /**
     * Baca file Excel per 200 baris agar tidak memory overflow
     * untuk file besar
     */
    public function chunkSize(): int
    {
        return 200;
    }
}
```

---

### 📤 TUGAS 2 — Buat Export Class untuk Template Excel

```bash
php artisan make:export TemplateSoalExport
```

**Isi `app/Exports/TemplateSoalExport.php`:**
```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateSoalExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    /**
     * Data contoh untuk template — 3 baris contoh (PG, BS, Essay)
     */
    public function array(): array
    {
        return [
            // Contoh soal Pilihan Ganda
            [
                'pg',
                'Pancasila terdiri dari berapa sila?',
                'mudah',
                '1',
                '3 Sila',
                '4 Sila',
                '5 Sila',
                '6 Sila',
                '',
                'C',
                '',
            ],
            // Contoh soal Benar/Salah
            [
                'bs',
                'Ibu kota Indonesia adalah Jakarta.',
                'mudah',
                '1',
                '',
                '',
                '',
                '',
                '',
                'Benar',
                '',
            ],
            // Contoh soal Essay
            [
                'essay',
                'Jelaskan pengertian Pancasila sebagai dasar negara!',
                'sedang',
                '5',
                '',
                '',
                '',
                '',
                '',
                '',
                'Pancasila adalah dasar negara Indonesia yang terdiri dari 5 sila...',
            ],
        ];
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'tipe_soal',
            'pertanyaan',
            'tingkat_kesulitan',
            'bobot_nilai',
            'pilihan_a',
            'pilihan_b',
            'pilihan_c',
            'pilihan_d',
            'pilihan_e',
            'jawaban_benar',
            'kunci_essay',
        ];
    }

    /**
     * Judul sheet Excel
     */
    public function title(): string
    {
        return 'Template Soal';
    }

    /**
     * Style header: bold, background hijau, teks putih
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            // Row 1 = header
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '16a34a']],
            ],
            // Row 2 = contoh PG (background kuning muda)
            2 => [
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFFDE7']],
            ],
            // Row 3 = contoh BS (background biru muda)
            3 => [
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E3F2FD']],
            ],
            // Row 4 = contoh Essay (background oranye muda)
            4 => [
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFF3E0']],
            ],
        ];
    }

    /**
     * Lebar kolom agar mudah dibaca
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // tipe_soal
            'B' => 50,  // pertanyaan
            'C' => 20,  // tingkat_kesulitan
            'D' => 12,  // bobot_nilai
            'E' => 25,  // pilihan_a
            'F' => 25,  // pilihan_b
            'G' => 25,  // pilihan_c
            'H' => 25,  // pilihan_d
            'I' => 25,  // pilihan_e
            'J' => 15,  // jawaban_benar
            'K' => 50,  // kunci_essay
        ];
    }
}
```

---

### 🎛️ TUGAS 3 — Update BankSoalController Guru

Edit `app/Http/Controllers/Guru/BankSoalController.php` — tambahkan 3 method berikut di dalam class (setelah method `destroy`):

```php
/**
 * Tampilkan form import soal via Excel
 */
public function formImport()
{
    // Guru hanya bisa import ke mapel yang diajarnya
    $mapelGuru = auth()->user()->mataPelajaran()->aktif()->get();
    return view('guru.bank_soal.import', compact('mapelGuru'));
}

/**
 * Proses upload dan import file Excel soal
 */
public function prosesImport(Request $request)
{
    $request->validate([
        'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
        'file_excel'        => 'required|mimes:xlsx,xls|max:5120', // max 5MB
    ], [
        'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
        'file_excel.required'        => 'File Excel wajib diupload.',
        'file_excel.mimes'           => 'File harus berformat .xlsx atau .xls.',
        'file_excel.max'             => 'Ukuran file maksimal 5MB.',
    ]);

    // Pastikan guru hanya bisa import ke mapel yang diajarnya
    $mapelGuru = auth()->user()->mataPelajaran()->pluck('id')->toArray();
    if (!in_array($request->mata_pelajaran_id, $mapelGuru)) {
        return back()->with('error', 'Kamu tidak mengajar mata pelajaran ini.');
    }

    // Jalankan import
    $import = new \App\Imports\SoalImport(
        guruId:          auth()->id(),
        mataPelajaranId: $request->mata_pelajaran_id
    );

    \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file_excel'));

    // Tampilkan hasil import
    $pesan = "Import selesai: {$import->berhasil} soal berhasil, {$import->gagal} gagal.";

    if (!empty($import->errorLog)) {
        // Simpan error log ke session untuk ditampilkan di view
        session(['import_errors' => $import->errorLog]);
    }

    $tipe = $import->gagal > 0 ? 'warning' : 'success';

    return redirect()->route('guru.bank-soal.index')->with($tipe, $pesan);
}

/**
 * Download template Excel untuk import soal
 */
public function downloadTemplate()
{
    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\TemplateSoalExport(),
        'template_import_soal.xlsx'
    );
}
```

---

### 🗺️ TUGAS 4 — Tambah Routes

Tambahkan di dalam route group GURU di `routes/web.php`:

```php
// Import Soal — WAJIB di atas Route::resource agar tidak bentrok
Route::get('bank-soal/import', [\App\Http\Controllers\Guru\BankSoalController::class, 'formImport'])->name('bank-soal.form-import');
Route::post('bank-soal/import', [\App\Http\Controllers\Guru\BankSoalController::class, 'prosesImport'])->name('bank-soal.proses-import');
Route::get('bank-soal/template-excel', [\App\Http\Controllers\Guru\BankSoalController::class, 'downloadTemplate'])->name('bank-soal.template-excel');

// Route::resource tetap di bawah
Route::resource('bank-soal', \App\Http\Controllers\Guru\BankSoalController::class);
```

> ⚠️ PENTING: Route spesifik (`/import`, `/template-excel`) WAJIB ditulis
> SEBELUM `Route::resource` agar tidak ditangkap sebagai `{bankSoal}` parameter!

---

### 🎨 TUGAS 5 — Buat View Import Soal

Buat file `resources/views/guru/bank_soal/import.blade.php`:

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Soal — Sistem Ujian MTs</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen" x-data>

    {{-- Navbar sederhana --}}
    <nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
        <span class="font-semibold text-primary-600">Sistem Ujian MTs</span>
        <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
    </nav>

    <div class="max-w-3xl mx-auto py-8 px-4">

        {{-- Breadcrumb --}}
        <div class="text-sm text-gray-500 mb-6">
            <a href="{{ route('guru.bank-soal.index') }}" class="hover:text-primary-600">Bank Soal</a>
            <span class="mx-2">›</span>
            <span class="text-gray-700">Import Soal via Excel</span>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-2">Import Soal via Excel</h1>
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

        {{-- Error log detail --}}
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

        {{-- Petunjuk Format Excel --}}
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
                        <tr><td class="border border-blue-200 px-2 py-1 font-mono">tipe_soal</td><td class="border border-blue-200 px-2 py-1">pg / bs / essay</td><td class="border border-blue-200 px-2 py-1">pg</td></tr>
                        <tr><td class="border border-blue-200 px-2 py-1 font-mono">pertanyaan</td><td class="border border-blue-200 px-2 py-1">Teks soal lengkap</td><td class="border border-blue-200 px-2 py-1">Pancasila terdiri dari...</td></tr>
                        <tr><td class="border border-blue-200 px-2 py-1 font-mono">tingkat_kesulitan</td><td class="border border-blue-200 px-2 py-1">mudah / sedang / sulit</td><td class="border border-blue-200 px-2 py-1">mudah</td></tr>
                        <tr><td class="border border-blue-200 px-2 py-1 font-mono">bobot_nilai</td><td class="border border-blue-200 px-2 py-1">Angka 1-100</td><td class="border border-blue-200 px-2 py-1">1</td></tr>
                        <tr><td class="border border-blue-200 px-2 py-1 font-mono">pilihan_a s/d e</td><td class="border border-blue-200 px-2 py-1">Teks pilihan (PG saja)</td><td class="border border-blue-200 px-2 py-1">5 Sila</td></tr>
                        <tr><td class="border border-blue-200 px-2 py-1 font-mono">jawaban_benar</td><td class="border border-blue-200 px-2 py-1">PG: A/B/C/D/E — BS: Benar/Salah</td><td class="border border-blue-200 px-2 py-1">C</td></tr>
                        <tr><td class="border border-blue-200 px-2 py-1 font-mono">kunci_essay</td><td class="border border-blue-200 px-2 py-1">Pedoman penilaian (Essay, opsional)</td><td class="border border-blue-200 px-2 py-1">-</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="{{ route('guru.bank-soal.template-excel') }}"
                   class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700 font-medium">
                    ⬇️ Download Template Excel
                </a>
                <span class="text-xs text-blue-500 ml-2">(sudah berisi contoh soal PG, BS, dan Essay)</span>
            </div>
        </div>

        {{-- Form Upload --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Upload File Excel</h2>

            <form method="POST"
                  action="{{ route('guru.bank-soal.proses-import') }}"
                  enctype="multipart/form-data">
                @csrf

                {{-- Pilih Mata Pelajaran --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mata Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <select name="mata_pelajaran_id"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mapelGuru as $mapel)
                            <option value="{{ $mapel->id }}" {{ old('mata_pelajaran_id') == $mapel->id ? 'selected' : '' }}>
                                {{ $mapel->nama_mapel }} ({{ $mapel->kode_mapel }})
                            </option>
                        @endforeach
                    </select>
                    @error('mata_pelajaran_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload File --}}
                <div class="mb-6"
                     x-data="{ namaFile: '' }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        File Excel <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-400 transition"
                         :class="namaFile ? 'border-primary-400 bg-primary-50' : ''">
                        <input type="file"
                               name="file_excel"
                               accept=".xlsx,.xls"
                               required
                               class="hidden"
                               id="file_input"
                               x-on:change="namaFile = $event.target.files[0]?.name || ''">
                        <label for="file_input" class="cursor-pointer">
                            <div x-show="!namaFile">
                                <p class="text-gray-500 text-sm">Klik untuk pilih file Excel</p>
                                <p class="text-gray-400 text-xs mt-1">Format: .xlsx atau .xls — Maks 5MB</p>
                            </div>
                            <div x-show="namaFile" class="text-primary-600">
                                <p class="font-medium text-sm">📄 <span x-text="namaFile"></span></p>
                                <p class="text-xs mt-1 text-gray-500">Klik untuk ganti file</p>
                            </div>
                        </label>
                    </div>
                    @error('file_excel')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition">
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
</body>
</html>
```

---

### 🔄 TUGAS 6 — Update View Index Bank Soal

Edit `resources/views/guru/bank_soal/index.blade.php` — tambahkan tombol import di sebelah tombol "Tambah Soal Baru":

```html
{{-- Tambahkan tombol ini di sebelah tombol "+ Tambah Soal Baru" --}}
<a href="{{ route('guru.bank-soal.form-import') }}"
   class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
    ⬆️ Import dari Excel
</a>
```

---

### ✅ TUGAS 7 — Verifikasi

```bash
php artisan route:clear
php artisan cache:clear
php artisan route:list | grep bank-soal
npm run build
php artisan serve
```

Test alur lengkap:
1. Login sebagai guru
2. Akses `http://localhost:8000/guru/bank-soal` → pastikan ada tombol "Import dari Excel"
3. Klik tombol → muncul halaman import dengan panduan format
4. Klik "Download Template Excel" → file terdownload dengan 3 baris contoh
5. Buka template → tambahkan beberapa soal → save
6. Upload file → klik "Mulai Import"
7. Cek halaman bank soal → soal baru harus muncul di daftar

---

## ✅ Kriteria Selesai:
- [ ] `SoalImport` class dibuat dan bisa proses PG, BS, Essay
- [ ] `TemplateSoalExport` generate file Excel dengan contoh 3 tipe soal
- [ ] 3 method baru di `BankSoalController`: `formImport`, `prosesImport`, `downloadTemplate`
- [ ] Routes import ditulis SEBELUM `Route::resource`
- [ ] View import tampil dengan panduan format tabel
- [ ] Download template Excel berhasil
- [ ] Upload dan import file Excel berhasil
- [ ] Error per baris ditampilkan jika ada data yang gagal
- [ ] Tombol "Import dari Excel" muncul di halaman index bank soal

---

## ⚠️ Troubleshooting Umum:

**Error: Route [guru.bank-soal.form-import] not defined**
→ Pastikan route import ditulis SEBELUM `Route::resource('bank-soal', ...)`
→ Jalankan: `php artisan route:clear`

**Error: Class SoalImport not found**
→ Jalankan: `composer dump-autoload`

**Soal ter-import tapi tidak ada pilihan jawaban**
→ Cek format kolom `jawaban_benar` di Excel — pastikan huruf besar (A, B, C) atau (Benar/Salah)

**File template tidak terdownload**
→ Pastikan `maatwebsite/excel` sudah terinstall: `composer require maatwebsite/excel`

---

*Prompt ini adalah TAMBAHAN untuk Step 4 — Bank Soal*
*Setelah ini, lanjutkan ke PROMPT_STEP_5.md — Manajemen Ujian*