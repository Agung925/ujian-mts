# Step 6 — Ruang Ujian Siswa (Timer, Anti-Cheat, Auto-Submit)

**Status:** ⬜ Belum Dikerjakan

---

## Tujuan

Membangun ruang ujian yang aman bagi siswa: autentikasi via token, timer countdown, auto-submit saat waktu habis, penyimpanan jawaban real-time, dan deteksi keluar tab.

---

## Fitur yang Akan Dibangun

- Siswa masuk ruang ujian dengan memasukkan token (6 karakter)
- Sesi ujian per siswa dengan timer countdown
- Jawaban disimpan otomatis setiap berganti soal (AJAX)
- Auto-submit saat waktu habis (via JavaScript + fallback server-side)
- Deteksi tab/window blur (anti-cheat sederhana) — hitung berapa kali keluar
- Tombol "Selesai" dengan konfirmasi
- Soal ditampilkan 1 per halaman ATAU semua sekaligus (konfigurasi per ujian)
- Navigasi antar soal dengan indikator soal sudah dijawab / belum

---

## Rencana Tabel Database

### `sesi_ujian`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint | Primary key |
| `ujian_id` | FK → ujian | — |
| `siswa_id` | FK → users | — |
| `waktu_mulai` | timestamp | Saat siswa mulai |
| `waktu_selesai` | timestamp | Saat siswa submit / waktu habis |
| `status` | enum `berlangsung`,`selesai` | — |
| `nilai_akhir` | decimal(6,2) | Nilai final (dihitung saat submit) |
| `keluar_tab` | smallint | Hitung berapa kali pindah tab |
| `ip_address` | varchar(45) | IP siswa saat ujian |
| `created_at/updated_at` | timestamp | — |

### `jawaban_siswa`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint | Primary key |
| `sesi_id` | FK → sesi_ujian | — |
| `soal_id` | FK → bank_soal | — |
| `jawaban_id` | FK → pilihan_jawaban | Untuk PG/B/S (nullable) |
| `jawaban_essay` | text | Untuk soal Essay (nullable) |
| `is_benar` | boolean | Otomatis diisi saat submit (nullable untuk essay) |
| `nilai` | decimal(5,2) | Poin untuk soal ini |
| `created_at/updated_at` | timestamp | — |

---

## Rencana Route

```
# Siswa
GET  /siswa/ujian                       siswa.ujian.index     ← Daftar ujian tersedia
POST /siswa/ujian/masuk                 siswa.ujian.masuk     ← Input token → mulai sesi
GET  /siswa/ujian/{sesi}/ruang          siswa.ujian.ruang     ← Ruang ujian
POST /siswa/ujian/{sesi}/jawab          siswa.ujian.jawab     ← Simpan jawaban (AJAX)
POST /siswa/ujian/{sesi}/submit         siswa.ujian.submit    ← Kumpulkan ujian
GET  /siswa/ujian/{sesi}/hasil          siswa.ujian.hasil     ← Lihat hasil setelah submit
```

---

## Rencana File yang Akan Dibuat

```
database/migrations/
├── create_sesi_ujian_table.php
└── create_jawaban_siswa_table.php

app/Models/
├── SesiUjian.php
└── JawabanSiswa.php

app/Http/Controllers/Siswa/
└── UjianController.php

app/Services/
└── PenilaianService.php        ← Logika hitung nilai dipisah ke Service

resources/views/siswa/ujian/
├── index.blade.php             ← Daftar ujian + form input token
├── ruang.blade.php             ← Halaman ujian dengan timer
└── hasil.blade.php             ← Tampilan nilai setelah selesai
```

---

## Alur Sesi Ujian

```
Siswa buka /siswa/ujian
    ↓
Input token 6 karakter
    ↓ Validasi:
    ├── Token valid?
    ├── Jadwal masih berlaku?
    ├── Siswa terdaftar di kelas ujian?
    └── Belum punya sesi aktif untuk ujian ini?
    ↓ Buat record sesi_ujian (status: berlangsung)
    ↓
Ruang ujian ditampilkan
    ├── Timer countdown (waktu_mulai + durasi - sekarang)
    ├── Daftar soal (acak jika dikonfigurasi)
    └── Setiap jawaban → POST /siswa/ujian/{sesi}/jawab (AJAX)
    ↓
Submit (manual atau auto saat timer = 0)
    ↓ PenilaianService::hitung($sesi)
    ↓ Update sesi_ujian (status: selesai, nilai_akhir, waktu_selesai)
    ↓ Redirect ke halaman hasil
```

---

## Catatan Implementasi

### Timer Auto-Submit
```javascript
// Alpine.js timer — kirim form saat countdown habis
x-data="{ 
    sisa: {{ $sisaDetik }},
    init() {
        const interval = setInterval(() => {
            this.sisa--;
            if (this.sisa <= 0) {
                clearInterval(interval);
                document.getElementById('form-submit').submit();
            }
        }, 1000);
    }
}"
```

### Anti-Cheat Tab Blur
```javascript
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        fetch('/siswa/ujian/{{ $sesi->id }}/blur', { method: 'POST', ... });
    }
});
```

### Simpan Jawaban AJAX
- Jawaban disimpan ke `jawaban_siswa` via `POST` setiap klik pilihan
- Jika record sudah ada → `updateOrCreate` (bukan duplikat)
- Server validasi: sesi harus `berlangsung`, waktu belum habis

### PenilaianService
```php
// app/Services/PenilaianService.php
public function hitung(SesiUjian $sesi): float
{
    // Loop semua jawaban_siswa
    // Untuk PG/BS: bandingkan jawaban_id dengan pilihan_jawaban.is_benar
    // Untuk Essay: nilai = 0 dulu (perlu dinilai manual oleh guru)
    // Jumlahkan poin berdasarkan bobot di ujian_soal
}
```

---

## Urutan Pengerjaan yang Disarankan

1. Migration `sesi_ujian` → `jawaban_siswa`
2. Model dengan relasi
3. `PenilaianService` (logika hitung nilai)
4. `UjianController` (Siswa) — masuk, ruang, jawab, submit
5. View `ruang.blade.php` dengan timer Alpine.js
6. Test end-to-end: login siswa → input token → kerjakan → submit → lihat nilai
