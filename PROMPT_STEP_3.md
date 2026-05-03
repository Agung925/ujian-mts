# 🗂️ PROMPT STEP 3 — Data Master

> Salin seluruh prompt di bawah ini dan paste ke GitHub Copilot Chat (Agent Mode) di VS Code.
> Pastikan Step 2 sudah selesai dan kamu berada di dalam folder `ujian-mts/` sebelum menjalankan prompt ini.

---

## ▶️ PROMPT STEP 3 (Copy dari garis bawah ini):

---

Kamu adalah AI developer assistant untuk project **ujian-mts**.
Sebelum mulai, baca file `SKILL.md` di root project untuk memahami konteks, konvensi, dan aturan yang berlaku.

Kita sekarang mengerjakan **Step 3: Data Master**.

---

### 📋 KONTEKS PENTING SEBELUM EKSEKUSI:

Aturan akses Data Master:
- **Super Admin** → bisa CRUD (Create, Read, Update, Delete) semua data master
- **Guru** → hanya bisa READ (melihat) data master, tidak bisa edit

Mata pelajaran menggunakan **standar Kemenag MTs** yang terdiri dari:
- Mata pelajaran **Umum**: Matematika, Bahasa Indonesia, Bahasa Inggris, IPA, IPS, PJOK, Seni Budaya, Prakarya, Informatika
- Mata pelajaran **Keagamaan Islam**: Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, Bahasa Arab

Tingkat kelas MTs: **VII, VIII, IX**

---

### 🗄️ TUGAS 1 — Buat Migration Data Master

Jalankan perintah berikut untuk membuat semua migration:

```bash
php artisan make:migration create_tahun_ajaran_table
php artisan make:migration create_mata_pelajaran_table
php artisan make:migration create_kelas_table
php artisan make:migration create_guru_mapel_table
php artisan make:migration create_siswa_kelas_table
```

