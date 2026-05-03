# 🔐 PROMPT STEP 2 — Autentikasi & Manajemen User

> Salin seluruh prompt di bawah ini dan paste ke GitHub Copilot Chat (Agent Mode) di VS Code.
> Pastikan Step 1 sudah selesai dan kamu berada di dalam folder `ujian-mts/` sebelum menjalankan prompt ini.

---

## ▶️ PROMPT STEP 2 (Copy dari garis bawah ini):

---

Kamu adalah AI developer assistant untuk project **ujian-mts**.
Sebelum mulai, baca file `SKILL.md` di root project ini untuk memahami konteks, konvensi, dan aturan yang berlaku.

Kita sekarang mengerjakan **Step 2: Autentikasi & Manajemen User**.

---

### 📋 KONTEKS PENTING SEBELUM EKSEKUSI:

Sistem ini memiliki **3 role**: `super_admin`, `guru`, `siswa`

Aturan login:
- **Siswa** → login menggunakan **NIS** (Nomor Induk Siswa) + Password
- **Guru & Super Admin** → login menggunakan **Email** + Password

Implikasi teknis:
- Kolom `email` di tabel `users` boleh **nullable** (karena siswa tidak pakai email)
- Tambahkan kolom `nis` yang **nullable** (hanya diisi untuk siswa)
- Field login di form akan **otomatis mendeteksi** apakah input adalah NIS atau Email

---

### 🗄️ TUGAS 1 — Modifikasi Migration Tabel Users

Buat migration baru untuk menambahkan kolom ke tabel users:

```bash
php artisan make:migration modify_users_table_for_cbt --table=users
```

Isi migration tersebut dengan kode berikut:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom NIS untuk siswa (nullable karena guru tidak pakai NIS)
            $table->string('nis', 20)->nullable()->unique()->after('name');

            // Ubah email menjadi nullable karena siswa tidak wajib punya email
            $table->string('email')->nullable()->change();

            // Tambah role pengguna
            $table->enum('role', ['super_admin', 'guru', 'siswa'])->default('siswa')->after('email');

            // Status akun aktif/nonaktif
            $table->boolean('is_aktif')->default(true)->after('role');

            // Jenis kelamin
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('is_aktif');

            // Nomor telepon (opsional)
            $table->string('no_telp', 15)->nullable()->after('jenis_kelamin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nis', 'role', 'is_aktif', 'jenis_kelamin', 'no_telp']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
```

Jalankan migration:
```bash
php artisan migrate
```

---

### 🧩 TUGAS 2 — Update Model User

Edit file `app/Models/User.php` menjadi seperti ini:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi massal (mass assignment)
     */
    protected $fillable = [
        'name',
        'nis',
        'email',
        'password',
        'role',
        'is_aktif',
        'jenis_kelamin',
        'no_telp',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data kolom
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_aktif'          => 'boolean',
        ];
    }

    // =============================================
    // SCOPE — Filter berdasarkan role
    // =============================================

    /** Scope untuk mengambil hanya data siswa */
    public function scopeSiswa($query)
    {
        return $query->where('role', 'siswa');
    }

    /** Scope untuk mengambil hanya data guru */
    public function scopeGuru($query)
    {
        return $query->where('role', 'guru');
    }

    /** Scope untuk mengambil hanya akun yang aktif */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    // =============================================
    // HELPER — Cek role pengguna
    // =============================================

    /** Cek apakah user adalah super admin */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /** Cek apakah user adalah guru */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /** Cek apakah user adalah siswa */
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }
}
```

---

### 🔐 TUGAS 3 — Buat Custom Authentication Logic

Karena siswa login pakai NIS dan guru/admin pakai email, kita perlu override logic login default Breeze.

Buat file `app/Http/Requests/Auth/LoginRequest.php` — **timpa file yang sudah ada** dengan kode berikut:

