# 📝 PROMPT STEP 4 — Bank Soal

> Salin seluruh prompt di bawah ini dan paste ke GitHub Copilot Chat (Agent Mode) di VS Code.
> Pastikan Step 3 sudah selesai dan kamu berada di dalam folder `ujian-mts/` sebelum menjalankan prompt ini.

---

## ▶️ PROMPT STEP 4 (Copy dari garis bawah ini):

---

Kamu adalah AI developer assistant untuk project **ujian-mts**.
Sebelum mulai, baca file `SKILL.md` di root project untuk memahami konteks, konvensi, dan aturan yang berlaku.

Kita sekarang mengerjakan **Step 4: Bank Soal**.

---

### 📋 KONTEKS PENTING SEBELUM EKSEKUSI:

Tipe soal yang didukung:
- **PG** (Pilihan Ganda) → punya 4-5 pilihan jawaban (A/B/C/D/E), hanya 1 yang benar
- **BS** (Benar/Salah) → hanya 2 pilihan: Benar atau Salah
- **Essay** → jawaban teks bebas, dikoreksi manual oleh guru

Aturan akses Bank Soal:
- **Guru** → bisa CRUD soal MILIK SENDIRI saja (tidak bisa lihat soal guru lain)
- **Super Admin** → bisa lihat semua soal dari semua guru

Fitur tambahan:
- Soal bisa dilengkapi **gambar** (upload file gambar)
- Soal punya **tingkat kesulitan**: Mudah, Sedang, Sulit
- Soal punya **bobot nilai** default yang bisa diubah saat membuat ujian

---

### 🗄️ TUGAS 1 — Buat Migration Bank Soal

```bash
php artisan make:migration create_bank_soal_table
php artisan make:migration create_pilihan_jawaban_table
```

**Isi `create_bank_soal_table`:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_soal', function (Blueprint $table) {
            $table->id();

            // Guru yang membuat soal ini
            $table->foreignId('guru_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Mata pelajaran soal ini
            $table->foreignId('mata_pelajaran_id')
                  ->constrained('mata_pelajaran')
                  ->onDelete('cascade');

            // Teks pertanyaan (bisa panjang, pakai text)
            $table->text('pertanyaan');

            // Tipe soal: pg = pilihan ganda, bs = benar/salah, essay
            $table->enum('tipe_soal', ['pg', 'bs', 'essay']);

            // Path gambar jika soal pakai gambar (opsional)
            $table->string('gambar')->nullable();

            // Tingkat kesulitan soal
            $table->enum('tingkat_kesulitan', ['mudah', 'sedang', 'sulit'])->default('sedang');

            // Bobot nilai default soal ini
            $table->unsignedTinyInteger('bobot_nilai')->default(1);

            // Khusus Essay: kunci jawaban/pedoman penilaian (opsional)
            $table->text('kunci_essay')->nullable();

            // Status soal: aktif bisa dipakai di ujian, nonaktif disembunyikan
            $table->boolean('is_aktif')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_soal');
    }
};
```

**Isi `create_pilihan_jawaban_table`:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel ini untuk menyimpan pilihan jawaban soal PG dan BS
        // Essay tidak punya pilihan jawaban di tabel ini
        Schema::create('pilihan_jawaban', function (Blueprint $table) {
            $table->id();

            $table->foreignId('soal_id')
                  ->constrained('bank_soal')
                  ->onDelete('cascade');

            // Label pilihan: A, B, C, D, E (untuk PG) atau Benar, Salah (untuk BS)
            $table->string('label', 10);

            // Teks isi pilihan jawaban
            $table->text('teks_pilihan');

            // Apakah pilihan ini adalah jawaban yang benar?
            $table->boolean('is_benar')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilihan_jawaban');
    }
};
```

Jalankan migration:
```bash
php artisan migrate
```

---

### 🧩 TUGAS 2 — Buat Models

