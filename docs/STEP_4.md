# Step 4 — Bank Soal (PG, B/S, Essay + Gambar)

**Status:** ⬜ Belum Dikerjakan

---

## Tujuan

Membangun modul bank soal yang dapat dikelola guru: tambah soal pilihan ganda, benar/salah, essay, lengkap dengan opsi upload gambar dan pengelompokan per mata pelajaran / tingkat kesulitan.

---

## Fitur yang Akan Dibangun

- Guru bisa tambah / edit / hapus soal miliknya sendiri
- Super admin bisa lihat dan kelola semua soal
- 3 tipe soal: Pilihan Ganda (PG), Benar/Salah (B/S), Essay
- Upload gambar opsional untuk tiap soal
- Filter soal berdasarkan: mapel, tipe soal, tingkat kesulitan
- Preview soal sebelum dimasukkan ke ujian

---

## Rencana Tabel Database

### `bank_soal`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint | Primary key |
| `guru_id` | FK → users | Pembuat soal (role guru) |
| `mata_pelajaran_id` | FK → mata_pelajaran | Mapel soal |
| `pertanyaan` | text | Teks soal (bisa panjang) |
| `tipe_soal` | enum `pg`,`bs`,`essay` | Jenis soal |
| `gambar` | varchar(255) | Path gambar (nullable) |
| `tingkat_kesulitan` | enum `mudah`,`sedang`,`sulit` | — |
| `pembahasan` | text | Pembahasan jawaban (nullable) |
| `created_at/updated_at` | timestamp | — |

### `pilihan_jawaban`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint | Primary key |
| `soal_id` | FK → bank_soal | — |
| `teks_pilihan` | text | Teks pilihan jawaban |
| `is_benar` | boolean | Apakah ini jawaban benar |
| `urutan` | tinyint | Urutan tampil (A, B, C, D) |
| `created_at/updated_at` | timestamp | — |

> Untuk soal tipe `essay`, tidak ada `pilihan_jawaban` — penilaian dilakukan manual oleh guru.  
> Untuk soal tipe `bs`, hanya 2 pilihan: "Benar" dan "Salah".

---

## Rencana Route

```
# Guru & Admin
GET    /guru/bank-soal              guru.bank-soal.index
GET    /guru/bank-soal/create       guru.bank-soal.create
POST   /guru/bank-soal              guru.bank-soal.store
GET    /guru/bank-soal/{id}/edit    guru.bank-soal.edit
PUT    /guru/bank-soal/{id}         guru.bank-soal.update
DELETE /guru/bank-soal/{id}         guru.bank-soal.destroy
```

---

## Rencana File yang Akan Dibuat

```
database/migrations/
├── create_bank_soal_table.php
└── create_pilihan_jawaban_table.php

app/Models/
├── BankSoal.php
└── PilihanJawaban.php

app/Http/Controllers/Guru/
└── BankSoalController.php

app/Http/Requests/Guru/
└── StoreSoalRequest.php

resources/views/guru/bank_soal/
├── index.blade.php
├── create.blade.php
└── edit.blade.php

storage/app/public/soal-gambar/   ← Gambar soal disimpan di sini
```

---

## Catatan Implementasi

- Jalankan `php artisan storage:link` agar file di `storage/app/public/` bisa diakses via URL
- Upload gambar: validasi `mimes:jpg,jpeg,png,gif|max:2048`
- Saat menghapus soal yang sudah masuk `ujian_soal` → tampilkan peringatan / tolak
- Form tambah soal PG harus dinamis (Alpine.js) — bisa tambah / hapus pilihan jawaban
- Minimal 2 pilihan untuk PG, tepat 2 untuk B/S
- Hanya boleh 1 jawaban yang `is_benar = true` untuk PG dan B/S

---

## Urutan Pengerjaan yang Disarankan

1. Migration `bank_soal` → `pilihan_jawaban`
2. Model `BankSoal` + `PilihanJawaban` dengan relasi `hasMany` / `belongsTo`
3. `storage:link` + konfigurasi disk
4. `BankSoalController` + `StoreSoalRequest`
5. View form dengan Alpine.js (tambah pilihan dinamis)
6. View index dengan filter