```php
<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'identifier' bisa berupa NIS atau Email
            'identifier' => ['required', 'string'],
            'password'   => ['required', 'string'],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia
     */
    public function messages(): array
    {
        return [
            'identifier.required' => 'NIS atau Email wajib diisi.',
            'password.required'   => 'Password wajib diisi.',
        ];
    }

    /**
     * Proses autentikasi — deteksi otomatis NIS atau Email
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = $this->input('identifier');
        $password   = $this->input('password');

        // Deteksi apakah input adalah email atau NIS
        // Jika mengandung '@' maka dianggap email, selainnya dianggap NIS
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            // Login menggunakan email (untuk guru & super_admin)
            $credentials = [
                'email'    => $identifier,
                'password' => $password,
                'is_aktif' => true,
            ];
        } else {
            // Login menggunakan NIS (untuk siswa)
            $credentials = [
                'nis'      => $identifier,
                'password' => $password,
                'is_aktif' => true,
            ];
        }

        // Coba autentikasi
        if (!Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identifier' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->input('identifier')) . '|' . $this->ip()
        );
    }
}
```

---

### 🛡️ TUGAS 4 — Buat Middleware Role

Buat 3 middleware untuk proteksi route berdasarkan role:

```bash
php artisan make:middleware RoleSuperAdmin
php artisan make:middleware RoleGuru
php artisan make:middleware RoleSiswa
```