```bash
php artisan make:model BankSoal
php artisan make:model PilihanJawaban
```

**Isi `app/Models/BankSoal.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BankSoal extends Model
{
    protected $table = 'bank_soal';

    protected $fillable = [
        'guru_id',
        'mata_pelajaran_id',
        'pertanyaan',
        'tipe_soal',
        'gambar',
        'tingkat_kesulitan',
        'bobot_nilai',
        'kunci_essay',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    // =============================================
    // RELASI
    // =============================================

    /** Relasi ke guru yang membuat soal */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /** Relasi ke mata pelajaran */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    /** Relasi ke pilihan jawaban */
    public function pilihanJawaban()
    {
        return $this->hasMany(PilihanJawaban::class, 'soal_id')->orderBy('label');
    }

    /** Ambil hanya jawaban yang benar */
    public function jawabanBenar()
    {
        return $this->hasOne(PilihanJawaban::class, 'soal_id')->where('is_benar', true);
    }

    // =============================================
    // SCOPE
    // =============================================

    /** Scope: soal milik guru tertentu */
    public function scopeMilikGuru($query, int $guruId)
    {
        return $query->where('guru_id', $guruId);
    }

    /** Scope: filter berdasarkan mata pelajaran */
    public function scopeMapel($query, int $mapelId)
    {
        return $query->where('mata_pelajaran_id', $mapelId);
    }

    /** Scope: filter berdasarkan tipe soal */
    public function scopeTipe($query, string $tipe)
    {
        return $query->where('tipe_soal', $tipe);
    }

    /** Scope: hanya soal yang aktif */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    // =============================================
    // HELPER
    // =============================================

    /** Ambil label tipe soal dalam Bahasa Indonesia */
    public function getLabelTipeAttribute(): string
    {
        return match($this->tipe_soal) {
            'pg'    => 'Pilihan Ganda',
            'bs'    => 'Benar / Salah',
            'essay' => 'Essay',
            default => '-',
        };
    }

    /** Ambil label tingkat kesulitan */
    public function getLabelKesulitanAttribute(): string
    {
        return match($this->tingkat_kesulitan) {
            'mudah'  => 'Mudah',
            'sedang' => 'Sedang',
            'sulit'  => 'Sulit',
            default  => '-',
        };
    }

    /** Ambil warna badge tingkat kesulitan untuk Tailwind */
    public function getWarnaBadgeKesulitanAttribute(): string
    {
        return match($this->tingkat_kesulitan) {
            'mudah'  => 'bg-green-100 text-green-700',
            'sedang' => 'bg-yellow-100 text-yellow-700',
            'sulit'  => 'bg-red-100 text-red-700',
            default  => 'bg-gray-100 text-gray-700',
        };
    }

    /** Ambil URL gambar soal (jika ada) */
    public function getUrlGambarAttribute(): ?string
    {
        return $this->gambar ? Storage::url($this->gambar) : null;
    }
}
```

**Isi `app/Models/PilihanJawaban.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PilihanJawaban extends Model
{
    protected $table = 'pilihan_jawaban';

    protected $fillable = [
        'soal_id',
        'label',
        'teks_pilihan',
        'is_benar',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
    ];

    /** Relasi balik ke soal */
    public function soal()
    {
        return $this->belongsTo(BankSoal::class, 'soal_id');
    }
}
```

---

### 🛠️ TUGAS 3 — Buat Form Request Validation

```bash
php artisan make:request Guru/StoreSoalRequest
php artisan make:request Guru/UpdateSoalRequest
```

