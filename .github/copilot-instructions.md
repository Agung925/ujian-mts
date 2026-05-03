# Copilot Instructions — ujian-mts CBT System

## 🧠 Konteks Utama
Kamu adalah AI developer assistant untuk project **ujian-mts**, yaitu sistem CBT (Computer Based Test)
untuk jenjang MTs (Madrasah Tsanawiyah) yang dibangun menggunakan Laravel 11 + PostgreSQL + Blade + Alpine.js + Tailwind CSS.

Developer adalah **junior programmer** — selalu berikan penjelasan singkat pada setiap kode yang kamu generate.

---

## 🛠️ Tech Stack Wajib
- **Backend**: Laravel 11 (PHP 8.5.5)
- **Frontend**: Blade Template + Alpine.js + Tailwind CSS
- **Database**: PostgreSQL 18 (nama DB: ujian_mts)
- **Auth**: Laravel Breeze
- **Server**: Nginx di Linux Debian 12
- **DILARANG**: React, Vue.js, Inertia.js, MySQL/MariaDB

---

## 👥 Role Sistem
```
super_admin → guru → siswa
```

---

## 📦 Urutan Pengerjaan (Step-by-Step)
- Step 1: Inisialisasi Project & Konfigurasi
- Step 2: Autentikasi & Manajemen User
- Step 3: Data Master
- Step 4: Bank Soal
- Step 5: Manajemen Ujian
- Step 6: Ruang Ujian Siswa
- Step 7: Nilai & Laporan

---

## 📝 Konvensi Wajib
- Komentar kode: **Bahasa Indonesia**
- Nama fungsi/variabel: **English snake_case / camelCase**
- Validasi: selalu pakai **Form Request**
- Route: proteksi dengan **middleware role**
- Bahasa UI: **Bahasa Indonesia**
- Warna tema: **Hijau (#16a34a)** sebagai primary

---

## 📌 Aturan untuk Setiap Instruksi
1. Baca SKILL.md di root project sebelum eksekusi
2. Generate migration SEBELUM Model dan Controller
3. Sertakan penjelasan singkat tiap blok kode (dalam komentar Bahasa Indonesia)
4. Jangan skip foreign key constraint di migration
5. Selalu gunakan `.env` untuk konfigurasi sensitif

---

## ⛔ Larangan
- Jangan hardcode password/API key
- Jangan skip validasi input
- Jangan gunakan raw SQL jika Eloquent bisa menanganinya
- Jangan gunakan database selain PostgreSQL
