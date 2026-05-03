# Step 3 — Data Master (Kelas, Mata Pelajaran, Tahun Ajaran)

**Status:** ✅ Selesai (3 Mei 2026)

---

## Tujuan

Membangun modul data master sebagai fondasi pembuatan ujian: manajemen kelas, mata pelajaran, tahun ajaran, serta relasi guru-mapel dan siswa-kelas.

---

## Fitur yang Akan Dibangun

- CRUD Tahun Ajaran (bisa set mana yang aktif)
- CRUD Kelas (VII-A, VIII-B, dst — terikat ke tahun ajaran)
- CRUD Mata Pelajaran (kode + nama + jenis)
- Penugasan guru ke mata pelajaran (guru_mapel)
- Penugasan siswa ke kelas (siswa_kelas)

---

## Rencana Tabel Database

### `tahun_ajaran`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint | Primary key |
| `nama` | varchar(20) | Contoh: `2025/2026` |
| `semester` | enum `1`,`2` | Semester ganjil/genap |
| `is_aktif` | boolean | Hanya 1 yang aktif |
| `created_at/updated_at` | timestamp | — |

### `mata_pelajaran`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint | Primary key |
| `kode_mapel` | varchar(10) | Contoh: `MTK`, `BIN` |
| `nama_mapel` | varchar(100) | Nama lengkap |
| `jenis` | enum `umum`,`keagamaan` | Jenis mapel |
| `created_at/updated_at` | timestamp | — |

### `kelas`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint | Primary key |
| `tahun_ajaran_id` | FK → tahun_ajaran | — |
| `nama_kelas` | varchar(20) | Contoh: `VII-A` |
| `tingkat` | enum `VII`,`VIII`,`IX` | Tingkat kelas |
| `created_at/updated_at` | timestamp | — |

### `guru_mapel` (pivot)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint | Primary key |
| `user_id` | FK → users | Harus role `guru` |
| `mata_pelajaran_id` | FK → mata_pelajaran | — |
| `created_at/updated_at` | timestamp | — |

### `siswa_kelas` (pivot)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint | Primary key |
| `user_id` | FK → users | Harus role `siswa` |
| `kelas_id` | FK → kelas | — |
| `tahun_ajaran_id` | FK → tahun_ajaran | — |
| `created_at/updated_at` | timestamp | — |

---

## Rencana Route

```
# Tahun Ajaran (admin)
GET    /admin/tahun-ajaran              admin.tahun-ajaran.index
GET    /admin/tahun-ajaran/create       admin.tahun-ajaran.create
POST   /admin/tahun-ajaran              admin.tahun-ajaran.store
GET    /admin/tahun-ajaran/{id}/edit    admin.tahun-ajaran.edit
PUT    /admin/tahun-ajaran/{id}         admin.tahun-ajaran.update
DELETE /admin/tahun-ajaran/{id}         admin.tahun-ajaran.destroy
POST   /admin/tahun-ajaran/{id}/aktif   admin.tahun-ajaran.aktif

# Mata Pelajaran (admin)
Resource /admin/mata-pelajaran          admin.mata-pelajaran.*

# Kelas (admin)
Resource /admin/kelas                   admin.kelas.*
POST   /admin/kelas/{id}/assign-siswa   admin.kelas.assign-siswa

# Guru Mapel (admin)
POST   /admin/guru-mapel                admin.guru-mapel.store
DELETE /admin/guru-mapel/{id}           admin.guru-mapel.destroy
```

---

## Rencana File yang Akan Dibuat

```
database/migrations/
├── create_tahun_ajaran_table.php
├── create_mata_pelajaran_table.php
├── create_kelas_table.php
├── create_guru_mapel_table.php
└── create_siswa_kelas_table.php

app/Models/
├── TahunAjaran.php
├── MataPelajaran.php
├── Kelas.php
├── GuruMapel.php
└── SiswaKelas.php

app/Http/Controllers/Admin/
├── TahunAjaranController.php
├── MataPelajaranController.php
├── KelasController.php
└── GuruMapelController.php

app/Http/Requests/Admin/
├── StoreTahunAjaranRequest.php
├── StoreMataPelajaranRequest.php
├── StoreKelasRequest.php
└── (dst.)

resources/views/admin/
├── tahun_ajaran/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── mata_pelajaran/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── kelas/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

---

## Catatan Implementasi

- Saat menghapus Tahun Ajaran yang masih terikat ke Kelas → tolak dengan pesan error
- Hanya boleh **1 Tahun Ajaran aktif** — saat mengaktifkan satu, nonaktifkan semua yang lain
- `guru_mapel` bisa di-manage lewat halaman detail guru
- Import massal siswa ke kelas bisa via Excel (kolom NIS + nama kelas)

---

## Urutan Pengerjaan yang Disarankan

1. Migration semua tabel (urutan: tahun_ajaran → mata_pelajaran → kelas → guru_mapel → siswa_kelas)
2. Semua Model dengan relasi Eloquent
3. Controller + Form Request (mulai dari TahunAjaran)
4. Daftarkan route di `web.php`
5. Views (gunakan komponen Blade yang konsisten)
6. Seeder data awal (beberapa tahun ajaran + mapel umum MTs)