**Isi `app/Http/Requests/Guru/StoreSoalRequest.php`:**
```php
<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class StoreSoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['guru', 'super_admin']);
    }

    public function rules(): array
    {
        $rules = [
            'mata_pelajaran_id'  => 'required|exists:mata_pelajaran,id',
            'pertanyaan'         => 'required|string|min:10',
            'tipe_soal'          => 'required|in:pg,bs,essay',
            'tingkat_kesulitan'  => 'required|in:mudah,sedang,sulit',
            'bobot_nilai'        => 'required|integer|min:1|max:100',
            'gambar'             => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];

        // Validasi khusus berdasarkan tipe soal
        if ($this->tipe_soal === 'pg') {
            // Pilihan ganda wajib ada minimal 2 pilihan, maksimal 5
            $rules['pilihan']           = 'required|array|min:2|max:5';
            $rules['pilihan.*.teks']    = 'required|string|min:1';
            $rules['jawaban_benar']     = 'required|integer|min:0'; // index pilihan yang benar
        }

        if ($this->tipe_soal === 'bs') {
            // Benar/Salah: cukup pilih mana yang benar
            $rules['jawaban_bs'] = 'required|in:benar,salah';
        }

        if ($this->tipe_soal === 'essay') {
            // Essay: kunci jawaban opsional
            $rules['kunci_essay'] = 'nullable|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'mata_pelajaran_id.exists'   => 'Mata pelajaran tidak ditemukan.',
            'pertanyaan.required'        => 'Teks pertanyaan wajib diisi.',
            'pertanyaan.min'             => 'Pertanyaan minimal 10 karakter.',
            'tipe_soal.required'         => 'Tipe soal wajib dipilih.',
            'tipe_soal.in'               => 'Tipe soal tidak valid.',
            'tingkat_kesulitan.required' => 'Tingkat kesulitan wajib dipilih.',
            'bobot_nilai.required'       => 'Bobot nilai wajib diisi.',
            'bobot_nilai.min'            => 'Bobot nilai minimal 1.',
            'bobot_nilai.max'            => 'Bobot nilai maksimal 100.',
            'gambar.image'               => 'File harus berupa gambar.',
            'gambar.max'                 => 'Ukuran gambar maksimal 2MB.',
            'pilihan.required'           => 'Pilihan jawaban wajib diisi untuk soal Pilihan Ganda.',
            'pilihan.min'                => 'Minimal 2 pilihan jawaban harus diisi.',
            'jawaban_benar.required'     => 'Jawaban yang benar wajib dipilih.',
            'jawaban_bs.required'        => 'Pilih apakah pernyataan ini Benar atau Salah.',
        ];
    }
}
```

**Isi `app/Http/Requests/Guru/UpdateSoalRequest.php`:**
Gunakan isi yang sama persis dengan `StoreSoalRequest.php`, hanya ubah nama class menjadi `UpdateSoalRequest`.

---

### 🎛️ TUGAS 4 — Buat Controller Bank Soal

```bash
php artisan make:controller Guru/BankSoalController --resource
php artisan make:controller Admin/BankSoalController
```

