# Step 2 — Autentikasi & Manajemen User (3 Role)

**Status:** ✅ Selesai  
**Tanggal:** 2 Mei 2026

---

## Tujuan

Membangun sistem login multi-role (super_admin / guru / siswa), middleware perlindungan route, manajemen user CRUD oleh admin, dan import massal data siswa via Excel.

---

## Fitur yang Dibangun

- Login menggunakan **Email** (guru/admin) atau **NIS** (siswa) — satu form, satu field
- Redirect otomatis ke dashboard sesuai role setelah login
- 3 middleware proteksi route: `role.superadmin`, `role.guru`, `role.siswa`
- CRUD user oleh super admin (tambah, edit, hapus, lihat daftar)
- Import massal siswa dari file Excel (.xlsx/.xls)
- Download template Excel untuk import
- Seeder akun super admin default

---

## File yang Dibuat / Diubah

### Database

| File | Keterangan |
|------|------------|
| `database/migrations/2026_05_02_133939_modify_users_table_for_cbt.php` | Tambah kolom CBT ke tabel `users` |
| `database/seeders/SuperAdminSeeder.php` | Buat akun super admin default |

### Models

| File | Keterangan |
|------|------------|
| `app/Models/User.php` | Tambah fillable, scopes, helper role |

### Middleware

| File | Alias Route | Keterangan |
|------|-------------|------------|
| `app/Http/Middleware/RoleSuperAdmin.php` | `role.superadmin` | Hanya super_admin |
| `app/Http/Middleware/RoleGuru.php` | `role.guru` | Guru dan super_admin |
| `app/Http/Middleware/RoleSiswa.php` | `role.siswa` | Hanya siswa |
| `bootstrap/app.php` | — | Registrasi alias middleware |

### Controllers

| File | Keterangan |
|------|------------|
| `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Diubah: redirect sesuai role setelah login |
| `app/Http/Controllers/Admin/DashboardController.php` | Dashboard super admin |
| `app/Http/Controllers/Guru/DashboardController.php` | Dashboard guru |
| `app/Http/Controllers/Siswa/DashboardController.php` | Dashboard siswa |
| `app/Http/Controllers/Admin/UserController.php` | CRUD user + import Excel + download template |

### Form Requests

| File | Keterangan |
|------|------------|
| `app/Http/Requests/Auth/LoginRequest.php` | Diubah: mendukung login via Email ATAU NIS |
| `app/Http/Requests/Admin/StoreUserRequest.php` | Validasi tambah user baru |
| `app/Http/Requests/Admin/UpdateUserRequest.php` | Validasi update user (ignore unique self) |

### Import / Export

| File | Keterangan |
|------|------------|
| `app/Imports/SiswaImport.php` | Import siswa dari Excel dengan skip duplikat |
| `app/Exports/TemplateSiswaExport.php` | Template Excel untuk diisi dan diupload |

### Routes

| File | Keterangan |
|------|------------|
| `routes/web.php` | Diubah total: route per role dengan middleware |

### Views

| File | Keterangan |
|------|------------|
| `resources/views/auth/login.blade.php` | Diubah: field `identifier` (bukan `email`), Alpine.js toggle password |
| `resources/views/admin/dashboard.blade.php` | Dashboard admin dengan link ke manajemen user |
| `resources/views/guru/dashboard.blade.php` | Dashboard guru (placeholder) |
| `resources/views/siswa/dashboard.blade.php` | Dashboard siswa (placeholder) |
| `resources/views/admin/users/index.blade.php` | Daftar user + filter + import modal |
| `resources/views/admin/users/create.blade.php` | Form tambah user |
| `resources/views/admin/users/edit.blade.php` | Form edit user + toggle status aktif |

---

## Skema Database — Perubahan `users`

Migration: `2026_05_02_133939_modify_users_table_for_cbt.php`

```php
// Kolom yang ditambahkan ke tabel users
$table->string('nis', 20)->nullable()->unique();      // Nomor Induk Siswa
$table->string('email')->nullable()->change();         // Dijadikan nullable (siswa tidak punya email)
$table->enum('role', ['super_admin','guru','siswa'])->default('siswa');
$table->boolean('is_aktif')->default(true);
$table->enum('jenis_kelamin', ['L','P'])->nullable();
$table->string('no_telp', 15)->nullable();
```

**Tabel `users` lengkap setelah migrasi:**

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint | Primary key |
| `name` | varchar(255) | Nama lengkap |
| `email` | varchar(255) | Email (nullable, untuk guru/admin) |
| `nis` | varchar(20) | Nomor Induk Siswa (nullable, unique, untuk siswa) |
| `password` | varchar(255) | Password di-hash otomatis (cast `hashed`) |
| `role` | enum | `super_admin` / `guru` / `siswa` |
| `is_aktif` | boolean | Status akun aktif (default: true) |
| `jenis_kelamin` | enum | `L` / `P` (nullable) |
| `no_telp` | varchar(15) | Nomor telepon (nullable) |
| `remember_token` | varchar(100) | Token "ingat saya" |
| `created_at` / `updated_at` | timestamp | Timestamps otomatis |

---

## Alur Login

```
User buka /login
    ↓
