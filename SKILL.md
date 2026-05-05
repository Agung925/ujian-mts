# SKILL.md — ujian-mts CBT System
> File ini adalah **memori permanen** untuk AI Agent / Copilot.
> Selalu baca file ini sebelum mengeksekusi perintah apapun pada project ini.

---

## 📋 Identitas Project

| Key | Value |
|-----|-------|
| **Nama Project** | ujian-mts |
| **Tipe** | CBT (Computer Based Test) |
| **Target Pengguna** | MTs (Madrasah Tsanawiyah) |
| **Jenjang Fokus** | Kelas VII, VIII, IX |
| **Bahasa UI** | Indonesia |
| **Bahasa Komentar Kode** | Indonesia |
| **Bahasa Variabel/Fungsi** | English (ikuti konvensi Laravel) |

---

## 🛠️ Tech Stack (WAJIB DIIKUTI — TIDAK BOLEH DIGANTI)

| Layer | Teknologi | Versi |
|-------|-----------|-------|
| **Backend** | Laravel | 11 |
| **Frontend** | Blade + Alpine.js + Tailwind CSS | Latest |
| **Database** | PostgreSQL | 18 |
| **Server** | Nginx | 1.22.1 |
| **Runtime** | PHP | 8.5.5 |
| **Package Manager** | Composer | 2.8.11 |
| **Node.js** | Node.js | 18.20.4 |
| **OS** | Linux Debian | 12 |
| **Auth Starter** | Laravel Breeze | Latest |
| **PDF** | barryvdh/laravel-dompdf | Latest |
| **Excel** | maatwebsite/laravel-excel | Latest |

> ⚠️ PENTING: Jangan gunakan Vue.js, React, Inertia.js, atau framework JS berat lainnya.
> Gunakan Alpine.js untuk interaktivitas ringan di frontend.

---

## 👥 Role Sistem (3 Role)

```
1. super_admin  → Akses penuh ke seluruh sistem
2. guru         → Kelola soal, ujian, dan lihat nilai
3. siswa        → Hanya bisa mengikuti ujian
```

---

## 🗄️ Database

```
Engine    : PostgreSQL 18
Nama DB   : ujian_mts
Prefix    : (tidak ada prefix tabel)
Timezone  : Asia/Jakarta
Charset   : UTF-8
```

---

## 📦 Modul & Status Pengerjaan

| Step | Modul | Status |
|------|-------|--------|
| Step 1 | Inisialisasi Project & Konfigurasi Environment | ✅ Selesai |
| Step 2 | Autentikasi & Manajemen User (3 Role) | ✅ Selesai |
| Step 3 | Data Master (Kelas, Mata Pelajaran, Tahun Ajaran) | ✅ Selesai |
| Step 4 | Bank Soal (PG, B/S, Essay + Gambar) | ✅ Selesai |
| Step 5 | Manajemen Ujian (Jadwal, Token, Acak Soal) | ✅ Selesai |
| Step 6 | Ruang Ujian Siswa (Timer, Anti-Cheat, Auto-Submit) | ✅ Selesai |
| Step 7 | Nilai & Laporan (Auto-score, Export PDF/Excel) | ✅ Selesai |

> Tandai `✅ Selesai` ketika sebuah step telah selesai dikerjakan.

---

## 📁 Struktur Folder Project (Konvensi Wajib)

```
ujian-mts/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          ← Controller untuk Super Admin
│   │   │   ├── Guru/           ← Controller untuk Guru
│   │   │   └── Siswa/          ← Controller untuk Siswa
│   │   ├── Middleware/
│   │   └── Requests/           ← Form Request Validation
│   ├── Models/
│   └── Services/               ← Business logic dipisah dari Controller
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/            ← Layout utama per role
│       ├── admin/
│       ├── guru/
│       └── siswa/
├── routes/
│   ├── web.php
│   └── auth.php
└── SKILL.md                    ← File ini
```

---

## 🗃️ Daftar Tabel Database (Rencana)