**Isi `create_tahun_ajaran_table`:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            // Contoh format: 2024/2025
            $table->string('nama', 20);
            // Semester 1 (Ganjil) atau 2 (Genap)
            $table->enum('semester', ['1', '2']);
            // Hanya satu tahun ajaran yang aktif dalam satu waktu
            $table->boolean('is_aktif')->default(false);
            $table->timestamps();

            // Kombinasi nama + semester harus unik
            $table->unique(['nama', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};
```

**Isi `create_mata_pelajaran_table`:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            // Nama lengkap mata pelajaran
            $table->string('nama_mapel', 100);
            // Kode singkat, contoh: MTK, BIN, ENG
            $table->string('kode_mapel', 10)->unique();
            // Jenis: umum atau keagamaan
            $table->enum('jenis', ['umum', 'keagamaan']);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran');
    }
};
```

**Isi `create_kelas_table`:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            // Contoh: VII-A, VIII-B, IX-C
            $table->string('nama_kelas', 20);
            // Tingkat kelas: VII, VIII, atau IX
            $table->enum('tingkat', ['VII', 'VIII', 'IX']);
            // Relasi ke tahun ajaran
            $table->foreignId('tahun_ajaran_id')
                  ->constrained('tahun_ajaran')
                  ->onDelete('cascade');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            // Nama kelas + tahun ajaran harus unik
            $table->unique(['nama_kelas', 'tahun_ajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
```

**Isi `create_guru_mapel_table`:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel relasi: satu guru bisa mengajar banyak mapel
        Schema::create('guru_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')
                  ->constrained('mata_pelajaran')
                  ->onDelete('cascade');
            $table->timestamps();

            // Satu guru tidak boleh di-assign ke mapel yang sama dua kali
            $table->unique(['user_id', 'mata_pelajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_mapel');
    }
};
```

**Isi `create_siswa_kelas_table`:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel relasi: satu siswa masuk ke satu kelas per tahun ajaran
        Schema::create('siswa_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('kelas_id')
                  ->constrained('kelas')
                  ->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')
                  ->constrained('tahun_ajaran')
                  ->onDelete('cascade');
            $table->timestamps();

            // Satu siswa hanya boleh ada di satu kelas per tahun ajaran
            $table->unique(['user_id', 'tahun_ajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_kelas');
    }
};
```

Jalankan semua migration:
```bash
php artisan migrate
```

---

### 🧩 TUGAS 2 — Buat Models

```bash
php artisan make:model TahunAjaran
php artisan make:model MataPelajaran
php artisan make:model Kelas
php artisan make:model GuruMapel
php artisan make:model SiswaKelas
```

**Isi `app/Models/TahunAjaran.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';

    protected $fillable = ['nama', 'semester', 'is_aktif'];

    protected $casts = ['is_aktif' => 'boolean'];

    /** Relasi ke kelas */
    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    /** Label lengkap tahun ajaran, contoh: 2024/2025 Semester 1 */
    public function getLabelAttribute(): string
    {
        $semester = $this->semester == '1' ? 'Ganjil' : 'Genap';
        return "{$this->nama} Semester {$semester}";
    }

    /** Scope: ambil tahun ajaran yang sedang aktif */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Pastikan hanya satu tahun ajaran yang aktif saat ini.
     * Nonaktifkan semua yang lain sebelum mengaktifkan yang baru.
     */
    public static function aktifkanSatu(int $id): void
    {
        self::query()->update(['is_aktif' => false]);
        self::find($id)->update(['is_aktif' => true]);
    }
}
```

**Isi `app/Models/MataPelajaran.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';

    protected $fillable = ['nama_mapel', 'kode_mapel', 'jenis', 'is_aktif'];

    protected $casts = ['is_aktif' => 'boolean'];

    /** Relasi ke guru melalui tabel pivot guru_mapel */
    public function guru()
    {
        return $this->belongsToMany(User::class, 'guru_mapel', 'mata_pelajaran_id', 'user_id');
    }

    /** Scope: ambil hanya mapel yang aktif */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    /** Scope: filter berdasarkan jenis (umum/keagamaan) */
    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis', $jenis);
    }
}
```

**Isi `app/Models/Kelas.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = ['nama_kelas', 'tingkat', 'tahun_ajaran_id', 'is_aktif'];

    protected $casts = ['is_aktif' => 'boolean'];

    /** Relasi ke tahun ajaran */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /** Relasi ke siswa melalui tabel pivot siswa_kelas */
    public function siswa()
    {
        return $this->belongsToMany(User::class, 'siswa_kelas', 'kelas_id', 'user_id');
    }

    /** Scope: ambil kelas yang aktif */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
```

**Isi `app/Models/GuruMapel.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruMapel extends Model
{
    protected $table = 'guru_mapel';

    protected $fillable = ['user_id', 'mata_pelajaran_id'];

    public function guru()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}
