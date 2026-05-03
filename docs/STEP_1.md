# Step 1 — Inisialisasi Project & Konfigurasi Environment

**Status:** ✅ Selesai  
**Tanggal:** 2 Mei 2026

---

## Tujuan

Membangun pondasi project Laravel dari nol: instalasi dependensi, konfigurasi environment, koneksi database PostgreSQL, setup Tailwind CSS dengan tema hijau, dan memastikan server berjalan (HTTP 200).

---

## Tech Stack yang Diinstal

| Package | Versi | Perintah Instalasi |
|---------|-------|--------------------|
| Laravel Framework | 11.x | `composer create-project laravel/laravel ujian-mts` |
| Laravel Breeze | ^2.4 | `composer require laravel/breeze` |
| maatwebsite/excel | ^3.1 | `composer require maatwebsite/excel` |
| barryvdh/laravel-dompdf | ^3.1 | `composer require barryvdh/laravel-dompdf` |
| Tailwind CSS | ^3.4 | `npm install -D tailwindcss@^3.4` |
| Alpine.js | ^3.4 | `npm install alpinejs` |
| Vite (downgrade) | ^5.0 | Downgrade dari v8 karena Node.js 18.20.4 |
| laravel-vite-plugin | ^1.0 | Downgrade bersama Vite |

> ⚠️ **Catatan penting:** Vite v8 membutuhkan Node.js v20+. Server menggunakan Node.js 18.20.4, sehingga Vite harus di-downgrade ke v5. `@tailwindcss/vite` juga dihapus karena tidak kompatibel dengan Vite 5.

### PHP Extensions yang Harus Ada

```bash
sudo apt-get install -y php8.5-gd php8.5-zip
```

Diperlukan oleh `maatwebsite/excel` untuk memproses file Excel (.xlsx).

---

## File yang Dibuat / Diubah

### Konfigurasi Utama

| File | Perubahan |
|------|-----------|
| `.env` | Konfigurasi lengkap (lihat bagian Environment) |
| `tailwind.config.js` | Tambah tema warna `primary` hijau (#16a34a) |
| `package.json` | Vite downgrade ke ^5.0, laravel-vite-plugin ke ^1.0 |
| `vite.config.js` | Tetap default (tanpa @tailwindcss/vite) |
| `resources/css/app.css` | Import Tailwind directives |
| `resources/js/app.js` | Import Alpine.js + bootstrap.js |
| `resources/js/bootstrap.js` | Dibuat manual (hilang saat instalasi Breeze) — berisi setup Axios |
| `SKILL.md` | Memori permanen project (dibuat manual di root) |

---

## Konfigurasi `.env`

```env
APP_NAME="Sistem Ujian MTs"
APP_ENV=local
APP_KEY=base64:...         # Di-generate otomatis oleh php artisan key:generate
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost
APP_LOCALE=id

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ujian_mts
DB_USERNAME=postgres
DB_PASSWORD=++123

SESSION_DRIVER=database
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

> ⚠️ Koneksi PostgreSQL harus menggunakan `DB_HOST=127.0.0.1` (bukan `localhost`) untuk menghindari masalah peer authentication di Debian.

---

## Konfigurasi `tailwind.config.js`

Tema warna `primary` ditambahkan agar kelas seperti `bg-primary-600`, `text-primary-500`, `ring-primary-500` tersedia di seluruh views.

```js
// tailwind.config.js
theme: {
    extend: {
        colors: {
            primary: {
                50:  '#f0fdf4',
                100: '#dcfce7',
                200: '#bbf7d0',
                300: '#86efac',
                400: '#4ade80',
                500: '#22c55e',
                600: '#16a34a',   // ← warna utama
                700: '#15803d',
                800: '#166534',
                900: '#14532d',
            }
        }
    }
}
```

---

## Database

### Cara Membuat Database

```bash
PGPASSWORD='++123' psql -U postgres -h 127.0.0.1 -c "CREATE DATABASE ujian_mts;"
```

> PostgreSQL di Debian menggunakan peer authentication secara default. Selalu gunakan `-h 127.0.0.1` dan `PGPASSWORD` environment variable untuk bypass.

### Migrasi Awal (bawaan Laravel)

```bash
php artisan migrate
```

Tabel yang dibuat:
- `users` — tabel autentikasi default Laravel
- `cache` — penyimpanan cache
- `jobs` — antrian pekerjaan
- `sessions` — sesi login (karena `SESSION_DRIVER=database`)

---

## Perintah Build & Verifikasi

```bash
# Install node modules
npm install

# Build assets CSS + JS
npm run build

# Verifikasi server (harus HTTP 200)
curl -s -o /dev/null -w "%{http_code}" http://localhost
```

---

## Troubleshooting yang Pernah Ditemui

| Masalah | Penyebab | Solusi |
|---------|----------|--------|
| `GD extension missing` | PHP 8.5 tidak install GD | `sudo apt-get install php8.5-gd` |
| `Zip extension missing` | PHP 8.5 tidak install Zip | `sudo apt-get install php8.5-zip` |
| Vite 8 error saat `npm run build` | Node.js 18 tidak kompatibel dengan Vite 8 | Downgrade `vite` ke `^5.0` dan `laravel-vite-plugin` ke `^1.0` di `package.json` |
| `bootstrap.js not found` | File hilang setelah instalasi Breeze | Buat manual `resources/js/bootstrap.js` dengan setup Axios |
| PostgreSQL peer auth error | Koneksi via socket bukan TCP | Gunakan `DB_HOST=127.0.0.1` di `.env` |
| `@tailwindcss/vite` error | Plugin ini membutuhkan Vite 8 | Hapus dari `package.json` dan `vite.config.js` |

---

## Struktur Folder Setelah Step 1

```
ujian-mts/
├── resources/
│   ├── css/app.css              ← Tailwind directives
│   ├── js/
│   │   ├── app.js               ← Import Alpine.js
│   │   └── bootstrap.js         ← Setup Axios (dibuat manual)
├── public/build/                ← Output Vite (di-generate oleh npm run build)
├── .env                         ← Konfigurasi environment
├── tailwind.config.js           ← Tema warna primary hijau
├── SKILL.md                     ← Memori project untuk AI agent
└── package.json                 ← Vite 5, Alpine.js, Tailwind 3
```