**Isi `app/Http/Controllers/Guru/BankSoalController.php`:**
```php
<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreSoalRequest;
use App\Http\Requests\Guru\UpdateSoalRequest;
use App\Models\BankSoal;
use App\Models\MataPelajaran;
use App\Models\PilihanJawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankSoalController extends Controller
{
    /**
     * Daftar soal milik guru yang sedang login.
     * Guru hanya bisa melihat soal miliknya sendiri.
     */
    public function index(Request $request)
    {
        $guruId = auth()->id();

        // Ambil mapel yang diajarkan guru ini untuk filter
        $mapelGuru = auth()->user()->mataPelajaran()->aktif()->get();

        // Query soal milik guru ini dengan filter opsional
        $query = BankSoal::milikGuru($guruId)
                         ->with(['mataPelajaran', 'pilihanJawaban']);

        // Filter berdasarkan mata pelajaran (opsional)
        if ($request->filled('mapel_id')) {
            $query->mapel($request->mapel_id);
        }

        // Filter berdasarkan tipe soal (opsional)
        if ($request->filled('tipe_soal')) {
            $query->tipe($request->tipe_soal);
        }

        // Filter berdasarkan tingkat kesulitan (opsional)
        if ($request->filled('kesulitan')) {
            $query->where('tingkat_kesulitan', $request->kesulitan);
        }

        $soal = $query->latest()->paginate(15)->withQueryString();

        return view('guru.bank_soal.index', compact('soal', 'mapelGuru'));
    }

    /** Tampilkan form tambah soal baru */
    public function create()
    {
        // Guru hanya bisa pilih mapel yang diajarnya
        $mapelGuru = auth()->user()->mataPelajaran()->aktif()->get();

        return view('guru.bank_soal.create', compact('mapelGuru'));
    }

    /**
     * Simpan soal baru ke database.
     * Logic berbeda untuk setiap tipe soal.
     */
    public function store(StoreSoalRequest $request)
    {
        // Upload gambar jika ada
        $pathGambar = null;
        if ($request->hasFile('gambar')) {
            $pathGambar = $request->file('gambar')->store('soal/gambar', 'public');
        }

        // Simpan soal utama
        $soal = BankSoal::create([
            'guru_id'           => auth()->id(),
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'pertanyaan'        => $request->pertanyaan,
            'tipe_soal'         => $request->tipe_soal,
            'gambar'            => $pathGambar,
            'tingkat_kesulitan' => $request->tingkat_kesulitan,
            'bobot_nilai'       => $request->bobot_nilai,
            'kunci_essay'       => $request->kunci_essay,
            'is_aktif'          => true,
        ]);

        // Simpan pilihan jawaban berdasarkan tipe soal
        if ($request->tipe_soal === 'pg') {
            $labelPilihan = ['A', 'B', 'C', 'D', 'E'];
            foreach ($request->pilihan as $index => $pilihan) {
                PilihanJawaban::create([
                    'soal_id'     => $soal->id,
                    'label'       => $labelPilihan[$index],
                    'teks_pilihan'=> $pilihan['teks'],
                    'is_benar'    => ($index == $request->jawaban_benar),
                ]);
            }
        }

        if ($request->tipe_soal === 'bs') {
            // Buat 2 pilihan: Benar dan Salah
            PilihanJawaban::create([
                'soal_id'      => $soal->id,
                'label'        => 'Benar',
                'teks_pilihan' => 'Benar',
                'is_benar'     => ($request->jawaban_bs === 'benar'),
            ]);
            PilihanJawaban::create([
                'soal_id'      => $soal->id,
                'label'        => 'Salah',
                'teks_pilihan' => 'Salah',
                'is_benar'     => ($request->jawaban_bs === 'salah'),
            ]);
        }

        // Essay tidak punya pilihan jawaban

        return redirect()->route('guru.bank-soal.index')
            ->with('success', 'Soal berhasil ditambahkan ke bank soal.');
    }

    /** Tampilkan detail soal */
    public function show(BankSoal $bankSoal)
    {
        // Pastikan guru hanya bisa lihat soal miliknya
        $this->authorizeGuru($bankSoal);

        $bankSoal->load(['mataPelajaran', 'pilihanJawaban', 'guru']);

        return view('guru.bank_soal.show', compact('bankSoal'));
    }

    /** Tampilkan form edit soal */
    public function edit(BankSoal $bankSoal)
    {
        $this->authorizeGuru($bankSoal);

        $mapelGuru = auth()->user()->mataPelajaran()->aktif()->get();
        $bankSoal->load('pilihanJawaban');

        return view('guru.bank_soal.edit', compact('bankSoal', 'mapelGuru'));
    }

    /** Update soal yang sudah ada */
    public function update(UpdateSoalRequest $request, BankSoal $bankSoal)
    {
        $this->authorizeGuru($bankSoal);

        // Handle upload gambar baru
        $pathGambar = $bankSoal->gambar;
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($bankSoal->gambar) {
                Storage::disk('public')->delete($bankSoal->gambar);
            }
            $pathGambar = $request->file('gambar')->store('soal/gambar', 'public');
        }

        // Update soal utama
        $bankSoal->update([
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'pertanyaan'        => $request->pertanyaan,
            'gambar'            => $pathGambar,
            'tingkat_kesulitan' => $request->tingkat_kesulitan,
            'bobot_nilai'       => $request->bobot_nilai,
            'kunci_essay'       => $request->kunci_essay,
        ]);

        // Update pilihan jawaban
        if ($bankSoal->tipe_soal === 'pg') {
            // Hapus pilihan lama, buat yang baru
            $bankSoal->pilihanJawaban()->delete();
            $labelPilihan = ['A', 'B', 'C', 'D', 'E'];
            foreach ($request->pilihan as $index => $pilihan) {
                PilihanJawaban::create([
                    'soal_id'      => $bankSoal->id,
                    'label'        => $labelPilihan[$index],
                    'teks_pilihan' => $pilihan['teks'],
                    'is_benar'     => ($index == $request->jawaban_benar),
                ]);
            }
        }

        if ($bankSoal->tipe_soal === 'bs') {
            $bankSoal->pilihanJawaban()->delete();
            PilihanJawaban::create([
                'soal_id'      => $bankSoal->id,
                'label'        => 'Benar',
                'teks_pilihan' => 'Benar',
                'is_benar'     => ($request->jawaban_bs === 'benar'),
            ]);
            PilihanJawaban::create([
                'soal_id'      => $bankSoal->id,
                'label'        => 'Salah',
                'teks_pilihan' => 'Salah',
                'is_benar'     => ($request->jawaban_bs === 'salah'),
            ]);
        }

        return redirect()->route('guru.bank-soal.index')
            ->with('success', 'Soal berhasil diperbarui.');
    }

    /** Hapus soal */
    public function destroy(BankSoal $bankSoal)
    {
        $this->authorizeGuru($bankSoal);

        // Hapus gambar dari storage jika ada
        if ($bankSoal->gambar) {
            Storage::disk('public')->delete($bankSoal->gambar);
        }

        $bankSoal->delete();

        return redirect()->route('guru.bank-soal.index')
            ->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Helper: pastikan guru hanya bisa akses soal miliknya sendiri.
     * Jika bukan miliknya, tampilkan error 403.
     */
    private function authorizeGuru(BankSoal $bankSoal): void
    {
        if (auth()->user()->role !== 'super_admin' && $bankSoal->guru_id !== auth()->id()) {
            abort(403, 'Kamu tidak memiliki akses ke soal ini.');
        }
    }
}
```