```sql
-- Master Data
users                  → id, name, email, password, role, aktif
kelas                  → id, nama_kelas, tingkat (VII/VIII/IX), tahun_ajaran_id
mata_pelajaran         → id, nama_mapel, kode_mapel, jenis (umum/keagamaan)
tahun_ajaran           → id, nama, semester, is_aktif

-- Relasi
guru_mapel             → id, user_id, mata_pelajaran_id
siswa_kelas            → id, user_id, kelas_id, tahun_ajaran_id

-- Bank Soal
bank_soal              → id, guru_id, mapel_id, pertanyaan, tipe_soal, gambar, kategori, sub_kategori
pilihan_jawaban        → id, soal_id, teks_pilihan, is_benar

-- Ujian
ujian                  → id, judul, mapel_id, kelas_id, guru_id, durasi_menit, token, jadwal_mulai, jadwal_selesai, acak_soal, acak_jawaban, status
ujian_soal             → id, ujian_id, soal_id, nomor_urut, bobot_nilai

-- Pelaksanaan Ujian
sesi_ujian             → id, ujian_id, siswa_id, waktu_mulai, waktu_selesai, status, nilai_akhir
jawaban_siswa          → id, sesi_id, soal_id, jawaban_id, jawaban_essay, is_benar, nilai
```

---

## 📝 Konvensi Penulisan Kode

```
✅ Nama Controller  : PascalCase  → BankSoalController
✅ Nama Model       : PascalCase  → BankSoal
✅ Nama Migration   : snake_case  → create_bank_soal_table
✅ Nama Route       : kebab-case  → /bank-soal/tambah
✅ Nama View        : snake_case  → bank_soal/tambah.blade.php
✅ Nama Kolom DB    : snake_case  → nama_kelas, tahun_ajaran_id
✅ Komentar         : Bahasa Indonesia yang jelas
✅ Validasi Input   : Selalu gunakan Form Request (bukan inline di Controller)
✅ Logika Bisnis    : Pisahkan ke Services/ jika kompleks
```

---

## 🔐 Aturan Keamanan (Wajib Diimplementasi)

```
✅ Setiap route diproteksi dengan middleware role
✅ Semua input wajib divalidasi menggunakan Form Request
✅ Token ujian di-hash sebelum disimpan
✅ Anti-cheat: deteksi pindah tab menggunakan Page Visibility API
✅ Siswa tidak bisa akses URL ujian tanpa token yang valid
✅ CSRF protection aktif di semua form
```

---

---

## 🎨 Panduan UI/UX

```
✅ Framework CSS    : Tailwind CSS (v3)
✅ Dark Mode        : Enabled via class-based mode (darkMode: 'class' di tailwind.config.js)
✅ Interaktivitas   : Alpine.js v3 (x-data, x-show, x-on)
✅ Ikon             : Heroicons (sudah include di Tailwind)
✅ Warna Tema       : 
   - Primary: Hijau #16a34a (tema Islami MTs)
   - Light Mode: bg-white, text-gray-900
   - Dark Mode: bg-gray-800/900, text-gray-100/200 (soft, tidak silau)
✅ Responsif        : Wajib mobile-friendly
✅ Bahasa UI        : Seluruhnya Bahasa Indonesia
✅ Notifikasi       : Gunakan session flash message
✅ Dark Mode Toggle : 
   - Tersedia di navbar (ikon matahari/bulan)
   - Desktop: button di sebelah avatar
   - Mobile: menu toggle item
   - Preference disimpan di localStorage
```

**Dark Mode Implementation:**
- **File Config**: `tailwind.config.js` (darkMode: 'class', safelist untuk dark variants)
- **File Main Layout**: `resources/views/layouts/app.blade.php` (Alpine.js binding + localStorage)
- **File Navigation**: `resources/views/layouts/navigation.blade.php` (toggle button + sun/moon icons)
- **47 View Files**: Updated dengan dark mode classes otomatis
- **CSS Warna Dark**:
  - `bg-gray-50 dark:bg-gray-900` (page background)
  - `bg-white dark:bg-gray-800` (card/panel background)
  - `text-gray-700 dark:text-gray-300` (normal text)
  - `border-gray-200 dark:border-gray-700` (borders)

---

## ⛔ Larangan (JANGAN DILAKUKAN)

```
❌ Jangan gunakan React / Vue.js / Inertia.js
❌ Jangan gunakan database selain PostgreSQL
❌ Jangan hardcode kredensial di kode — gunakan .env
❌ Jangan skip validasi input
❌ Jangan taruh logika bisnis di View
❌ Jangan gunakan raw SQL jika Eloquent bisa menanganinya
❌ Jangan lupa menambahkan foreign key constraint di migration
```