**Isi `app/Http/Middleware/RoleSuperAdmin.php`:**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleSuperAdmin
{
    /**
     * Hanya izinkan akses jika role adalah super_admin
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Super Admin.');
        }

        return $next($request);
    }
}
```

**Isi `app/Http/Middleware/RoleGuru.php`:**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleGuru
{
    /**
     * Hanya izinkan akses jika role adalah guru atau super_admin
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['guru', 'super_admin'])) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Guru.');
        }

        return $next($request);
    }
}
```

**Isi `app/Http/Middleware/RoleSiswa.php`:**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleSiswa
{
    /**
     * Hanya izinkan akses jika role adalah siswa
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'siswa') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Siswa.');
        }

        return $next($request);
    }
}
```

Daftarkan middleware di `bootstrap/app.php` — tambahkan di bagian `withMiddleware`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role.superadmin' => \App\Http\Middleware\RoleSuperAdmin::class,
        'role.guru'       => \App\Http\Middleware\RoleGuru::class,
        'role.siswa'      => \App\Http\Middleware\RoleSiswa::class,
    ]);
})
```

---

### 🗺️ TUGAS 5 — Update Routes

Edit file `routes/web.php` menjadi seperti ini:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Guru\DashboardController as GuruDashboard;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Admin\UserController;

// =============================================
// ROUTE PUBLIK — Redirect ke dashboard sesuai role setelah login
// =============================================
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard.' . auth()->user()->role);
    }
    return redirect()->route('login');
});

// =============================================
// ROUTE SUPER ADMIN
// =============================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.superadmin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Manajemen User
    Route::resource('users', UserController::class);
    Route::post('users/import-siswa', [UserController::class, 'importSiswa'])->name('users.import-siswa');
    Route::get('users/template-excel', [UserController::class, 'downloadTemplate'])->name('users.template-excel');
});

// =============================================
// ROUTE GURU
// =============================================
Route::prefix('guru')->name('guru.')->middleware(['auth', 'role.guru'])->group(function () {
    Route::get('/dashboard', [GuruDashboard::class, 'index'])->name('dashboard');
});

// =============================================
// ROUTE SISWA
// =============================================
Route::prefix('siswa')->name('siswa.')->middleware(['auth', 'role.siswa'])->group(function () {
    Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('dashboard');
});

// =============================================
// ROUTE AUTH (Login, Logout — dari Breeze)
// =============================================
require __DIR__ . '/auth.php';
```

---

### 🎛️ TUGAS 6 — Buat Controller Dashboard & Redirect Setelah Login

**Buat controller dashboard untuk setiap role:**

```bash
php artisan make:controller Admin/DashboardController
php artisan make:controller Guru/DashboardController
php artisan make:controller Siswa/DashboardController
php artisan make:controller Admin/UserController --resource
```

**Isi `app/Http/Controllers/Admin/DashboardController.php`:**
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /** Tampilkan halaman dashboard super admin */
    public function index()
    {
        return view('admin.dashboard');
    }
}
```

**Isi `app/Http/Controllers/Guru/DashboardController.php`:**
```php
<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /** Tampilkan halaman dashboard guru */
    public function index()
    {
        return view('guru.dashboard');
    }
}
```

**Isi `app/Http/Controllers/Siswa/DashboardController.php`:**
```php
<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /** Tampilkan halaman dashboard siswa */
    public function index()
    {
        return view('siswa.dashboard');
    }
}
```

**Update `app/Http/Controllers/Auth/AuthenticatedSessionController.php`** — ubah method `store` agar redirect sesuai role:

```php
/** Redirect ke dashboard sesuai role setelah login berhasil */
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $role = auth()->user()->role;

    // Arahkan ke dashboard sesuai role
    return match($role) {
        'super_admin' => redirect()->route('admin.dashboard'),
        'guru'        => redirect()->route('guru.dashboard'),
        'siswa'       => redirect()->route('siswa.dashboard'),
        default       => redirect('/'),
    };
}
```

---

### 📊 TUGAS 7 — Fitur Import Siswa via Excel

**Buat Import Class:**
```bash
php artisan make:import SiswaImport --model=User
```

**Isi `app/Imports/SiswaImport.php`:**
```php
<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    /**
     * Mapping data dari baris Excel ke Model User
     * Format kolom Excel: nis | nama | jenis_kelamin | no_telp
     */
    public function model(array $row): User
    {
        return new User([
            'nis'           => $row['nis'],
            'name'          => $row['nama'],
            'jenis_kelamin' => strtoupper($row['jenis_kelamin']), // L atau P
            'no_telp'       => $row['no_telp'] ?? null,
            'role'          => 'siswa',
            'is_aktif'      => true,
            // Password default = NIS siswa (siswa wajib ganti saat pertama login)
            'password'      => Hash::make($row['nis']),
        ]);
    }

    /**
     * Aturan validasi untuk setiap baris Excel
     */
    public function rules(): array
    {
        return [
            'nis'           => ['required', 'string', 'unique:users,nis'],
            'nama'          => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P,l,p'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nis.required'           => 'Kolom NIS wajib diisi.',
            'nis.unique'             => 'NIS :input sudah terdaftar di sistem.',
            'nama.required'          => 'Kolom Nama wajib diisi.',
            'jenis_kelamin.required' => 'Kolom Jenis Kelamin wajib diisi.',
            'jenis_kelamin.in'       => 'Jenis Kelamin harus L atau P.',
        ];
    }
}
```

**Update `app/Http/Controllers/Admin/UserController.php`** — tambahkan method `importSiswa` dan `downloadTemplate`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\SiswaImport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /** Tampilkan daftar semua user */
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    /** Tampilkan form tambah user */
    public function create()
    {
        return view('admin.users.create');
    }

    /** Simpan user baru (guru atau super_admin) */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'role'          => 'required|in:super_admin,guru',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_telp'       => 'nullable|string|max:15',
            'password'      => 'required|min:8|confirmed',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'role'          => $request->role,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_telp'       => $request->no_telp,
            'password'      => $request->password,
            'is_aktif'      => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /** Tampilkan form edit user */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /** Update data user */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'is_aktif'      => 'required|boolean',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_telp'       => 'nullable|string|max:15',
        ]);

        $user->update($request->only(['name', 'is_aktif', 'jenis_kelamin', 'no_telp']));

        return redirect()->route('admin.users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    /** Hapus user */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Import siswa massal dari file Excel
     * Format kolom: nis | nama | jenis_kelamin | no_telp
     */
    public function importSiswa(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        $import = new SiswaImport();
        Excel::import($import, $request->file('file_excel'));

        $gagal = count($import->errors());

        if ($gagal > 0) {
            return redirect()->route('admin.users.index')
                ->with('warning', "Import selesai dengan {$gagal} baris gagal. Periksa format data Excel kamu.");
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Import siswa berhasil dilakukan.');
    }

    /**
     * Download template Excel untuk import siswa
     */
    public function downloadTemplate()
    {
        $templatePath = public_path('templates/template_import_siswa.xlsx');

        // Jika file template belum ada, buat otomatis menggunakan Excel facade
        if (!file_exists($templatePath)) {
            // Buat folder jika belum ada
            if (!is_dir(public_path('templates'))) {
                mkdir(public_path('templates'), 0755, true);
            }

            // Generate file template sederhana
            Excel::store(
                new \App\Exports\TemplateSiswaExport(),
                'templates/template_import_siswa.xlsx',
                'public'
            );
        }

        return response()->download($templatePath, 'template_import_siswa.xlsx');
    }
}
```