Isi field "Email / NIS" + Password → POST /login
    ↓
LoginRequest::authenticate()
    ├── Jika identifier mengandung '@' → auth via email + is_aktif=true
    └── Jika tidak → auth via nis + is_aktif=true
    ↓
AuthenticatedSessionController::store()
    ↓ match($role)
    ├── super_admin → redirect /admin/dashboard
    ├── guru        → redirect /guru/dashboard
    └── siswa       → redirect /siswa/dashboard
```

---

## Daftar Route

```
GET  /                      → Redirect ke dashboard role (jika login) atau ke /login
GET  /login                 → Halaman login
POST /login                 → Proses login
POST /logout                → Logout

# Super Admin (middleware: auth, role.superadmin)
GET    /admin/dashboard             admin.dashboard
GET    /admin/users                 admin.users.index
GET    /admin/users/create          admin.users.create
POST   /admin/users                 admin.users.store
GET    /admin/users/{user}          admin.users.show
GET    /admin/users/{user}/edit     admin.users.edit
PUT    /admin/users/{user}          admin.users.update
DELETE /admin/users/{user}          admin.users.destroy
POST   /admin/users/import-siswa    admin.users.import-siswa
GET    /admin/users/template-excel  admin.users.template-excel

# Guru (middleware: auth, role.guru)
GET    /guru/dashboard              guru.dashboard

# Siswa (middleware: auth, role.siswa)
GET    /siswa/dashboard             siswa.dashboard
```

---

## Cara Menjalankan (setelah clone / setup ulang)

```bash
# 1. Jalankan migrasi
php artisan migrate

# 2. Buat akun super admin
php artisan db:seed --class=SuperAdminSeeder

# 3. Build asset frontend
npm run build

# 4. Verifikasi route
php artisan route:list
```

### Akun Default Super Admin

| Field | Nilai |
|-------|-------|
| Email | `admin@ujianmts.sch.id` |
| Password | `Admin@12345` |
| Role | `super_admin` |

> ⚠️ Ganti password setelah login pertama.

---

## Fitur Import Siswa Excel

### Format File Excel

Header kolom yang diharapkan (baris pertama):

| Kolom | Wajib | Keterangan |
|-------|-------|------------|
| `nis` | ✅ | Nomor Induk Siswa (unik) |
| `name` | ✅ | Nama lengkap |
| `jenis_kelamin` | ❌ | `L` atau `P` |
| `no_telp` | ❌ | Nomor telepon |

- **Password default** = NIS siswa (bisa langsung digunakan untuk login)
- Baris dengan NIS yang sudah terdaftar akan **dilewati** (tidak error, tidak duplikat)
- Baris dengan kolom `nis` atau `name` kosong akan dilewati

### Download Template

```
GET /admin/users/template-excel
```

Template sudah berisi 1 baris contoh dengan styling header hijau.

---

## Middleware — Cara Kerja

```php
// Contoh: RoleSuperAdmin
public function handle(Request $request, Closure $next): Response
{
    if (!Auth::check() || Auth::user()->role !== 'super_admin') {
        abort(403, 'Akses ditolak.');
    }
    return $next($request);
}
```

Alias terdaftar di `bootstrap/app.php`:

```php
$middleware->alias([
    'role.superadmin' => \App\Http\Middleware\RoleSuperAdmin::class,
    'role.guru'       => \App\Http\Middleware\RoleGuru::class,
    'role.siswa'      => \App\Http\Middleware\RoleSiswa::class,
]);
```

> **Catatan:** Gunakan `Auth::` facade (bukan `auth()` helper) agar IDE (Intelephense) dapat mengenali tipe return dan tidak menampilkan error palsu.

---

## Troubleshooting yang Pernah Ditemui

| Masalah | Penyebab | Solusi |
|---------|----------|--------|
| Import Excel diam-diam tidak ada data masuk | `WithValidation` melempar exception sebelum `SkipsErrors` menangkap | Hapus `WithValidation`, ganti dengan pengecekan manual `empty()` di `model()` |
| Password siswa tidak bisa untuk login | Double hashing: `Hash::make()` di import + cast `hashed` di Model | Hapus `Hash::make()` dari import — cukup kirim string, cast Model otomatis hash |
| IDE menampilkan error `Undefined method check/user` | Intelephense tidak mengenali return type `auth()` helper | Ganti ke `Auth::check()` / `Auth::user()` (facade) |
| `routes/web.php` replace gagal saat development | Breeze mengubah `web.php` saat instalasi | Baca isi file aktual sebelum edit, jangan asumsikan isi default |
| Login form field `email` masih ada di Breeze default | Breeze generate form dengan field `email` | Overwrite `resources/views/auth/login.blade.php` dengan form custom field `identifier` |