---

## 📌 Catatan Khusus untuk AI Agent

1. **Selalu baca file ini** sebelum mengeksekusi instruksi apapun
2. **Tanyakan konfirmasi** jika ada instruksi yang ambigu
3. **Jangan skip step** — ikuti urutan Step 1 → Step 7
4. **Tandai modul selesai** di tabel status setiap kali satu step tuntas
5. **Gunakan Bahasa Indonesia** untuk semua komentar dalam kode
6. **Developer adalah junior programmer** — berikan penjelasan pada setiap kode yang dibuat
7. **Selalu generate migration** sebelum membuat Model dan Controller

---

*Terakhir diperbarui: Step 7 — Nilai & Laporan + UI Enhancements*

---

## 📚 CHANGELOG — Feature & Improvement

### 🆕 Dark Mode (May 6, 2026)
**Problem**: UI putih terang menyebabkan kelelahan mata di tempat gelap (malam hari)

**Solution**:
- Enable Tailwind `darkMode: 'class'` di `tailwind.config.js`
- Alpine.js binding di `app.blade.php` untuk toggle class pada `<html>` element
- Toggle button di navbar (desktop & mobile) dengan sun/moon icons
- LocalStorage persistence — preference user tersimpan
- Batch update 47 view files dengan dark variants:
  - Light: `bg-white text-gray-700`
  - Dark: `dark:bg-gray-800 dark:text-gray-300`

**Files Changed**:
- `tailwind.config.js` — Add `darkMode: 'class'`, update safelist untuk dark variants
- `resources/views/layouts/app.blade.php` — Alpine.js dark mode state + localStorage
- `resources/views/layouts/navigation.blade.php` — Toggle button + color updates
- 47 view files — Batch dark mode classes update via `scripts/add_dark_mode.py`

**Build Result**: 73.37 kB gzipped CSS (all dark variants included)

**Test**: Toggle di navbar → dark mode aktif/nonaktif → refresh page → preference tersimpan ✓

---

### � Fix Dark Mode Text Visibility (May 6, 2026)
**Problem**: Text di dropdown dan navigation components tidak terlihat saat dark mode
         (text color default tidak menyesuaikan dengan background gelap)

**Root Cause**: 
- `dropdown-link.blade.php` → `text-gray-700` tanpa `dark:text-gray-300`
- `responsive-nav-link.blade.php` → `text-gray-600` tanpa dark variant, hanya background saja

**Solution**:
- `dropdown-link`: Add `dark:text-gray-300` + `dark:hover:bg-gray-700`
- `responsive-nav-link`: Comprehensive dark mode support:
  - Inactive: `text-gray-600 dark:text-gray-400`
  - Active: `text-indigo-700 dark:text-indigo-400` + dark backgrounds
  - Hover/Focus: Dark variants untuk semua states

**Files Changed**:
- `resources/views/components/dropdown-link.blade.php` — Add dark text color
- `resources/views/components/responsive-nav-link.blade.php` — Add full dark mode support

**CSS Result**: 74.36 kB gzipped (from 73.37 kB, +0.99 kB for dark variants)

**Test**: 
- Toggle dark mode → dropdown items sekarang terlihat jelas ✓
- Navigation links readable di dark mode ✓
- Active/hover states terlihat di dark mode ✓

---
### 🎯 Fix Form Element Text Visibility in Dark Mode (May 6, 2026)
**Problem**: Filter inputs, select dropdowns, dan textarea tidak terlihat saat dark mode
         (text dan borders tidak menyesuaikan dengan background gelap)

**Root Cause**: 
- 23 form elements across 13 view files missing dark mode variants
- Pattern: `border border-gray-300 rounded-lg` tanpa `dark:border-gray-600`
- Missing background dark variant: `dark:bg-gray-700`
- Missing text dark variant: `dark:text-gray-100`

**Solution**:
- Identify semua form elements (input, select, textarea) yang perlu dark mode
- Pattern yang diterapkan pada semua form elements:
  - `border-gray-300` → `border-gray-300 dark:border-gray-600`
  - Add `bg-white dark:bg-gray-700` untuk background
  - Add `text-gray-900 dark:text-gray-100` untuk text visibility