**Isi `app/Http/Controllers/Admin/BankSoalController.php`:**
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Http\Request;

class BankSoalController extends Controller
{
    /**
     * Admin bisa melihat SEMUA soal dari semua guru.
     * Dilengkapi filter per guru dan per mapel.
     */
    public function index(Request $request)
    {
        $query = BankSoal::with(['guru', 'mataPelajaran']);

        if ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }

        if ($request->filled('mapel_id')) {
            $query->where('mata_pelajaran_id', $request->mapel_id);
        }

        if ($request->filled('tipe_soal')) {
            $query->where('tipe_soal', $request->tipe_soal);
        }

        $soal        = $query->latest()->paginate(20);
        $semuaGuru   = User::guru()->aktif()->orderBy('name')->get();
        $semuaMapel  = MataPelajaran::aktif()->orderBy('nama_mapel')->get();

        return view('admin.bank_soal.index', compact('soal', 'semuaGuru', 'semuaMapel'));
    }
}
```

---

### 🗺️ TUGAS 5 — Update Routes

Tambahkan routes berikut ke `routes/web.php`:

```php
// ===== TAMBAHKAN DI DALAM ROUTE GROUP ADMIN =====
// Bank Soal (Admin: read only, lihat semua soal)
Route::get('bank-soal', [\App\Http\Controllers\Admin\BankSoalController::class, 'index'])->name('bank-soal.index');