**Buat Export Class untuk template:**
```bash
php artisan make:export TemplateSiswaExport
```

**Isi `app/Exports/TemplateSiswaExport.php`:**
```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateSiswaExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * Baris contoh data untuk template
     */
    public function array(): array
    {
        return [
            ['2024001', 'Ahmad Fauzi', 'L', '081234567890'],
            ['2024002', 'Siti Aisyah', 'P', '082345678901'],
        ];
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return ['nis', 'nama', 'jenis_kelamin', 'no_telp'];
    }

    /**
     * Style header agar bold dan berwarna
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '16a34a']],
            ],
        ];
    }
}
```

---

### 🎨 TUGAS 8 — Buat View Login Custom

Edit file `resources/views/auth/login.blade.php` — ganti konten dengan form login yang mendukung NIS dan Email:

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Ujian MTs</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-primary-600 to-primary-900 flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">

        {{-- Logo & Judul --}}
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-primary-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Sistem Ujian MTs</h1>
            <p class="text-gray-500 text-sm mt-1">Masukkan kredensial kamu untuk melanjutkan</p>
        </div>

        {{-- Error Message --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Form Login --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Field NIS atau Email --}}
            <div class="mb-5">
                <label for="identifier" class="block text-sm font-medium text-gray-700 mb-2">
                    NIS atau Email
                </label>
                <input
                    id="identifier"
                    type="text"
                    name="identifier"
                    value="{{ old('identifier') }}"
                    required
                    autofocus
                    placeholder="Masukkan NIS (siswa) atau Email (guru/admin)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm"
                >
            </div>

            {{-- Field Password --}}
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <div class="relative" x-data="{ tampilPassword: false }">
                    <input
                        id="password"
                        :type="tampilPassword ? 'text' : 'password'"
                        name="password"
                        required
                        placeholder="Masukkan password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm pr-12"
                    >
                    {{-- Tombol toggle tampilkan/sembunyikan password --}}
                    <button type="button" @click="tampilPassword = !tampilPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg x-show="!tampilPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="tampilPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Tombol Login --}}
            <button type="submit"
                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 text-sm">
                Masuk ke Sistem
            </button>

        </form>

        {{-- Petunjuk --}}
        <div class="mt-6 p-4 bg-gray-50 rounded-lg text-xs text-gray-500">
            <p class="font-medium text-gray-600 mb-1">Petunjuk Login:</p>
            <p>• <strong>Siswa</strong>: gunakan NIS sebagai username & password default</p>
            <p>• <strong>Guru / Admin</strong>: gunakan Email & password yang sudah diberikan</p>
        </div>

    </div>