**Files Changed** (13 total):
1. `resources/views/guru/bank_soal/index.blade.php` — 3 filter select (mapel_id, tipe_soal, kategori)
2. `resources/views/admin/bank_soal/index.blade.php` — 3 filter select (guru_id, mapel_id, tipe_soal)
3. `resources/views/admin/users/index.blade.php` — 2 filter inputs (search, role select)
4. `resources/views/auth/login.blade.php` — 2 login inputs (email, password)
5. `resources/views/guru/ujian/show.blade.php` — 1 select (status filter)
6. `resources/views/guru/ujian/edit.blade.php` — 1 textarea (deskripsi)
7. `resources/views/guru/ujian/create.blade.php` — 1 textarea (deskripsi)
8. `resources/views/admin/guru_mapel/index.blade.php` — 1 select (guru search)
9. `resources/views/admin/mata_pelajaran/edit.blade.php` — 1 select (is_aktif)
10. `resources/views/admin/kelas/edit.blade.php` — 3 select (tingkat, tahun_ajaran_id, is_aktif)
11. `resources/views/admin/users/create.blade.php` — 1 password input
12. `resources/views/siswa/ujian/ruang.blade.php` — 1 textarea (jawaban essay)
13. `resources/views/siswa/dashboard.blade.php` — 1 token display input
14. `resources/views/profile/partials/delete-user-form.blade.php` — 1 password input
15. `resources/views/admin/users/index.blade.php` — 1 file input (photo upload)

**CSS Result**: 74.36 kB gzipped (dark variants sudah dalam safelist dari dark mode implementation sebelumnya)

**Test Results**:
- Login page: email & password inputs readable di dark mode ✓
- Filter forms: all select dropdowns terlihat jelas ✓
- Admin forms: edit forms untuk kelas, mata pelajaran, guru terlihat jelas ✓
- Exam room: siswa textarea untuk essay jawaban readable ✓
- Profile: delete form password input visible ✓
- Dashboard: token display readable di dark mode ✓
- File inputs: upload inputs terlihat dengan proper contrast ✓

**Build & Deploy**:
- `php artisan view:clear && php artisan view:cache` — Compiled views
- `npm run build` — Rebuilt CSS/JS assets
- Commit: "fix: filter text visibility in dark mode for all form elements"

---
### 🎬 Fix Dark Mode Transition Flash on Page Navigate (May 6, 2026)
**Problem**: Ketika navigate ke halaman baru, transisi dark mode terasa kasar (flash)
         Dark mode tidak smooth, ada lag saat switch antar halaman

**Root Cause**:
- Alpine.js initialization membutuhkan waktu untuk load dan run
- Sebelum Alpine.js ready, HTML element tidak punya class `dark`
- Browser render page dengan light mode CSS dulu (sesuai default HTML)
- Ketika Alpine.js initialize dan apply dark mode class → DOM rerender → **visual flash terjadi**
- Efeknya: light mode muncul sebentar, terus berubah ke dark mode

**Solution**:
- Tambahkan inline script di `<head>` yang instantly apply dark mode class
- Script execute **SEBELUM** Vite assets (Tailwind CSS) load
- Hasil: dark class sudah applied saat CSS render, tidak ada flash
- Pattern dari Tailwind docs untuk preventing flash

**Files Changed**:
- `resources/views/layouts/app.blade.php` — Add no-flash inline script di head (before Vite assets)

**Code Pattern**:
```html
<script>
  // Prevent dark mode flash: apply class sebelum CSS render
  if (localStorage.getItem('darkMode') === 'true') {
    document.documentElement.classList.add('dark');
  }
</script>
```

**Placement** (Critical):
- Script harus berada di `<head>` tag
- Script harus SEBELUM `@vite(['resources/css/app.css', ...])` tag
- Timing: execute sebelum CSS load → class applied saat styling applied

**Test Results**:
- Toggle dark mode → class instantly applied, no delay ✓
- Navigate antar halaman → smooth transition, no flash ✓
- Light mode → dark mode switch jelas dan smooth ✓
- Mobile navigate juga smooth tanpa lag ✓
- Page refresh dalam dark mode → no flash, stays dark ✓