```

**Isi `app/Models/SiswaKelas.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaKelas extends Model
{
    protected $table = 'siswa_kelas';

    protected $fillable = ['user_id', 'kelas_id', 'tahun_ajaran_id'];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
```

Tambahkan relasi berikut ke **`app/Models/User.php`** (tambahkan di dalam class, setelah method yang sudah ada):

```php
/** Relasi: guru mengajar banyak mata pelajaran */
public function mataPelajaran()
{
    return $this->belongsToMany(MataPelajaran::class, 'guru_mapel', 'user_id', 'mata_pelajaran_id');
}

/** Relasi: siswa terdaftar di kelas */
public function kelas()
{
    return $this->belongsToMany(Kelas::class, 'siswa_kelas', 'user_id', 'kelas_id');
}
```

---

### 🌱 TUGAS 3 — Buat Seeders Data Master

```bash
php artisan make:seeder TahunAjaranSeeder
php artisan make:seeder MataPelajaranSeeder
php artisan make:seeder KelasSeeder
```

**Isi `database/seeders/TahunAjaranSeeder.php`:**
```php
<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => '2024/2025', 'semester' => '1', 'is_aktif' => false],
            ['nama' => '2024/2025', 'semester' => '2', 'is_aktif' => false],
            ['nama' => '2025/2026', 'semester' => '1', 'is_aktif' => true], // ← aktif
            ['nama' => '2025/2026', 'semester' => '2', 'is_aktif' => false],
        ];

        foreach ($data as $item) {
            TahunAjaran::updateOrCreate(
                ['nama' => $item['nama'], 'semester' => $item['semester']],
                $item
            );
        }

        $this->command->info('✅ Tahun Ajaran selesai di-seed!');
    }
}
```

**Isi `database/seeders/MataPelajaranSeeder.php`:**
```php
<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        // ===================================
        // Mata Pelajaran Umum (Standar MTs)
        // ===================================
        $mapelUmum = [
            ['kode_mapel' => 'MTK',  'nama_mapel' => 'Matematika'],
            ['kode_mapel' => 'BIN',  'nama_mapel' => 'Bahasa Indonesia'],
            ['kode_mapel' => 'ENG',  'nama_mapel' => 'Bahasa Inggris'],
            ['kode_mapel' => 'IPA',  'nama_mapel' => 'Ilmu Pengetahuan Alam'],
            ['kode_mapel' => 'IPS',  'nama_mapel' => 'Ilmu Pengetahuan Sosial'],
            ['kode_mapel' => 'PKN',  'nama_mapel' => 'PPKn'],
            ['kode_mapel' => 'PJOK', 'nama_mapel' => 'Pendidikan Jasmani, Olahraga dan Kesehatan'],
            ['kode_mapel' => 'SBD',  'nama_mapel' => 'Seni Budaya'],
            ['kode_mapel' => 'PRK',  'nama_mapel' => 'Prakarya'],
            ['kode_mapel' => 'INF',  'nama_mapel' => 'Informatika'],
        ];

        // ===================================
        // Mata Pelajaran Keagamaan Islam (Kemenag MTs)
        // ===================================
        $mapelKeagamaan = [
            ['kode_mapel' => 'AQH',  'nama_mapel' => 'Al-Qur\'an Hadis'],
            ['kode_mapel' => 'AAK',  'nama_mapel' => 'Akidah Akhlak'],
            ['kode_mapel' => 'FQH',  'nama_mapel' => 'Fikih'],
            ['kode_mapel' => 'SKI',  'nama_mapel' => 'Sejarah Kebudayaan Islam'],
            ['kode_mapel' => 'BAR',  'nama_mapel' => 'Bahasa Arab'],
        ];

        foreach ($mapelUmum as $mapel) {
            MataPelajaran::updateOrCreate(
                ['kode_mapel' => $mapel['kode_mapel']],
                array_merge($mapel, ['jenis' => 'umum', 'is_aktif' => true])
            );
        }

        foreach ($mapelKeagamaan as $mapel) {
            MataPelajaran::updateOrCreate(
                ['kode_mapel' => $mapel['kode_mapel']],
                array_merge($mapel, ['jenis' => 'keagamaan', 'is_aktif' => true])
            );
        }

        $this->command->info('✅ Mata Pelajaran Kemenag MTs selesai di-seed!');
    }
}
```

**Isi `database/seeders/KelasSeeder.php`:**
```php
<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil tahun ajaran yang sedang aktif
        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();

        if (!$tahunAjaran) {
            $this->command->warn('⚠️ Tidak ada tahun ajaran aktif. Jalankan TahunAjaranSeeder dulu!');
            return;
        }

        // Generate kelas VII-A sampai IX-C
        $tingkatan = ['VII', 'VIII', 'IX'];
        $rombel    = ['A', 'B', 'C'];

        foreach ($tingkatan as $tingkat) {
            foreach ($rombel as $huruf) {
                Kelas::updateOrCreate(
                    [
                        'nama_kelas'      => "{$tingkat}-{$huruf}",
                        'tahun_ajaran_id' => $tahunAjaran->id,
                    ],
                    [
                        'tingkat'         => $tingkat,
                        'is_aktif'        => true,
                    ]
                );
            }
        }

        $this->command->info('✅ Kelas MTs (VII-A s/d IX-C) selesai di-seed!');
    }
}
```

Update `database/seeders/DatabaseSeeder.php` untuk memanggil semua seeder:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            TahunAjaranSeeder::class,
            MataPelajaranSeeder::class,
            KelasSeeder::class,
        ]);
    }
}
```

