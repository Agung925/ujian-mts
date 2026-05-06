# ujian-mts — Sistem CBT MTs

Sistem Computer Based Test (CBT) untuk jenjang **MTs (Madrasah Tsanawiyah)**.

## Tech Stack
- **Backend**: Laravel 11 (PHP 8.5.5)
- **Frontend**: Blade + Alpine.js + Tailwind CSS
- **Database**: PostgreSQL 18
- **Auth**: Laravel Breeze
- **Server**: Nginx 1.22.1, Debian 12

## Role Pengguna
| Role | Akses |
|------|-------|
| `super_admin` | Kelola semua data, user, konfigurasi |
| `guru` | Kelola soal, ujian, lihat nilai |
| `siswa` | Ikuti ujian |

## Progress Pengerjaan
| Step | Modul | Status |
|------|-------|--------|
| Step 1 | Inisialisasi Project & Konfigurasi | ✅ Selesai |
| Step 2 | Autentikasi & Manajemen User | ✅ Selesai |
| Step 3 | Data Master (Kelas, Mapel, Tahun Ajaran) | ✅ Selesai |
| Step 4 | Bank Soal (PG, B/S, Essay + Gambar + Import Excel) | ✅ Selesai |
| Step 5 | Manajemen Ujian (Jadwal, Token, Acak Soal) | ✅ Selesai |
| Step 6 | Ruang Ujian Siswa (Timer, Anti-Cheat, Auto-Submit) | ✅ Selesai |
| Step 7 | Nilai & Laporan (Auto-score, Koreksi Essay, Export PDF/Excel) | ✅ Selesai |

## Fitur Tambahan
| Fitur | Keterangan |
|-------|------------|
| 🌙 Dark Mode | Toggle di navbar, localStorage persistence, no-flash |
| 👤 Profile Photo | Upload & display foto profil di navbar |
| 🏫 Branding Sekolah | Logo & nama MTs Al-Hidayah Tamansari di navbar + footer |
| 📂 Kategori Soal | Kategori & sub-kategori untuk bank soal |
| 📊 Excel Import/Export | Import soal massal & export template Excel |
| 🔒 Anti-Cheat | Deteksi pindah tab via Page Visibility API |
| 🎨 UI Konsisten | Semua halaman menggunakan layout navbar + footer yang sama |

## Setup Lokal
```bash
cp .env.example .env
# Isi DB_HOST=127.0.0.1, DB_DATABASE=ujian_mts, DB_USERNAME=postgres

composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install && npm run build
```

## Akun Default (Seeder)
| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@ujian-mts.sch.id | password |