**Build & Deploy**:
- `php artisan view:cache` — Compiled views
- `npm run build` — Rebuilt assets (74.36 kB gzipped, no CSS changes)
- Commit: "fix: smooth dark mode transition tanpa flash saat navigate"

---
### 👤 Fix Profile Form Input Text Visibility in Dark Mode (May 6, 2026)
**Problem**: Nama dan input fields di profile edit tidak terlihat saat dark mode
         (text invisible pada form inputs dan password fields)

**Root Cause**:
- Profile form inputs (name, email, password) missing dark mode color variants
- Same pattern as previous filter forms: no dark:border-gray-600, dark:bg-gray-700, dark:text-gray-100
- Affects: profile edit page, admin user edit page, password change form

**Solution**:
- Updated profile form inputs dengan dark mode variants
- Applied consistent pattern to all form elements in profile-related pages

**Files Changed** (3 total):
1. `resources/views/profile/partials/update-profile-information-form.blade.php`
   - Input nama (line 58)
   - Input email (line 68)

2. `resources/views/profile/partials/update-password-form.blade.php`
   - Input current_password (line 8)
   - Input password baru (line 15)
   - Input password confirmation (line 23)

3. `resources/views/admin/users/edit.blade.php`
   - Input nama (line 48)
   - Input no_telp (line 98)

**Pattern Applied** (Consistent):
```blade
class="... {{ $errors->has('field') ? 'border-red-400 bg-red-50' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}"
```

**Test Results**:
- Profile page dark mode → nama/email inputs readable ✓
- Password form dark mode → all password fields visible ✓
- Admin user edit dark mode → name and phone inputs visible ✓
- Light mode unaffected ✓
- Error states (red background) visible in both modes ✓

**Build & Deploy**:
- `php artisan view:cache` — Compiled views
- `npm run build` — Rebuilt assets (74.36 kB gzipped, no CSS changes)
- Commit: "fix: profile form input text visibility in dark mode"

---
### �🔄 Remove Essay Answer Key (May 6, 2026)
**Problem**: Essay soal dengan kunci jawaban otomatis kurang fleksibel, guru perlu nilai manual

**Solution**:
- Drop column `kunci_essay` dari table `bank_soal` via migration
- Remove `kunci_essay` dari:
  - Model `BankSoal.php` ($fillable array)
  - `TemplateSoalExport.php` (Excel export template)
  - `SoalImport.php` (Excel import processor)
  - `UpdateSoalRequest.php` (form validation)
  - View `guru/bank_soal/edit.blade.php` (replace textarea with info message)
  - View `guru/bank_soal/import.blade.php` (remove column description)

**Files Changed**:
- `database/migrations/2026_05_05_165630_drop_kunci_essay_from_bank_soal_table.php` — Drop column
- `app/Models/BankSoal.php` — Remove 'kunci_essay' dari fillable
- `app/Exports/TemplateSoalExport.php` — Remove dari headers & contoh data
- `app/Imports/SoalImport.php` — Remove dari create() call
- `app/Http/Requests/Guru/UpdateSoalRequest.php` — Remove validasi
- `resources/views/guru/bank_soal/edit.blade.php` — Replace textarea with info panel
- `resources/views/guru/bank_soal/import.blade.php` — Remove kunci_essay row

**Architecture Change**: Essay answers di table `jawaban_siswa` → guru grade manual via koreksi page

**Test**: 
- Excel import template no longer has kunci_essay column ✓
- Edit essay soal shows info message (no input) ✓
- Migration executed successfully ✓

---

### 🎨 UI Consistency & Layout Restructure (May 5, 2026)
**Problem**: Some pages (bank soal, ujian) had navbar but others didn't; inconsistent styling

**Solution**:
- Create shared `resources/views/layouts/app.blade.php` dengan navbar + footer
- Convert 20 view files dari standalone HTML ke `@extends('layouts.app')`
- Added sticky navbar, footer dengan brand info, min-height for main content

**Files Changed**:
- `resources/views/layouts/app.blade.php` — New main layout template
- 20 view files converted to use @extends('layouts.app')

**Result**: Consistent UI across all authenticated pages ✓

---

### 👤 Profile Features Enhancement
**Features Implemented**:

