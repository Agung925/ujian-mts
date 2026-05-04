# 📅 PROMPT STEP 5 — Manajemen Ujian

> Salin seluruh prompt di bawah ini dan paste ke GitHub Copilot Chat (Agent Mode) di VS Code.
> Pastikan Step 4 sudah selesai dan kamu berada di dalam folder `ujian-mts/` sebelum menjalankan prompt ini.

---

## ▶️ PROMPT STEP 5 (Copy dari garis bawah ini):

---

Kamu adalah AI developer assistant untuk project **ujian-mts**.
Sebelum mulai, baca file `SKILL.md` di root project untuk memahami konteks, konvensi, dan aturan yang berlaku.

Kita sekarang mengerjakan **Step 5: Manajemen Ujian**.

---

### 📋 KONTEKS PENTING SEBELUM EKSEKUSI:

Aturan bisnis Manajemen Ujian:
- **Guru** bisa membuat ujian dari bank soal miliknya sendiri
- Soal bisa dipilih **manual** (satu per satu) ATAU **random otomatis** (sistem acak dari bank soal)
- Token akses ujian format **kombinasi huruf+angka** contoh: `MTK-A3F2`, `IPA-9X12`
- Ujian dibuka dan ditutup **secara manual oleh guru** (bukan otomatis jadwal)
- Status ujian: `draft` → `aktif` → `selesai`
- Siswa hanya bisa masuk ujian jika status = `aktif` dan memasukkan token yang benar
- Fitur **acak soal** (urutan soal diacak per siswa) dan **acak jawaban** (urutan pilihan diacak)

---

### 🗄️ TUGAS 1 — Buat Migration Ujian

```bash
php artisan make:migration create_ujian_table
php artisan make:migration create_ujian_soal_table
php artisan make:migration create_sesi_ujian_table
php artisan make:migration create_jawaban_siswa_table
```

**Isi `create_ujian_table`:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian', function (Blueprint $table) {
            $table->id();

            // Judul ujian, contoh: "Ulangan Harian Bab 1 - PPKn"
            $table->string('judul', 200);

            // Relasi ke mata pelajaran
            $table->foreignId('mata_pelajaran_id')
                  ->constrained('mata_pelajaran')
                  ->onDelete('cascade');

            // Relasi ke kelas yang mengikuti ujian ini
            $table->foreignId('kelas_id')
                  ->constrained('kelas')
                  ->onDelete('cascade');

            // Relasi ke guru yang membuat ujian
            $table->foreignId('guru_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Durasi ujian dalam menit
            $table->unsignedSmallInteger('durasi_menit')->default(60);

            // Token akses ujian — format huruf+angka, contoh: MTK-A3F2
            $table->string('token', 20)->unique();

            // Status ujian: draft (belum dibuka), aktif (sedang berlangsung), selesai
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');

            // Apakah urutan soal diacak per siswa?
            $table->boolean('acak_soal')->default(true);

            // Apakah urutan pilihan jawaban diacak per siswa?
            $table->boolean('acak_jawaban')->default(true);

            // Jumlah soal yang diambil (untuk mode random)
            // Null = ambil semua soal yang sudah dipilih
            $table->unsignedSmallInteger('jumlah_soal')->nullable();

            // Catatan/deskripsi ujian (opsional)
            $table->text('deskripsi')->nullable();

            // Kapan guru membuka ujian
            $table->timestamp('dibuka_pada')->nullable();

            // Kapan guru menutup ujian
            $table->timestamp('ditutup_pada')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian');
    }
};
```

**Isi `create_ujian_soal_table`:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel relasi antara ujian dan soal-soal yang dipilih
        Schema::create('ujian_soal', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ujian_id')
                  ->constrained('ujian')
                  ->onDelete('cascade');

            $table->foreignId('soal_id')
                  ->constrained('bank_soal')
                  ->onDelete('cascade');

            // Nomor urut soal dalam ujian ini
            $table->unsignedSmallInteger('nomor_urut')->default(1);

            // Bobot nilai soal dalam ujian ini (bisa berbeda dari bobot default soal)
            $table->unsignedTinyInteger('bobot_nilai')->default(1);

            $table->timestamps();

            // Satu soal tidak boleh muncul dua kali dalam ujian yang sama
            $table->unique(['ujian_id', 'soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_soal');
    }
};
```

