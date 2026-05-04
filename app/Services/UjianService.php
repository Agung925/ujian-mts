<?php

namespace App\Services;

use App\Models\BankSoal;
use App\Models\Ujian;
use App\Models\UjianSoal;
use App\Models\SesiUjian;
use App\Models\JawabanSiswa;
use Illuminate\Support\Facades\DB;

class UjianService
{
    /**
     * Tambahkan soal ke ujian secara manual (guru pilih satu per satu)
     */
    public function tambahSoalManual(Ujian $ujian, array $soalIds): void
    {
        // Mulai nomor urut dari setelah soal yang sudah ada
        $nomorUrut = $ujian->soal()->count() + 1;

        foreach ($soalIds as $soalId) {
            $soal = BankSoal::find($soalId);
            if (!$soal) continue;

            UjianSoal::updateOrCreate(
                ['ujian_id' => $ujian->id, 'soal_id' => $soalId],
                ['nomor_urut' => $nomorUrut++, 'bobot_nilai' => $soal->bobot_nilai]
            );
        }
    }

    /**
     * Tambahkan soal ke ujian secara random dari bank soal guru
     * Ambil $jumlah soal secara acak dari mapel yang sama
     */
    public function tambahSoalRandom(Ujian $ujian, int $jumlah): int
    {
        // Ambil soal random dari bank soal guru dengan mapel yang sama
        $soalRandom = BankSoal::milikGuru($ujian->guru_id)
                               ->mapel($ujian->mata_pelajaran_id)
                               ->aktif()
                               ->inRandomOrder()
                               ->limit($jumlah)
                               ->get();

        if ($soalRandom->isEmpty()) {
            return 0;
        }

        // Hapus soal lama, ganti dengan yang baru
        $ujian->soal()->detach();

        foreach ($soalRandom as $index => $soal) {
            UjianSoal::create([
                'ujian_id'    => $ujian->id,
                'soal_id'     => $soal->id,
                'nomor_urut'  => $index + 1,
                'bobot_nilai' => $soal->bobot_nilai,
            ]);
        }

        return $soalRandom->count();
    }

    /**
     * Buka ujian — ubah status dari draft ke aktif
     * Catat waktu pembukaan
     */
    public function bukaUjian(Ujian $ujian): void
    {
        // Validasi: ujian harus punya soal sebelum dibuka
        if ($ujian->soal()->count() === 0) {
            throw new \Exception('Ujian tidak bisa dibuka karena belum ada soal.');
        }

        $ujian->update([
            'status'      => 'aktif',
            'dibuka_pada' => now(),
        ]);
    }

    /**
     * Tutup ujian — ubah status dari aktif ke selesai
     * Auto-submit semua sesi yang masih berlangsung
     */
    public function tutupUjian(Ujian $ujian): void
    {
        // Auto-submit semua siswa yang belum selesai
        $sesiAktif = $ujian->sesiUjian()
                           ->where('status', 'sedang')
                           ->get();

        foreach ($sesiAktif as $sesi) {
            $this->submitJawaban($sesi);
        }

        $ujian->update([
            'status'       => 'selesai',
            'ditutup_pada' => now(),
        ]);
    }

    /**
     * Hitung dan simpan nilai akhir siswa
     * Dipanggil saat siswa submit atau auto-submit saat ujian ditutup
     */
    public function submitJawaban(SesiUjian $sesi): void
    {
        DB::transaction(function () use ($sesi) {
            $totalNilai    = 0;
            $totalMaksimal = 0;

            $jawabanList = $sesi->jawabanSiswa()->with(['soal', 'pilihan'])->get();

            foreach ($jawabanList as $jawaban) {
                $soal          = $jawaban->soal;
                $bobotSoal     = UjianSoal::where('ujian_id', $sesi->ujian_id)
                                          ->where('soal_id', $soal->id)
                                          ->value('bobot_nilai') ?? $soal->bobot_nilai;
                $totalMaksimal += $bobotSoal;

                if ($soal->tipe_soal !== 'essay') {
                    // Auto-koreksi PG dan BS berdasarkan is_benar di pilihan_jawaban
                    $isBenar = $jawaban->pilihan?->is_benar ?? false;
                    $nilai   = $isBenar ? $bobotSoal : 0;

                    $jawaban->update([
                        'is_benar' => $isBenar,
                        'nilai'    => $nilai,
                    ]);

                    $totalNilai += $nilai;
                }
                // Essay dikoreksi manual oleh guru di Step 7
            }

            // Hitung nilai akhir dalam skala 0-100
            $nilaiAkhir = $totalMaksimal > 0
                ? round(($totalNilai / $totalMaksimal) * 100, 2)
                : 0;

            $sesi->update([
                'status'        => 'selesai',
                'waktu_selesai' => now(),
                'nilai_akhir'   => $nilaiAkhir,
            ]);
        });
    }

    /**
     * Buat sesi ujian untuk siswa saat masuk ruang ujian dengan token
     * Generate urutan soal yang diacak khusus untuk siswa ini
     */
    public function buatSesiSiswa(Ujian $ujian, int $siswaId): SesiUjian
    {
        // Ambil semua ID soal di ujian ini
        $soalIds = $ujian->soal()->pluck('bank_soal.id')->toArray();

        // Acak urutan soal jika fitur acak soal aktif
        if ($ujian->acak_soal) {
            shuffle($soalIds);
        }

        return SesiUjian::create([
            'ujian_id'    => $ujian->id,
            'siswa_id'    => $siswaId,
            'status'      => 'sedang',
            'waktu_mulai' => now(),
            'urutan_soal' => $soalIds,
        ]);
    }
}