Jalankan seeder:
```bash
php artisan db:seed --class=TahunAjaranSeeder
php artisan db:seed --class=MataPelajaranSeeder
php artisan db:seed --class=KelasSeeder
```

---

### 🎛️ TUGAS 4 — Buat Controllers Data Master

```bash
php artisan make:controller Admin/TahunAjaranController --resource
php artisan make:controller Admin/MataPelajaranController --resource
php artisan make:controller Admin/KelasController --resource
php artisan make:controller Admin/GuruMapelController
php artisan make:controller Admin/SiswaKelasController
php artisan make:controller Guru/DataMasterController
```

**Isi `app/Http/Controllers/Admin/TahunAjaranController.php`:**
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    /** Daftar semua tahun ajaran */
    public function index()
    {
        $tahunAjaran = TahunAjaran::latest()->paginate(10);
        return view('admin.tahun_ajaran.index', compact('tahunAjaran'));
    }

    /** Form tambah tahun ajaran */
    public function create()
    {
        return view('admin.tahun_ajaran.create');
    }

    /** Simpan tahun ajaran baru */
    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:20',
            'semester' => 'required|in:1,2',
        ], [
            'nama.required'     => 'Format tahun ajaran wajib diisi. Contoh: 2025/2026',
            'semester.required' => 'Semester wajib dipilih.',
        ]);

        TahunAjaran::create([
            'nama'     => $request->nama,
            'semester' => $request->semester,
            'is_aktif' => false, // default tidak aktif dulu
        ]);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /** Form edit tahun ajaran */
    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('admin.tahun_ajaran.edit', compact('tahunAjaran'));
    }

    /** Update tahun ajaran */
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'nama'     => 'required|string|max:20',
            'semester' => 'required|in:1,2',
        ]);

        $tahunAjaran->update($request->only(['nama', 'semester']));

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /** Aktifkan satu tahun ajaran (nonaktifkan yang lain) */
    public function aktifkan(TahunAjaran $tahunAjaran)
    {
        TahunAjaran::aktifkanSatu($tahunAjaran->id);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', "Tahun ajaran {$tahunAjaran->label} sekarang aktif.");
    }

    /** Hapus tahun ajaran */
    public function destroy(TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->is_aktif) {
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('error', 'Tidak bisa menghapus tahun ajaran yang sedang aktif.');
        }

        $tahunAjaran->delete();

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
```

**Isi `app/Http/Controllers/Admin/MataPelajaranController.php`:**
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index()
    {
        // Kelompokkan berdasarkan jenis untuk tampilan yang lebih rapi
        $umum       = MataPelajaran::jenis('umum')->orderBy('nama_mapel')->get();
        $keagamaan  = MataPelajaran::jenis('keagamaan')->orderBy('nama_mapel')->get();
        return view('admin.mata_pelajaran.index', compact('umum', 'keagamaan'));
    }

    public function create()
    {
        return view('admin.mata_pelajaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajaran,kode_mapel',
            'jenis'      => 'required|in:umum,keagamaan',
        ], [
            'kode_mapel.unique' => 'Kode mapel sudah digunakan.',
        ]);

        MataPelajaran::create($request->only(['nama_mapel', 'kode_mapel', 'jenis']));

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        return view('admin.mata_pelajaran.edit', compact('mataPelajaran'));
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajaran,kode_mapel,' . $mataPelajaran->id,
            'jenis'      => 'required|in:umum,keagamaan',
            'is_aktif'   => 'required|boolean',
        ]);

        $mataPelajaran->update($request->only(['nama_mapel', 'kode_mapel', 'jenis', 'is_aktif']));

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();
        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
```