**Isi `create_sesi_ujian_table`:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sesi ujian = satu siswa mengerjakan satu ujian
        Schema::create('sesi_ujian', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ujian_id')
                  ->constrained('ujian')
                  ->onDelete('cascade');

            $table->foreignId('siswa_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Waktu siswa mulai mengerjakan
            $table->timestamp('waktu_mulai')->nullable();

            // Waktu siswa selesai (submit atau auto-submit)
            $table->timestamp('waktu_selesai')->nullable();

            // Status pengerjaan siswa
            $table->enum('status', ['belum_mulai', 'sedang', 'selesai'])->default('belum_mulai');

            // Nilai akhir siswa (dihitung setelah submit)
            $table->decimal('nilai_akhir', 5, 2)->nullable();

            // Urutan soal yang diacak khusus untuk siswa ini (disimpan sebagai JSON)
            // Contoh: [3, 1, 5, 2, 4] = urutan nomor soal untuk siswa ini
            $table->json('urutan_soal')->nullable();

            $table->timestamps();

            // Satu siswa hanya boleh punya satu sesi per ujian
            $table->unique(['ujian_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_ujian');
    }
};
```

**Isi `create_jawaban_siswa_table`:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_siswa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sesi_id')
                  ->constrained('sesi_ujian')
                  ->onDelete('cascade');

            $table->foreignId('soal_id')
                  ->constrained('bank_soal')
                  ->onDelete('cascade');

            // Jawaban untuk soal PG dan BS: ID dari pilihan_jawaban yang dipilih
            $table->foreignId('pilihan_id')
                  ->nullable()
                  ->constrained('pilihan_jawaban')
                  ->onDelete('set null');

            // Jawaban untuk soal Essay: teks bebas
            $table->text('jawaban_essay')->nullable();

            // Apakah jawaban ini benar? (null = belum dikoreksi/essay)
            $table->boolean('is_benar')->nullable();

            // Nilai yang didapat untuk soal ini
            $table->decimal('nilai', 5, 2)->default(0);

            $table->timestamps();

            // Satu siswa hanya boleh punya satu jawaban per soal dalam satu sesi
            $table->unique(['sesi_id', 'soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_siswa');
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
php artisan make:model Ujian
php artisan make:model UjianSoal
php artisan make:model SesiUjian
php artisan make:model JawabanSiswa
```

**Isi `app/Models/Ujian.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ujian extends Model
{
    protected $table = 'ujian';

    protected $fillable = [
        'judul', 'mata_pelajaran_id', 'kelas_id', 'guru_id',
        'durasi_menit', 'token', 'status', 'acak_soal',
        'acak_jawaban', 'jumlah_soal', 'deskripsi',
        'dibuka_pada', 'ditutup_pada',
    ];

    protected $casts = [
        'acak_soal'    => 'boolean',
        'acak_jawaban' => 'boolean',
        'dibuka_pada'  => 'datetime',
        'ditutup_pada' => 'datetime',
    ];

    // =============================================
    // RELASI
    // =============================================

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function soal()
    {
        return $this->belongsToMany(BankSoal::class, 'ujian_soal', 'ujian_id', 'soal_id')
                    ->withPivot('nomor_urut', 'bobot_nilai')
                    ->orderBy('ujian_soal.nomor_urut');
    }

    public function sesiUjian()
    {
        return $this->hasMany(SesiUjian::class);
    }

    // =============================================
    // SCOPE
    // =============================================

    public function scopeMilikGuru($query, int $guruId)
    {
        return $query->where('guru_id', $guruId);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // =============================================
    // HELPER
    // =============================================

    /** Cek apakah ujian sedang aktif/dibuka */
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /** Cek apakah ujian sudah selesai */
    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    /** Cek apakah ujian masih draft */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /** Hitung total nilai maksimal ujian ini */
    public function getTotalNilaiMaksAttribute(): int
    {
        return $this->soal->sum('pivot.bobot_nilai');
    }

    /** Warna badge status untuk Tailwind */
    public function getWarnaBadgeStatusAttribute(): string
    {
        return match($this->status) {
            'draft'   => 'bg-gray-100 text-gray-600',
            'aktif'   => 'bg-green-100 text-green-700',
            'selesai' => 'bg-blue-100 text-blue-700',
            default   => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Generate token unik format huruf+angka
     * Contoh: MTK-A3F2, IPA-9X12
     */
    public static function generateToken(string $kodeMapel = 'CBT'): string
    {
        do {
            // Ambil 3 karakter kode mapel (huruf besar)
            $prefix = strtoupper(substr(preg_replace('/[^A-Z]/', '', $kodeMapel), 0, 3));
            if (empty($prefix)) $prefix = 'CBT';

            // Generate 4 karakter random huruf+angka
            $suffix = strtoupper(Str::random(4));

            $token = "{$prefix}-{$suffix}";

        } while (self::where('token', $token)->exists()); // pastikan unik

        return $token;
    }
}
```

**Isi `app/Models/UjianSoal.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianSoal extends Model
{
    protected $table = 'ujian_soal';

    protected $fillable = ['ujian_id', 'soal_id', 'nomor_urut', 'bobot_nilai'];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function soal()
    {
        return $this->belongsTo(BankSoal::class, 'soal_id');
    }
}
```

**Isi `app/Models/SesiUjian.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiUjian extends Model
{
    protected $table = 'sesi_ujian';

    protected $fillable = [
        'ujian_id', 'siswa_id', 'waktu_mulai', 'waktu_selesai',
        'status', 'nilai_akhir', 'urutan_soal',
    ];

    protected $casts = [
        'waktu_mulai'  => 'datetime',
        'waktu_selesai'=> 'datetime',
        'urutan_soal'  => 'array', // otomatis parse JSON ke array PHP
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(JawabanSiswa::class, 'sesi_id');
    }

    /** Hitung sisa waktu ujian dalam detik */
    public function getSisaWaktuDetikAttribute(): int
    {
        if (!$this->waktu_mulai) return 0;

        $batasWaktu = $this->waktu_mulai->addMinutes($this->ujian->durasi_menit);
        $sisaDetik  = now()->diffInSeconds($batasWaktu, false);

        return max(0, $sisaDetik);
    }

    /** Cek apakah waktu ujian sudah habis */
    public function isWaktuHabis(): bool
    {
        return $this->getSisaWaktuDetikAttribute() <= 0;
    }
}
```

**Isi `app/Models/JawabanSiswa.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanSiswa extends Model
{
    protected $table = 'jawaban_siswa';

    protected $fillable = [
        'sesi_id', 'soal_id', 'pilihan_id',
        'jawaban_essay', 'is_benar', 'nilai',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
    ];

    public function sesi()
    {
        return $this->belongsTo(SesiUjian::class, 'sesi_id');
    }

    public function soal()
    {
        return $this->belongsTo(BankSoal::class, 'soal_id');
    }

    public function pilihan()
    {
        return $this->belongsTo(PilihanJawaban::class, 'pilihan_id');
    }
}
```

---

### 🎛️ TUGAS 3 — Buat Service UjianService

Pisahkan logika bisnis ujian ke Service class agar Controller tetap bersih:

```bash
# Buat file secara manual
touch app/Services/UjianService.php
```

**Isi `app/Services/UjianService.php`:**
```php
<?php

namespace App\Services;

use App\Models\BankSoal;
use App\Models\Ujian;
use App\Models\UjianSoal;
use App\Models\SesiUjian;
use App\Models\JawabanSiswa;
use Illuminate\Support\Facades\DB;

class UjianService
{
    /**
     * Tambahkan soal ke ujian secara manual (guru pilih satu per satu)
     */
    public function tambahSoalManual(Ujian $ujian, array $soalIds): void
    {
        $nomorUrut = $ujian->soal()->count() + 1;

        foreach ($soalIds as $soalId) {
            $soal = BankSoal::find($soalId);
            if (!$soal) continue;

            UjianSoal::updateOrCreate(
                ['ujian_id' => $ujian->id, 'soal_id' => $soalId],
                ['nomor_urut' => $nomorUrut++, 'bobot_nilai' => $soal->bobot_nilai]
            );
        }
    }

    /**
     * Tambahkan soal ke ujian secara random dari bank soal guru
     * Ambil $jumlah soal secara acak dari mapel yang sama
     */
    public function tambahSoalRandom(Ujian $ujian, int $jumlah): int
    {
        // Ambil soal random dari bank soal guru dengan mapel yang sama
        $soalRandom = BankSoal::milikGuru($ujian->guru_id)
                               ->mapel($ujian->mata_pelajaran_id)
                               ->aktif()
                               ->inRandomOrder()
                               ->limit($jumlah)
                               ->get();

        if ($soalRandom->isEmpty()) {
            return 0;
        }

        // Hapus soal lama, ganti dengan yang baru
        $ujian->soal()->detach();

        foreach ($soalRandom as $index => $soal) {
            UjianSoal::create([
                'ujian_id'    => $ujian->id,
                'soal_id'     => $soal->id,
                'nomor_urut'  => $index + 1,
                'bobot_nilai' => $soal->bobot_nilai,
            ]);
        }

        return $soalRandom->count();
    }

    /**
     * Buka ujian — ubah status dari draft ke aktif
     * Catat waktu pembukaan
     */
    public function bukaUjian(Ujian $ujian): void
    {
        if ($ujian->soal()->count() === 0) {
            throw new \Exception('Ujian tidak bisa dibuka karena belum ada soal.');
        }

        $ujian->update([
            'status'      => 'aktif',
            'dibuka_pada' => now(),
        ]);
    }

    /**
     * Tutup ujian — ubah status dari aktif ke selesai
     * Auto-submit semua sesi yang masih berlangsung
     */
    public function tutupUjian(Ujian $ujian): void
    {
        // Auto-submit semua siswa yang belum selesai
        $sesiAktif = $ujian->sesiUjian()
                           ->where('status', 'sedang')
                           ->get();

        foreach ($sesiAktif as $sesi) {
            $this->submitJawaban($sesi);
        }

        $ujian->update([
            'status'       => 'selesai',
            'ditutup_pada' => now(),
        ]);
    }

    /**
     * Hitung dan simpan nilai akhir siswa
     * Dipanggil saat siswa submit atau auto-submit
     */
    public function submitJawaban(SesiUjian $sesi): void
    {
        DB::transaction(function () use ($sesi) {
            $totalNilai   = 0;
            $totalMaksimal = 0;

            $jawabanList = $sesi->jawabanSiswa()->with(['soal', 'pilihan'])->get();

            foreach ($jawabanList as $jawaban) {
                $soal          = $jawaban->soal;
                $bobotSoal     = UjianSoal::where('ujian_id', $sesi->ujian_id)
                                          ->where('soal_id', $soal->id)
                                          ->value('bobot_nilai') ?? $soal->bobot_nilai;
                $totalMaksimal += $bobotSoal;

                if ($soal->tipe_soal !== 'essay') {
                    // Auto-koreksi PG dan BS
                    $isBenar = $jawaban->pilihan?->is_benar ?? false;
                    $nilai   = $isBenar ? $bobotSoal : 0;

                    $jawaban->update([
                        'is_benar' => $isBenar,
                        'nilai'    => $nilai,
                    ]);

                    $totalNilai += $nilai;
                }
                // Essay dikoreksi manual oleh guru di Step 7
            }

            // Hitung nilai akhir dalam skala 0-100
            $nilaiAkhir = $totalMaksimal > 0
                ? round(($totalNilai / $totalMaksimal) * 100, 2)
                : 0;

            $sesi->update([
                'status'        => 'selesai',
                'waktu_selesai' => now(),
                'nilai_akhir'   => $nilaiAkhir,
            ]);
        });
    }

    /**
     * Buat sesi ujian untuk siswa saat masuk ruang ujian dengan token
     * Generate urutan soal yang diacak khusus untuk siswa ini
     */
    public function buatSesiSiswa(Ujian $ujian, int $siswaId): SesiUjian
    {
        // Ambil semua ID soal di ujian ini
        $soalIds = $ujian->soal()->pluck('bank_soal.id')->toArray();

        // Acak urutan soal jika fitur acak soal aktif
        if ($ujian->acak_soal) {
            shuffle($soalIds);
        }

        return SesiUjian::create([
            'ujian_id'    => $ujian->id,
            'siswa_id'    => $siswaId,
            'status'      => 'sedang',
            'waktu_mulai' => now(),
            'urutan_soal' => $soalIds,
        ]);
    }
}
```

---

### 🎛️ TUGAS 4 — Buat Controllers

```bash
php artisan make:controller Guru/UjianController --resource
```

**Isi `app/Http/Controllers/Guru/UjianController.php`:**
```php
<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\Ujian;
use App\Models\UjianSoal;
use App\Services\UjianService;
use Illuminate\Http\Request;

class UjianController extends Controller
{
    public function __construct(private UjianService $ujianService) {}

    /** Daftar semua ujian milik guru yang login */
    public function index()
    {
        $ujian = Ujian::milikGuru(auth()->id())
                      ->with(['mataPelajaran', 'kelas'])
                      ->latest()
                      ->paginate(15);

        return view('guru.ujian.index', compact('ujian'));
    }

    /** Form buat ujian baru */
    public function create()
    {
        // Guru hanya bisa pilih mapel yang diajarnya
        $mapelGuru   = auth()->user()->mataPelajaran()->aktif()->get();
        $tahunAktif  = TahunAjaran::aktif()->first();
        $kelasList   = Kelas::aktif()
                            ->where('tahun_ajaran_id', $tahunAktif?->id)
                            ->orderBy('tingkat')
                            ->get();

        return view('guru.ujian.create', compact('mapelGuru', 'kelasList'));
    }

    /** Simpan ujian baru */
    public function store(Request $request)
    {
        $request->validate([
            'judul'             => 'required|string|max:200',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id'          => 'required|exists:kelas,id',
            'durasi_menit'      => 'required|integer|min:5|max:240',
            'acak_soal'         => 'boolean',
            'acak_jawaban'      => 'boolean',
            'deskripsi'         => 'nullable|string',
        ], [
            'judul.required'             => 'Judul ujian wajib diisi.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'kelas_id.required'          => 'Kelas wajib dipilih.',
            'durasi_menit.min'           => 'Durasi minimal 5 menit.',
            'durasi_menit.max'           => 'Durasi maksimal 240 menit (4 jam).',
        ]);

        // Ambil kode mapel untuk generate token
        $mapel = MataPelajaran::find($request->mata_pelajaran_id);
        $token = Ujian::generateToken($mapel->kode_mapel);

        Ujian::create([
            'judul'             => $request->judul,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'kelas_id'          => $request->kelas_id,
            'guru_id'           => auth()->id(),
            'durasi_menit'      => $request->durasi_menit,
            'token'             => $token,
            'status'            => 'draft',
            'acak_soal'         => $request->boolean('acak_soal', true),
            'acak_jawaban'      => $request->boolean('acak_jawaban', true),
            'deskripsi'         => $request->deskripsi,
        ]);

        return redirect()->route('guru.ujian.index')
            ->with('success', "Ujian berhasil dibuat! Token: {$token}");
    }

    /** Detail ujian + daftar soal yang sudah dipilih */
    public function show(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        $ujian->load(['mataPelajaran', 'kelas', 'soal.pilihanJawaban', 'sesiUjian.siswa']);

        // Soal dari bank soal guru ini yang belum masuk ujian
        $soalTersedia = BankSoal::milikGuru(auth()->id())
                                ->mapel($ujian->mata_pelajaran_id)
                                ->aktif()
                                ->whereNotIn('id', $ujian->soal->pluck('id'))
                                ->get();

        // Total soal tersedia untuk mode random
        $totalSoalTersedia = BankSoal::milikGuru(auth()->id())
                                     ->mapel($ujian->mata_pelajaran_id)
                                     ->aktif()
                                     ->count();

        return view('guru.ujian.show', compact('ujian', 'soalTersedia', 'totalSoalTersedia'));
    }

    /** Form edit ujian (hanya saat masih draft) */
    public function edit(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        if (!$ujian->isDraft()) {
            return redirect()->route('guru.ujian.show', $ujian)
                ->with('error', 'Ujian yang sudah aktif atau selesai tidak bisa diedit.');
        }

        $mapelGuru  = auth()->user()->mataPelajaran()->aktif()->get();
        $tahunAktif = TahunAjaran::aktif()->first();
        $kelasList  = Kelas::aktif()
                           ->where('tahun_ajaran_id', $tahunAktif?->id)
                           ->orderBy('tingkat')
                           ->get();

        return view('guru.ujian.edit', compact('ujian', 'mapelGuru', 'kelasList'));
    }

    /** Update data ujian */
    public function update(Request $request, Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        if (!$ujian->isDraft()) {
            return redirect()->route('guru.ujian.show', $ujian)
                ->with('error', 'Ujian tidak bisa diedit karena sudah aktif atau selesai.');
        }

        $request->validate([
            'judul'        => 'required|string|max:200',
            'durasi_menit' => 'required|integer|min:5|max:240',
            'deskripsi'    => 'nullable|string',
        ]);

        $ujian->update($request->only(['judul', 'durasi_menit', 'deskripsi', 'acak_soal', 'acak_jawaban']));

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', 'Ujian berhasil diperbarui.');
    }

    /** Hapus ujian (hanya saat masih draft) */
    public function destroy(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        if (!$ujian->isDraft()) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Ujian yang sudah aktif tidak bisa dihapus.');
        }

        $ujian->delete();

        return redirect()->route('guru.ujian.index')
            ->with('success', 'Ujian berhasil dihapus.');
    }

    /** Tambah soal ke ujian secara manual */
    public function tambahSoalManual(Request $request, Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        $request->validate([
            'soal_ids'   => 'required|array|min:1',
            'soal_ids.*' => 'exists:bank_soal,id',
        ]);

        $this->ujianService->tambahSoalManual($ujian, $request->soal_ids);

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', count($request->soal_ids) . ' soal berhasil ditambahkan ke ujian.');
    }

    /** Tambah soal ke ujian secara random */
    public function tambahSoalRandom(Request $request, Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        $request->validate([
            'jumlah_soal' => 'required|integer|min:1|max:100',
        ]);

        $jumlahDitambahkan = $this->ujianService->tambahSoalRandom($ujian, $request->jumlah_soal);

        if ($jumlahDitambahkan === 0) {
            return redirect()->route('guru.ujian.show', $ujian)
                ->with('error', 'Tidak ada soal tersedia di bank soal untuk mapel ini.');
        }

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', "{$jumlahDitambahkan} soal berhasil dipilih secara random.");
    }

    /** Hapus soal dari ujian */
    public function hapusSoal(Ujian $ujian, int $soalId)
    {
        $this->authorizeGuru($ujian);

        if (!$ujian->isDraft()) {
            return redirect()->route('guru.ujian.show', $ujian)
                ->with('error', 'Soal tidak bisa dihapus dari ujian yang sudah aktif.');
        }

        UjianSoal::where('ujian_id', $ujian->id)
                 ->where('soal_id', $soalId)
                 ->delete();

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', 'Soal berhasil dihapus dari ujian.');
    }

    /** Buka ujian — ubah status ke aktif */
    public function buka(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        try {
            $this->ujianService->bukaUjian($ujian);
        } catch (\Exception $e) {
            return redirect()->route('guru.ujian.show', $ujian)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', "Ujian berhasil dibuka! Token: {$ujian->token}");
    }

    /** Tutup ujian — ubah status ke selesai */
    public function tutup(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        $this->ujianService->tutupUjian($ujian);

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', 'Ujian berhasil ditutup. Semua jawaban siswa sudah diproses.');
    }

    /** Helper: pastikan guru hanya akses ujian miliknya */
    private function authorizeGuru(Ujian $ujian): void
    {
        if (auth()->user()->role !== 'super_admin' && $ujian->guru_id !== auth()->id()) {
            abort(403, 'Kamu tidak memiliki akses ke ujian ini.');
        }
    }
}
```

---

### 🗺️ TUGAS 5 — Update Routes

Tambahkan ke `routes/web.php` di dalam route group GURU:

```php
// ===== TAMBAHKAN DI DALAM ROUTE GROUP GURU =====

// Ujian — CRUD + aksi buka/tutup/tambah soal
Route::resource('ujian', \App\Http\Controllers\Guru\UjianController::class);
Route::post('ujian/{ujian}/tambah-soal-manual', [\App\Http\Controllers\Guru\UjianController::class, 'tambahSoalManual'])->name('ujian.tambah-soal-manual');
Route::post('ujian/{ujian}/tambah-soal-random', [\App\Http\Controllers\Guru\UjianController::class, 'tambahSoalRandom'])->name('ujian.tambah-soal-random');
Route::delete('ujian/{ujian}/hapus-soal/{soalId}', [\App\Http\Controllers\Guru\UjianController::class, 'hapusSoal'])->name('ujian.hapus-soal');
Route::post('ujian/{ujian}/buka', [\App\Http\Controllers\Guru\UjianController::class, 'buka'])->name('ujian.buka');
Route::post('ujian/{ujian}/tutup', [\App\Http\Controllers\Guru\UjianController::class, 'tutup'])->name('ujian.tutup');
```

---

### 🎨 TUGAS 6 — Buat Views Ujian

```bash
mkdir -p resources/views/guru/ujian
```

Buatkan view lengkap Tailwind CSS tema hijau, Alpine.js, Bahasa Indonesia:

**`guru/ujian/index.blade.php`** — Daftar ujian guru:
- Tabel: No, Judul Ujian, Mapel, Kelas, Durasi, Token, Status (badge), Jumlah Soal, Aksi
- Badge status: Draft=abu, Aktif=hijau, Selesai=biru
- Tombol "+ Buat Ujian Baru"
- Flash message

**`guru/ujian/create.blade.php`** — Form buat ujian:
- Input: Judul Ujian, Pilih Mapel (dropdown), Pilih Kelas (dropdown), Durasi (menit)
- Toggle: Acak Urutan Soal (default: ON), Acak Urutan Jawaban (default: ON)
- Textarea: Deskripsi (opsional)
- Info: "Token akan digenerate otomatis setelah ujian dibuat"
- Tombol: Buat Ujian | Batal

**`guru/ujian/show.blade.php`** — Detail ujian (halaman utama manajemen ujian):

Bagian atas — Info Ujian:
- Judul, Mapel, Kelas, Durasi, Token (tampilkan besar dengan box khusus), Status badge
- Tombol aksi sesuai status:
  - Draft: tombol "Buka Ujian" (hijau), "Edit", "Hapus"
  - Aktif: tombol "Tutup Ujian" (merah) + konfirmasi Alpine.js
  - Selesai: tidak ada tombol aksi

Bagian tengah — Tambah Soal (hanya tampil jika status Draft):
- **Tab 1: Pilih Manual** → daftar soal tersedia dengan checkbox, tombol "Tambah Soal Terpilih"
- **Tab 2: Ambil Random** → input jumlah soal, info total soal tersedia, tombol "Ambil Soal Random"

Bagian bawah — Daftar Soal dalam Ujian:
- Tabel: No Urut, Pertanyaan (truncate), Tipe (badge), Bobot, Aksi Hapus (hanya saat Draft)
- Total soal dan total bobot nilai di footer tabel

**`guru/ujian/edit.blade.php`** — Form edit ujian (hanya bisa saat Draft):
- Field yang bisa diedit: Judul, Durasi, Acak Soal, Acak Jawaban, Deskripsi
- Mapel dan Kelas tidak bisa diubah (tampilkan sebagai teks read-only)

---

### 🔄 TUGAS 7 — Update Dashboard Guru

Edit `resources/views/guru/dashboard.blade.php`:
- Tambahkan statistik: Total Ujian Draft, Total Ujian Aktif, Total Ujian Selesai
- Tambahkan link cepat: "Buat Ujian Baru", "Lihat Semua Ujian"

---

### ✅ TUGAS 8 — Verifikasi & Update SKILL.md

```bash
php artisan route:clear
php artisan cache:clear
php artisan route:list | grep ujian
php artisan serve
```

Test alur lengkap:
1. Login sebagai guru
2. Akses `http://localhost:8000/guru/ujian` → halaman daftar ujian
3. Klik "Buat Ujian Baru" → isi form → simpan → pastikan token terbuat otomatis
4. Klik detail ujian → tambah soal manual atau random
5. Klik "Buka Ujian" → status berubah ke Aktif, token tampil jelas
6. Klik "Tutup Ujian" → status berubah ke Selesai

Ubah status Step 5 di `SKILL.md` dari `⬜ Belum` menjadi `✅ Selesai`.

---

## ✅ Kriteria Step 5 Dinyatakan Selesai:
- [ ] 4 Migration baru berhasil (ujian, ujian_soal, sesi_ujian, jawaban_siswa)
- [ ] 4 Model baru dengan relasi lengkap
- [ ] UjianService dibuat dengan logic buka/tutup/tambah soal
- [ ] Controller Guru: CRUD ujian + buka/tutup + tambah soal manual/random
- [ ] Routes ujian terdaftar lengkap
- [ ] View index: daftar ujian dengan badge status
- [ ] View create: form ujian dengan auto-generate token
- [ ] View show: detail ujian + tab tambah soal + daftar soal + tombol buka/tutup
- [ ] Buat ujian baru berhasil dengan token otomatis format KODE-XXXX
- [ ] Tambah soal manual dan random berhasil
- [ ] Buka ujian: status berubah ke Aktif
- [ ] Tutup ujian: status berubah ke Selesai
- [ ] SKILL.md Step 5 diupdate ke ✅ Selesai

---

## ⚠️ Troubleshooting Umum:

**Error: Token duplicate**
→ Method `generateToken()` sudah handle ini dengan loop do-while
→ Kalau masih error, jalankan: `php artisan cache:clear`

**Error: UjianService not found**
→ Jalankan: `composer dump-autoload`

**Ujian tidak bisa dibuka: "belum ada soal"**
→ Tambahkan minimal 1 soal ke ujian dulu sebelum klik Buka

**Tab Alpine.js tidak berfungsi**
→ Jalankan: `npm run build`

---

*Prompt ini adalah bagian dari seri Step-by-Step project ujian-mts*
*Setelah Step 5 selesai, lanjut ke PROMPT_STEP_6.md — Ruang Ujian Siswa*