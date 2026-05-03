# Step 7 — Nilai & Laporan (Auto-score, Export PDF/Excel)

**Status:** ⬜ Belum Dikerjakan

---

## Tujuan

Menampilkan rekap nilai per ujian, nilai per siswa, penilaian manual soal essay oleh guru, dan export laporan ke PDF maupun Excel.

---

## Fitur yang Akan Dibangun

- Rekap nilai semua peserta per ujian (guru)
- Penilaian manual soal essay oleh guru
- Nilai per siswa: riwayat semua ujian yang diikuti
- Export daftar nilai ke Excel (.xlsx) per ujian
- Export laporan nilai siswa ke PDF
- Statistik sederhana: nilai tertinggi, terendah, rata-rata, jumlah lulus

---

## Rencana Route

```
# Guru — Laporan Ujian
GET  /guru/laporan/ujian/{ujian}            guru.laporan.ujian        ← Rekap nilai
GET  /guru/laporan/ujian/{ujian}/export-excel   guru.laporan.export-excel
GET  /guru/laporan/ujian/{ujian}/export-pdf     guru.laporan.export-pdf
GET  /guru/laporan/sesi/{sesi}/nilai-essay      guru.laporan.nilai-essay  ← Form nilai essay
POST /guru/laporan/sesi/{sesi}/nilai-essay      guru.laporan.simpan-essay

# Siswa — Riwayat Nilai
GET  /siswa/nilai                           siswa.nilai.index
GET  /siswa/nilai/{sesi}                    siswa.nilai.detail        ← Detail per ujian

# Admin — Laporan Global
GET  /admin/laporan                         admin.laporan.index
```

---

## Rencana File yang Akan Dibuat

```
app/Http/Controllers/Guru/
└── LaporanController.php

app/Http/Controllers/Siswa/
└── NilaiController.php

app/Http/Controllers/Admin/
└── LaporanController.php

app/Exports/
├── NilaiUjianExport.php         ← Export Excel daftar nilai per ujian
└── RaporSiswaExport.php         ← Export Excel rekap nilai semua ujian

app/Http/View/
└── NilaiUjianPdf.php            ← Data untuk view PDF (opsional, bisa langsung di controller)

resources/views/guru/laporan/
├── ujian.blade.php              ← Tabel rekap nilai + tombol export
├── nilai_essay.blade.php        ← Form penilaian essay
└── pdf/nilai_ujian.blade.php    ← Template PDF (tanpa Tailwind, CSS inline)

resources/views/siswa/nilai/
├── index.blade.php              ← Riwayat ujian + nilai
└── detail.blade.php             ← Detail soal + jawaban + nilai per soal
```

---

## Rencana Perubahan Tabel

Tidak ada tabel baru. Step ini hanya membaca tabel yang sudah ada:
- `ujian`
- `sesi_ujian`
- `jawaban_siswa`
- `bank_soal`
- `pilihan_jawaban`
- `ujian_soal`

Satu kolom yang mungkin perlu ditambahkan ke `jawaban_siswa`:

```php
// Jika soal essay dinilai manual
$table->decimal('nilai_essay', 5, 2)->nullable(); // nilai yang diberikan guru
$table->text('catatan_guru')->nullable();          // komentar/feedback guru
```

---

## Contoh Logika Export Excel

```php
// app/Exports/NilaiUjianExport.php
class NilaiUjianExport implements FromCollection, WithHeadings, WithStyles
{
    public function __construct(private Ujian $ujian) {}

    public function collection()
    {
        return $this->ujian->sesiUjian()
            ->with('siswa')
            ->get()
            ->map(fn($sesi) => [
                'Nama'          => $sesi->siswa->name,
                'NIS'           => $sesi->siswa->nis,
                'Nilai'         => $sesi->nilai_akhir,
                'Waktu Mulai'   => $sesi->waktu_mulai,
                'Waktu Selesai' => $sesi->waktu_selesai,
                'Status'        => $sesi->status,
            ]);
    }
}
```

---

## Template PDF

PDF di-generate via `barryvdh/laravel-dompdf`. View PDF menggunakan **CSS inline** (bukan Tailwind) karena dompdf tidak mendukung utility classes.

```php
// Controller
$pdf = Pdf::loadView('guru.laporan.pdf.nilai_ujian', ['ujian' => $ujian]);
return $pdf->download("nilai_{$ujian->judul}.pdf");
```

---

## Statistik yang Ditampilkan

| Metrik | Cara Hitung |
|--------|-------------|
| Nilai tertinggi | `max(nilai_akhir)` |
| Nilai terendah | `min(nilai_akhir)` |
| Nilai rata-rata | `avg(nilai_akhir)` |
| Jumlah lulus | `count WHERE nilai_akhir >= KKM` |
| Persentase lulus | `(lulus / total_peserta) * 100` |

KKM (Kriteria Ketuntasan Minimal) bisa dikonfigurasi per ujian atau global di `.env`.

---

## Catatan Implementasi

- Soal essay yang belum dinilai guru ditandai `nilai = null` → tampilkan label "Belum Dinilai"
- Nilai akhir dihitung ulang saat guru mengisi nilai essay: `PenilaianService::hitungUlang($sesi)`
- Export PDF: pastikan font mendukung karakter Indonesia (gunakan font DejaVu atau set charset UTF-8)
- Untuk rekap besar (>500 siswa), gunakan `Queue` agar tidak timeout

---

## Urutan Pengerjaan yang Disarankan

1. `LaporanController` (Guru) — tampilkan rekap nilai
2. Form penilaian essay + update `jawaban_siswa`
3. `NilaiUjianExport` — export Excel
4. View PDF + `Pdf::loadView()` — export PDF
5. `NilaiController` (Siswa) — riwayat nilai + detail
6. Dashboard admin dengan statistik global
