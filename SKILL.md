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
| Step 6 | Ruang Ujian Siswa (Timer, Anti-Cheat, Auto-Submit) | ⬜ Belum |
| Step 7 | Nilai & Laporan (Auto-score, Export PDF/Excel) | ⬜ Belum |

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
bank_soal              → id, guru_id, mapel_id, pertanyaan, tipe_soal, gambar, tingkat_kesulitan
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

## 🎨 Panduan UI/UX

```
✅ Framework CSS    : Tailwind CSS
✅ Interaktivitas   : Alpine.js (x-data, x-show, x-on)
✅ Ikon             : Heroicons (sudah include di Tailwind)
✅ Warna Tema       : Hijau (#16a34a) sebagai primary (tema Islami MTs)
✅ Responsif        : Wajib mobile-friendly
✅ Bahasa UI        : Seluruhnya Bahasa Indonesia
✅ Notifikasi       : Gunakan session flash message
```

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

*Terakhir diperbarui: Step 4 — Bank Soal*
