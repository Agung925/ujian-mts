# 🚀 PROMPT STEP 1 — Inisialisasi Project ujian-mts

> Salin seluruh prompt di bawah ini dan paste ke GitHub Copilot Chat (Agent Mode) di VS Code.
> Pastikan kamu sudah berada di direktori kerja yang benar sebelum menjalankan prompt ini.

---

## ▶️ PROMPT STEP 1 (Copy dari garis bawah ini):

---

Kamu adalah AI developer assistant untuk project CBT (Computer Based Test) bernama **ujian-mts**.
Baca dan ikuti semua aturan yang ada di file `SKILL.md` di root project.

**Tugasmu di Step 1 ini adalah:**

### 1. Buat project Laravel baru
Jalankan perintah berikut di terminal untuk membuat project Laravel 11:
```bash
composer create-project laravel/laravel ujian-mts
cd ujian-mts
```

### 2. Install dependency yang dibutuhkan
Setelah project dibuat, jalankan perintah berikut satu per satu:
```bash
# Install Laravel Breeze untuk autentikasi
composer require laravel/breeze --dev

# Install package PDF
composer require barryvdh/laravel-dompdf

# Install package Excel
composer require maatwebsite/excel

# Install Breeze dengan Blade (bukan React/Vue)
php artisan breeze:install blade

# Install dependency Node.js
npm install

# Build asset Tailwind CSS
npm run build
```

### 3. Buat file SKILL.md di root project
Salin file `SKILL.md` yang sudah disiapkan ke dalam root folder `ujian-mts/`.

### 4. Buat file .github/copilot-instructions.md
- Buat folder `.github/` di dalam root project
- Salin file `copilot-instructions.md` ke dalam folder `.github/`
- Rename menjadi `copilot-instructions.md`

### 5. Konfigurasi file .env
Edit file `.env` di root project dengan konfigurasi berikut:

```env
APP_NAME="Sistem Ujian MTs"
APP_ENV=local
APP_KEY=  # akan di-generate otomatis
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ujian_mts
DB_USERNAME=postgres
DB_PASSWORD=isi_dengan_password_postgresql_kamu

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### 6. Buat database PostgreSQL
Jalankan perintah berikut di terminal untuk membuat database:
```bash
psql -U postgres -c "CREATE DATABASE ujian_mts;"
```

### 7. Generate APP_KEY dan jalankan migrasi awal
```bash
php artisan key:generate
php artisan migrate
```

### 8. Konfigurasi Tailwind CSS untuk tema warna hijau
Edit file `tailwind.config.js`, tambahkan konfigurasi warna custom:
```javascript
// Tambahkan di dalam theme.extend.colors
colors: {
  primary: {
    50:  '#f0fdf4',
    100: '#dcfce7',
    200: '#bbf7d0',
    300: '#86efac',
    400: '#4ade80',
    500: '#22c55e',
    600: '#16a34a', // ← Warna utama tema
    700: '#15803d',
    800: '#166534',
    900: '#14532d',
  }
}
```

### 9. Buat struktur folder Controller per role
Jalankan perintah berikut untuk membuat folder controller:
```bash
mkdir -p app/Http/Controllers/Admin
mkdir -p app/Http/Controllers/Guru
mkdir -p app/Http/Controllers/Siswa
mkdir -p app/Services
mkdir -p resources/views/layouts
mkdir -p resources/views/admin
mkdir -p resources/views/guru
mkdir -p resources/views/siswa
```

### 10. Verifikasi instalasi
Jalankan server development untuk memastikan semua berjalan dengan baik:
```bash
php artisan serve
```
Buka browser dan akses `http://localhost:8000` — harus muncul halaman Laravel default atau halaman login Breeze.

### 11. Jika semua berhasil, update SKILL.md
Ubah status Step 1 di tabel modul `SKILL.md` dari `⬜ Belum` menjadi `✅ Selesai`.

---

## ✅ Kriteria Step 1 Dinyatakan Selesai:
- [ ] Project Laravel 11 berhasil dibuat
- [ ] Semua dependency (Breeze, dompdf, excel) terinstall
- [ ] File SKILL.md ada di root project
- [ ] File .github/copilot-instructions.md ada
- [ ] File .env terkonfigurasi dengan benar (PostgreSQL)
- [ ] Database `ujian_mts` berhasil dibuat
- [ ] `php artisan migrate` berhasil tanpa error
- [ ] `php artisan serve` berhasil dan bisa diakses di browser
- [ ] Struktur folder Controller per role sudah dibuat

---

## ⚠️ Troubleshooting Umum:

**Error: SQLSTATE[08006] — koneksi PostgreSQL gagal**
→ Pastikan PostgreSQL service berjalan: `sudo service postgresql start`
→ Cek username & password di file .env

**Error: Class not found setelah composer install**
→ Jalankan: `composer dump-autoload`

**Tailwind CSS tidak muncul**
→ Jalankan ulang: `npm run build`

---

*Prompt ini adalah bagian dari seri Step-by-Step project ujian-mts*
*Setelah Step 1 selesai, lanjut ke PROMPT_STEP_2.md*