1. **Delete Account Restriction**
   - Only `super_admin` can delete accounts
   - Restriction in `ProfileController::destroy()` with 403 abort
   - Prevents guru/siswa from deleting own account

2. **Photo Upload**
   - Migration: Add `foto` column to `users` table
   - `ProfileController::update()` — guru/siswa upload own photo
   - `Admin/UserController::uploadFoto()` — admin upload photo untuk any user
   - Storage: `storage/app/public/fotos/` (symlinked to `public/storage`)
   - Validation: image, mimes:jpg,jpeg,png,webp, max:2048

3. **Photo Display in Navbar**
   - 3 locations: navbar button (32x32), dropdown panel (36x36), mobile header (40x40)
   - Fallback: Gradient circle dengan user initials jika tidak ada foto
   - Using `Storage::url()` untuk file access

**Files Changed**:
- `app/Models/User.php` — Add 'foto' to fillable
- `app/Http/Controllers/ProfileController.php` — restrict delete, handle upload
- `app/Http/Controllers/Admin/UserController.php` — new uploadFoto() method
- `app/Http/Requests/ProfileUpdateRequest.php` — foto validation rules
- `resources/views/layouts/navigation.blade.php` — display foto in 3 places

---

### 🗂️ Kategori & Sub-Kategori untuk Bank Soal
**Implementation**: 4 kategori × 12 sub-kategori system untuk pengorganisasian soal

**Categories**:
1. **Ujian Akhir Jenjang (AM)** — 3 sub-kategori
2. **Mid Semester** — 3 sub-kategori  
3. **Daily Assessment** — 3 sub-kategori
4. **Remedial/Enrichment** — 3 sub-kategori

**Files**:
- `app/Models/BankSoal.php` — Methods `daftarKategori()` dan `semuaSubKategori()`
- Controllers & Views — Use dropdowns untuk select kategori + sub_kategori

---

### 📊 Fitur yang Sudah Complete (7/7 Steps)

| Step | Fitur | Status | Notes |
|------|-------|--------|-------|
| 1 | Project Setup & Config | ✅ | Laravel 11, PostgreSQL, Tailwind, Alpine.js |
| 2 | Auth & User Management | ✅ | 3 roles (super_admin, guru, siswa) |
| 3 | Data Master | ✅ | Kelas, Mata Pelajaran, Tahun Ajaran |
| 4 | Bank Soal | ✅ | PG, B/S, Essay; Kategori; Excel import/export |
| 5 | Ujian Management | ✅ | Jadwal, Token, Acak Soal & Jawaban |
| 6 | Exam Room | ✅ | Timer, Anti-cheat, Auto-submit |
| 7 | Scoring & Reports | ✅ | Auto-score PG/BS, Manual essay, PDF/Excel export |

**Recent Enhancements**:
- ✅ Dark Mode support
- ✅ Profile photo upload & display
- ✅ Delete account restriction
- ✅ Manual essay grading (no auto-key)
- ✅ UI consistency across all pages

---

### 🚀 Command Reference untuk Development

```bash
# Blade compilation
php artisan view:clear && php artisan view:cache

# CSS/JS Build
npm run build          # Production build
npm run dev            # Development watch

# Database
php artisan migrate --force          # Run migrations
php artisan migrate:rollback --force # Rollback last migration

# Excel template download
GET /guru/bank-soal/template-excel   # Download template

# Git workflow
git add -A && git commit -m "..."
git push                             # Hanya push jika tidak ada issue
```

---

## 🔍 Maintenance Tips

**Ketika menambah feature baru:**
1. ✅ Update SKILL.md di section CHANGELOG
2. ✅ Dokumentasikan files yang diubah
3. ✅ Sertakan test steps untuk verifikasi
4. ✅ Update table "Fitur yang Sudah Complete" jika perlu
5. ✅ Commit dengan pesan deskriptif (format: type: description)

**Commit Message Format:**
```
feat: deskripsi fitur baru
fix: deskripsi bug fix
refactor: deskripsi perubahan kode
docs: update dokumentasi

Contoh:
feat: dark mode support untuk UI yang lebih nyaman di mata
fix: delete account restriction untuk guru dan siswa
```

---

*Terakhir diperbarui: May 6, 2026 — Profile Form Dark Mode Fix + Complete Dark Mode Coverage with Smooth Transitions*