// ===== TAMBAHKAN DI DALAM ROUTE GROUP GURU =====
// Bank Soal (Guru: CRUD soal milik sendiri)
Route::resource('bank-soal', \App\Http\Controllers\Guru\BankSoalController::class);
```

---

### 🎨 TUGAS 6 — Buat Views Bank Soal

Buat folder-folder view:
```bash
mkdir -p resources/views/guru/bank_soal
mkdir -p resources/views/admin/bank_soal
```

Buatkan view lengkap dengan Tailwind CSS tema hijau (#16a34a), Alpine.js untuk interaktivitas, dan Bahasa Indonesia untuk semua halaman berikut:

---

**`guru/bank_soal/index.blade.php`:**
Halaman daftar soal milik guru yang login. Fitur:
- Tabel dengan kolom: No, Pertanyaan (truncate 80 karakter), Mapel, Tipe Soal (badge warna berbeda per tipe), Kesulitan (badge warna), Bobot, Status, Aksi
- Filter soal: dropdown Mata Pelajaran, dropdown Tipe Soal, dropdown Tingkat Kesulitan
- Tombol "Tambah Soal Baru" di kanan atas
- Counter total soal di atas tabel
- Pagination
- Badge tipe soal: PG = biru, BS = ungu, Essay = oranye
- Badge kesulitan: Mudah = hijau, Sedang = kuning, Sulit = merah
- Flash message sukses/error

---

**`guru/bank_soal/create.blade.php`:**
Form tambah soal dengan fitur dinamis menggunakan Alpine.js:

```html
<!-- Contoh struktur Alpine.js untuk form dinamis -->
<div x-data="{
    tipeSoal: '',
    pilihan: [
        { teks: '' },
        { teks: '' },
        { teks: '' },
        { teks: '' }
    ],
    jawabanBenar: 0,
    jawaban_bs: 'benar',
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
```

Field form:
1. Pilih Mata Pelajaran (dropdown — hanya mapel yang diajar guru ini)
2. Tipe Soal (radio button: Pilihan Ganda | Benar/Salah | Essay) → **mengubah tampilan form secara dinamis dengan Alpine.js**
3. Pertanyaan (textarea dengan label "Teks Pertanyaan")
4. Upload Gambar (opsional, preview sebelum upload)
5. Tingkat Kesulitan (radio: Mudah | Sedang | Sulit)
6. Bobot Nilai (input number, default: 1)

**Bagian dinamis berdasarkan tipe soal (Alpine.js x-show):**
- **Jika PG**: tampilkan input pilihan A, B, C, D (dengan tombol tambah pilihan E dan hapus pilihan), radio button pilih jawaban yang benar
- **Jika BS**: tampilkan radio button pilih Benar atau Salah
- **Jika Essay**: tampilkan textarea Kunci Jawaban/Pedoman Penilaian (opsional)

Tombol: Simpan Soal | Batal

---

**`guru/bank_soal/edit.blade.php`:**
Form edit soal — sama seperti create tapi field sudah terisi data lama. Tipe soal TIDAK bisa diubah setelah disimpan (tampilkan sebagai teks, bukan dropdown).

---

**`guru/bank_soal/show.blade.php`:**
Halaman detail soal lengkap:
- Tampilkan pertanyaan lengkap
- Tampilkan gambar jika ada
- Tampilkan semua pilihan jawaban (untuk PG dan BS) dengan highlight jawaban yang benar (warna hijau)
- Untuk Essay: tampilkan kunci jawaban/pedoman jika ada
- Tombol Edit dan Hapus (dengan konfirmasi Alpine.js)
- Badge tipe soal, kesulitan, bobot nilai

---

**`admin/bank_soal/index.blade.php`:**
Halaman admin melihat semua soal dari semua guru:
- Filter: dropdown Guru, dropdown Mapel, dropdown Tipe Soal
- Tabel dengan kolom: No, Pertanyaan, Mapel, Guru (nama), Tipe, Kesulitan, Bobot
- Badge dan warna sama seperti view guru
- Tidak ada tombol Edit/Hapus (admin hanya monitor)
- Statistik di atas: total soal PG, total BS, total Essay

---

### 🔧 TUGAS 7 — Konfigurasi Storage untuk Upload Gambar

Jalankan perintah berikut untuk membuat symbolic link storage:
```bash
php artisan storage:link
```

Pastikan di `config/filesystems.php`, disk `public` sudah terkonfigurasi dengan benar (sudah default di Laravel).

---

### 🔄 TUGAS 8 — Update Dashboard Guru

Edit `resources/views/guru/dashboard.blade.php` untuk menambahkan:
- Statistik: total soal PG, BS, Essay yang dimiliki guru ini
- Link/tombol cepat ke "Tambah Soal Baru"
- Link ke "Bank Soal Saya"

---

### ✅ TUGAS 9 — Update SKILL.md dan Verifikasi

Jalankan:
```bash
php artisan route:clear
php artisan cache:clear
php artisan route:list | grep bank-soal
php artisan serve
```

Test semua URL berikut:
- `http://localhost:8000/guru/bank-soal` → daftar soal guru (login sebagai guru dulu)
- `http://localhost:8000/guru/bank-soal/create` → form tambah soal
- Coba tambah soal tipe PG, BS, dan Essay masing-masing 1 soal
- `http://localhost:8000/admin/bank-soal` → admin lihat semua soal

