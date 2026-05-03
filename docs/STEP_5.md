# Step 5 — Manajemen Ujian (Jadwal, Token, Acak Soal)

**Status:** ⬜ Belum Dikerjakan

---

## Tujuan

Guru dapat membuat paket ujian: memilih soal dari bank soal, mengatur jadwal, durasi, token keamanan, dan opsi pengacakan soal/jawaban.

---

## Fitur yang Akan Dibangun

- Guru buat ujian: pilih mapel, kelas, jadwal mulai/selesai, durasi
- Pilih soal dari bank soal milik sendiri
- Token akses ujian (6 karakter, bisa di-regenerate)
- Opsi acak urutan soal & acak pilihan jawaban
- Status ujian: `draft` → `aktif` → `selesai`
- Super admin bisa lihat semua ujian

---

## Rencana Tabel Database

### `ujian`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint | Primary key |
| `judul` | varchar(200) | Judul ujian |
| `guru_id` | FK → users | Pembuat ujian |
| `mata_pelajaran_id` | FK → mata_pelajaran | — |
| `kelas_id` | FK → kelas | Target kelas |
| `tahun_ajaran_id` | FK → tahun_ajaran | — |
| `durasi_menit` | smallint | Durasi (menit) |
| `token` | varchar(10) | Token masuk ujian |
| `jadwal_mulai` | timestamp | Waktu mulai |
| `jadwal_selesai` | timestamp | Waktu selesai |
| `acak_soal` | boolean | Acak urutan soal |
| `acak_jawaban` | boolean | Acak pilihan jawaban |
| `status` | enum `draft`,`aktif`,`selesai` | Status ujian |
| `created_at/updated_at` | timestamp | — |

### `ujian_soal` (pivot + metadata)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint | Primary key |
| `ujian_id` | FK → ujian | — |
| `soal_id` | FK → bank_soal | — |
| `nomor_urut` | smallint | Urutan soal dalam ujian |
| `bobot_nilai` | decimal(5,2) | Bobot poin soal ini |
| `created_at/updated_at` | timestamp | — |

---

## Rencana Route

```
# Guru
GET    /guru/ujian              guru.ujian.index
GET    /guru/ujian/create       guru.ujian.create
POST   /guru/ujian              guru.ujian.store
GET    /guru/ujian/{id}         guru.ujian.show       ← Detail + daftar peserta
GET    /guru/ujian/{id}/edit    guru.ujian.edit
PUT    /guru/ujian/{id}         guru.ujian.update
DELETE /guru/ujian/{id}         guru.ujian.destroy
POST   /guru/ujian/{id}/aktif   guru.ujian.aktif      ← Ubah status ke aktif
POST   /guru/ujian/{id}/token   guru.ujian.token      ← Regenerate token
```

---

## Rencana File yang Akan Dibuat

```
database/migrations/
├── create_ujian_table.php
└── create_ujian_soal_table.php

app/Models/
├── Ujian.php
└── UjianSoal.php

app/Http/Controllers/Guru/
└── UjianController.php

app/Http/Requests/Guru/
└── StoreUjianRequest.php

resources/views/guru/ujian/
├── index.blade.php
├── create.blade.php    ← Wizard: info ujian → pilih soal
├── edit.blade.php
└── show.blade.php      ← Detail: token, daftar soal, monitoring peserta
```

---

## Catatan Implementasi

- Token di-generate dengan `Str::upper(Str::random(6))`
- Ujian yang sudah `aktif` tidak bisa diedit, hanya bisa regenerate token
- Ujian otomatis jadi `selesai` saat `jadwal_selesai` terlewati (bisa via scheduled command)
- `bobot_nilai` default otomatis = `100 / jumlah_soal` (bisa di-override)
- Form pilih soal menggunakan AJAX / live search Alpine.js untuk filter soal

---

## Urutan Pengerjaan yang Disarankan

1. Migration `ujian` → `ujian_soal`
2. Model dengan relasi
3. `UjianController` + Request
4. Route + view
5. Fitur regenerate token
6. Scheduled command untuk auto-close ujian yang sudah lewat jadwal