**Isi `app/Http/Controllers/Admin/KelasController.php`:**
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\SiswaKelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunAjaran::aktif()->first();
        $kelas      = Kelas::with('tahunAjaran')
                           ->where('tahun_ajaran_id', $tahunAktif?->id)
                           ->orderBy('tingkat')
                           ->orderBy('nama_kelas')
                           ->paginate(15);

        return view('admin.kelas.index', compact('kelas', 'tahunAktif'));
    }

    public function create()
    {
        $tahunAjaran = TahunAjaran::orderBy('nama')->get();
        return view('admin.kelas.create', compact('tahunAjaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas'      => 'required|string|max:20',
            'tingkat'         => 'required|in:VII,VIII,IX',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        Kelas::create($request->only(['nama_kelas', 'tingkat', 'tahun_ajaran_id']));

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas)
    {
        $tahunAjaran = TahunAjaran::orderBy('nama')->get();
        return view('admin.kelas.edit', compact('kelas', 'tahunAjaran'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:20',
            'tingkat'    => 'required|in:VII,VIII,IX',
            'is_aktif'   => 'required|boolean',
        ]);

        $kelas->update($request->only(['nama_kelas', 'tingkat', 'is_aktif']));

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    /** Halaman assign siswa ke kelas */
    public function assignSiswa(Kelas $kelas)
    {
        // Ambil siswa yang belum di-assign ke kelas manapun di tahun ajaran ini
        $siswaBelumAssign = User::siswa()->aktif()
            ->whereDoesntHave('kelas', function ($q) use ($kelas) {
                $q->where('tahun_ajaran_id', $kelas->tahun_ajaran_id);
            })
            ->orderBy('name')
            ->get();

        // Ambil siswa yang sudah ada di kelas ini
        $siswaKelas = $kelas->siswa()->orderBy('name')->get();

        return view('admin.kelas.assign_siswa', compact('kelas', 'siswaBelumAssign', 'siswaKelas'));
    }

    /** Simpan assign siswa ke kelas */
    public function simpanAssignSiswa(Request $request, Kelas $kelas)
    {
        $request->validate([
            'siswa_ids'   => 'required|array',
            'siswa_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->siswa_ids as $siswaId) {
            SiswaKelas::updateOrCreate([
                'user_id'         => $siswaId,
                'tahun_ajaran_id' => $kelas->tahun_ajaran_id,
            ], [
                'kelas_id' => $kelas->id,
            ]);
        }

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Siswa berhasil di-assign ke kelas ' . $kelas->nama_kelas);
    }
}
```

**Isi `app/Http/Controllers/Admin/GuruMapelController.php`:**
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruMapel;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Http\Request;

class GuruMapelController extends Controller
{
    /** Halaman assign mata pelajaran ke guru */
    public function index()
    {
        $guru = User::guru()->aktif()->with('mataPelajaran')->orderBy('name')->get();
        return view('admin.guru_mapel.index', compact('guru'));
    }

    /** Simpan assign mapel ke guru */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'            => 'required|exists:users,id',
            'mata_pelajaran_ids' => 'required|array',
            'mata_pelajaran_ids.*' => 'exists:mata_pelajaran,id',
        ]);

        // Hapus assign lama untuk guru ini, lalu buat yang baru
        GuruMapel::where('user_id', $request->user_id)->delete();

        foreach ($request->mata_pelajaran_ids as $mapelId) {
            GuruMapel::create([
                'user_id'           => $request->user_id,
                'mata_pelajaran_id' => $mapelId,
            ]);
        }

        return redirect()->route('admin.guru-mapel.index')
            ->with('success', 'Mata pelajaran berhasil di-assign ke guru.');
    }
}
```

**Isi `app/Http/Controllers/Guru/DataMasterController.php`:**
```php
<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;

class DataMasterController extends Controller
{
    /** Guru hanya bisa melihat daftar kelas (read only) */
    public function kelas()
    {
        $tahunAktif = TahunAjaran::aktif()->first();
        $kelas      = Kelas::aktif()
                           ->where('tahun_ajaran_id', $tahunAktif?->id)
                           ->orderBy('tingkat')
                           ->get();

        return view('guru.data_master.kelas', compact('kelas', 'tahunAktif'));
    }

    /** Guru hanya bisa melihat daftar mata pelajaran (read only) */
    public function mataPelajaran()
    {
        $umum      = MataPelajaran::jenis('umum')->aktif()->orderBy('nama_mapel')->get();
        $keagamaan = MataPelajaran::jenis('keagamaan')->aktif()->orderBy('nama_mapel')->get();

        return view('guru.data_master.mata_pelajaran', compact('umum', 'keagamaan'));
    }
}
```

---

### 🗺️ TUGAS 5 — Update Routes

Tambahkan routes berikut ke `routes/web.php`:

```php
// ===== TAMBAHKAN DI DALAM ROUTE GROUP ADMIN =====
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.superadmin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Manajemen User (sudah ada dari Step 2)
    Route::get('users/template-excel', [UserController::class, 'downloadTemplate'])->name('users.template-excel');
    Route::post('users/import-siswa', [UserController::class, 'importSiswa'])->name('users.import-siswa');
    Route::resource('users', UserController::class);

    // ===== BARU: Data Master =====
    Route::resource('tahun-ajaran', \App\Http\Controllers\Admin\TahunAjaranController::class);
    Route::post('tahun-ajaran/{tahunAjaran}/aktifkan', [\App\Http\Controllers\Admin\TahunAjaranController::class, 'aktifkan'])->name('tahun-ajaran.aktifkan');

    Route::resource('mata-pelajaran', \App\Http\Controllers\Admin\MataPelajaranController::class);

    Route::get('kelas/{kelas}/assign-siswa', [\App\Http\Controllers\Admin\KelasController::class, 'assignSiswa'])->name('kelas.assign-siswa');
    Route::post('kelas/{kelas}/simpan-assign-siswa', [\App\Http\Controllers\Admin\KelasController::class, 'simpanAssignSiswa'])->name('kelas.simpan-assign-siswa');
    Route::resource('kelas', \App\Http\Controllers\Admin\KelasController::class);

    Route::get('guru-mapel', [\App\Http\Controllers\Admin\GuruMapelController::class, 'index'])->name('guru-mapel.index');
    Route::post('guru-mapel', [\App\Http\Controllers\Admin\GuruMapelController::class, 'store'])->name('guru-mapel.store');
});

// ===== TAMBAHKAN DI DALAM ROUTE GROUP GURU =====
Route::prefix('guru')->name('guru.')->middleware(['auth', 'role.guru'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Guru\DashboardController::class, 'index'])->name('dashboard');

    // ===== BARU: Data Master (read only untuk guru) =====
    Route::get('data-master/kelas', [\App\Http\Controllers\Guru\DataMasterController::class, 'kelas'])->name('data-master.kelas');
    Route::get('data-master/mata-pelajaran', [\App\Http\Controllers\Guru\DataMasterController::class, 'mataPelajaran'])->name('data-master.mata-pelajaran');
});
```

---

### 🎨 TUGAS 6 — Buat Views Data Master

Buat folder-folder view berikut:
```bash
mkdir -p resources/views/admin/tahun_ajaran
mkdir -p resources/views/admin/mata_pelajaran
mkdir -p resources/views/admin/kelas
mkdir -p resources/views/admin/guru_mapel
mkdir -p resources/views/guru/data_master
```

Buatkan view lengkap dengan Tailwind CSS tema hijau (#16a34a) dan Bahasa Indonesia untuk semua halaman berikut:

**Admin Views:**
- `admin/tahun_ajaran/index.blade.php` → tabel daftar tahun ajaran + tombol Aktifkan, Edit, Hapus
- `admin/tahun_ajaran/create.blade.php` → form input nama tahun ajaran + pilih semester
- `admin/tahun_ajaran/edit.blade.php` → form edit
- `admin/mata_pelajaran/index.blade.php` → tabel dikelompokkan: Mapel Umum | Mapel Keagamaan
- `admin/mata_pelajaran/create.blade.php` → form tambah mapel
- `admin/mata_pelajaran/edit.blade.php` → form edit mapel
- `admin/kelas/index.blade.php` → tabel kelas + tombol Assign Siswa
- `admin/kelas/create.blade.php` → form tambah kelas
- `admin/kelas/edit.blade.php` → form edit kelas
- `admin/kelas/assign_siswa.blade.php` → dua kolom: siswa belum assign (kiri) | siswa di kelas ini (kanan), dengan checkbox
- `admin/guru_mapel/index.blade.php` → daftar guru + mapel yang diajar + form assign mapel baru

**Guru Views (read only, tanpa tombol tambah/edit/hapus):**
- `guru/data_master/kelas.blade.php` → tabel kelas aktif
- `guru/data_master/mata_pelajaran.blade.php` → tabel mapel dikelompokkan per jenis

Semua view harus:
- Menggunakan Tailwind CSS dengan warna primary hijau (#16a34a)
- Menampilkan flash message sukses/error/warning
- Teks seluruhnya Bahasa Indonesia
- Responsif (mobile-friendly)

---

### 🔄 TUGAS 7 — Update Dashboard Admin

Edit `resources/views/admin/dashboard.blade.php` untuk menampilkan menu navigasi ke semua halaman data master yang sudah dibuat. Tampilkan juga statistik ringkas:
- Total guru aktif
- Total siswa aktif
- Total kelas (tahun ajaran aktif)
- Total mata pelajaran aktif

---

### 🔄 TUGAS 8 — Update Dashboard Guru

Edit `resources/views/guru/dashboard.blade.php` untuk menampilkan:
- Nama guru yang login
- Daftar mata pelajaran yang diajar (dari relasi `mataPelajaran`)
- Link ke halaman Data Master (read only)

---

### ✅ TUGAS 9 — Verifikasi & Update SKILL.md

Jalankan perintah berikut untuk memastikan tidak ada error:
```bash
php artisan route:list | grep -E "tahun|mapel|kelas|guru-mapel"
php artisan route:clear
php artisan cache:clear
php artisan serve
```

Test akses semua URL berikut:
- `http://localhost:8000/admin/tahun-ajaran` → daftar tahun ajaran (harus ada 4 data dari seeder)
- `http://localhost:8000/admin/mata-pelajaran` → daftar 15 mapel Kemenag
- `http://localhost:8000/admin/kelas` → daftar 9 kelas (VII-A s/d IX-C)
- `http://localhost:8000/admin/guru-mapel` → halaman assign mapel ke guru
- `http://localhost:8000/guru/data-master/kelas` → read only untuk guru

Ubah status Step 3 di `SKILL.md` dari `⬜ Belum` menjadi `✅ Selesai`.

---

## ✅ Kriteria Step 3 Dinyatakan Selesai:
- [ ] 5 migration baru berhasil dijalankan
- [ ] 5 model baru dibuat dengan relasi yang benar
- [ ] Seeder: 4 tahun ajaran, 15 mapel Kemenag, 9 kelas tersimpan di DB
- [ ] Controller Admin: CRUD untuk TahunAjaran, MataPelajaran, Kelas
- [ ] Controller Admin: Assign siswa ke kelas & assign mapel ke guru
- [ ] Controller Guru: Read only untuk kelas dan mata pelajaran
- [ ] Routes terdaftar dengan benar (tidak ada conflict)
- [ ] Semua view tampil dengan Tailwind tema hijau
- [ ] Dashboard admin menampilkan statistik dan navigasi
- [ ] Dashboard guru menampilkan mapel yang diajar
- [ ] SKILL.md Step 3 diupdate ke ✅ Selesai

---

## ⚠️ Troubleshooting Umum:

**Error: relation "tahun_ajaran" does not exist**
→ Pastikan urutan migration benar: `tahun_ajaran` harus dibuat sebelum `kelas`
→ Cek dengan: `php artisan migrate:status`

**Error: Class MataPelajaran not found**
→ Jalankan: `composer dump-autoload`

**Halaman assign siswa kosong padahal sudah ada siswa**
→ Pastikan siswa sudah di-import via Excel dan kolom `role` = `siswa`

---

*Prompt ini adalah bagian dari seri Step-by-Step project ujian-mts*
*Setelah Step 3 selesai, lanjut ke PROMPT_STEP_4.md — Bank Soal*