Ubah status Step 4 di `SKILL.md` dari `⬜ Belum` menjadi `✅ Selesai`.

---

## ✅ Kriteria Step 4 Dinyatakan Selesai:
- [ ] Migration `bank_soal` dan `pilihan_jawaban` berhasil dijalankan
- [ ] Model `BankSoal` dan `PilihanJawaban` dibuat dengan relasi lengkap
- [ ] Form Request validasi dibuat untuk Store dan Update
- [ ] Controller Guru: CRUD dengan otorisasi (hanya bisa akses soal sendiri)
- [ ] Controller Admin: index dengan filter semua soal semua guru
- [ ] Routes bank-soal terdaftar untuk guru dan admin
- [ ] View index guru: tabel soal + filter + badge tipe & kesulitan
- [ ] View create: form dinamis Alpine.js berubah sesuai tipe soal
- [ ] View edit: field terisi data lama, tipe soal tidak bisa diubah
- [ ] View show: detail soal + highlight jawaban benar
- [ ] Upload gambar berfungsi (`php artisan storage:link` sudah dijalankan)
- [ ] Bisa tambah soal PG, BS, dan Essay tanpa error
- [ ] Admin bisa melihat semua soal di `/admin/bank-soal`
- [ ] SKILL.md Step 4 diupdate ke ✅ Selesai

---

## ⚠️ Troubleshooting Umum:

**Error: Form Alpine.js tidak reaktif**
→ Pastikan `npm run build` sudah dijalankan setelah ada perubahan view
→ Pastikan Alpine.js sudah include di layout atau langsung di view

**Error: Gambar tidak tampil setelah upload**
→ Pastikan sudah jalankan: `php artisan storage:link`
→ Cek folder `storage/app/public/soal/gambar/` sudah ada

**Error: Class BankSoal not found**
→ Jalankan: `composer dump-autoload`

**Error: SQLSTATE foreign key constraint pada bank_soal**
→ Pastikan migration `bank_soal` dijalankan SEBELUM `pilihan_jawaban`

**Guru bisa akses soal guru lain (403 tidak muncul)**
→ Pastikan method `authorizeGuru()` dipanggil di setiap method Controller

---

*Prompt ini adalah bagian dari seri Step-by-Step project ujian-mts*
*Setelah Step 4 selesai, lanjut ke PROMPT_STEP_5.md — Manajemen Ujian*