</body>
</html>
```

---

### 📄 TUGAS 9 — Buat View Dashboard Sementara (Placeholder)

Buat file-file view dashboard berikut agar routing tidak error:

**`resources/views/admin/dashboard.blade.php`:**
```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-xl p-8 shadow">
        <h1 class="text-2xl font-bold text-primary-600">Dashboard Super Admin</h1>
        <p class="text-gray-600 mt-2">Selamat datang, {{ auth()->user()->name }}!</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm">Logout</button>
        </form>
    </div>
</body>
</html>
```

Buat juga `resources/views/guru/dashboard.blade.php` dan `resources/views/siswa/dashboard.blade.php` dengan isi serupa (ganti teks "Super Admin" sesuai role).

---

### 🌱 TUGAS 10 — Buat Seeder Super Admin

```bash
php artisan make:seeder SuperAdminSeeder
```

**Isi `database/seeders/SuperAdminSeeder.php`:**
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Buat akun super admin default untuk pertama kali
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ujianmts.sch.id'],
            [
                'name'     => 'Super Administrator',
                'email'    => 'admin@ujianmts.sch.id',
                'password' => Hash::make('Admin@12345'),
                'role'     => 'super_admin',
                'is_aktif' => true,
            ]
        );

        $this->command->info('✅ Super Admin berhasil dibuat!');
        $this->command->info('   Email    : admin@ujianmts.sch.id');
        $this->command->info('   Password : Admin@12345');
        $this->command->warn('   ⚠️  Segera ganti password setelah login pertama!');
    }
}
```

Jalankan seeder:
```bash
php artisan db:seed --class=SuperAdminSeeder
```

---

### 🔄 TUGAS 11 — Rebuild Asset & Test

```bash
# Rebuild Tailwind CSS
npm run build

# Cek apakah tidak ada error syntax
php artisan route:list

# Jalankan server
php artisan serve
```

Test login dengan akun super admin:
- Buka `http://localhost:8000`
- Masukkan email: `admin@ujianmts.sch.id`
- Password: `Admin@12345`
- Harus redirect ke `/admin/dashboard`

---

### ✅ Setelah Semua Selesai — Update SKILL.md

Ubah status Step 2 di tabel modul `SKILL.md` dari `⬜ Belum` menjadi `✅ Selesai`.

---

## ✅ Kriteria Step 2 Dinyatakan Selesai:
- [ ] Migration `modify_users_table_for_cbt` berhasil dijalankan
- [ ] Model `User` sudah memiliki scope dan helper role
- [ ] `LoginRequest` custom sudah bisa deteksi NIS dan Email
- [ ] 3 Middleware role sudah dibuat dan terdaftar di `bootstrap/app.php`
- [ ] Routes per role sudah terdefinisi dengan benar
- [ ] Controller Dashboard untuk 3 role sudah dibuat
- [ ] `SiswaImport` dan `TemplateSiswaExport` sudah dibuat
- [ ] View login custom sudah tampil dengan benar
- [ ] View dashboard placeholder untuk 3 role sudah ada
- [ ] Seeder `SuperAdminSeeder` berhasil dijalankan
- [ ] Login dengan `admin@ujianmts.sch.id` berhasil dan redirect ke `/admin/dashboard`
- [ ] SKILL.md Step 2 diupdate ke `✅ Selesai`

---

## ⚠️ Troubleshooting Umum:

**Error: Column already exists**
→ Jalankan: `php artisan migrate:status` untuk cek status migration
→ Jika tabel sudah ada kolom tersebut, skip atau edit migration

**Error: Route [dashboard.super_admin] not defined**
→ Pastikan nama route di `web.php` menggunakan `->name('dashboard')` bukan nama lain
→ Cek dengan: `php artisan route:list | grep dashboard`

**Error: Class SiswaImport not found**
→ Jalankan: `composer dump-autoload`

**Halaman login tidak memakai Tailwind**
→ Pastikan `@vite` directive ada di view
→ Jalankan ulang: `npm run build`

---

*Prompt ini adalah bagian dari seri Step-by-Step project ujian-mts*
*Setelah Step 2 selesai, lanjut ke PROMPT_STEP_3.md — Data Master*