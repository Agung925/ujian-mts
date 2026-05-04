<?php

namespace App\Imports;

use App\Models\BankSoal;
use App\Models\PilihanJawaban;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SoalImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    // Simpan hasil import untuk ditampilkan ke guru
    public int $berhasil   = 0;
    public int $gagal      = 0;
    public array $errorLog = [];

    public function __construct(
        private int $guruId,
        private int $mataPelajaranId
    ) {}

    /**
     * Proses setiap chunk data dari Excel.
     * Menggunakan ToCollection agar bisa validasi per baris secara manual.
     */
    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $nomorBaris = $index + 2; // +2 karena baris 1 = header

            // Konversi row ke array biasa dan trim spasi
            $data     = array_map('trim', $row->toArray());
            $tipeSoal = strtolower($data['tipe_soal'] ?? '');

            // Validasi dasar per baris
            $validator = Validator::make($data, [
                'tipe_soal'         => 'required|in:pg,bs,essay',
                'pertanyaan'        => 'required|string|min:5',
                'tingkat_kesulitan' => 'required|in:mudah,sedang,sulit',
                'bobot_nilai'       => 'required|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                $this->gagal++;
                $this->errorLog[] = "Baris {$nomorBaris}: " . implode(', ', $validator->errors()->all());
                continue;
            }

            // Validasi tambahan per tipe soal
            if ($tipeSoal === 'pg') {
                if (empty($data['pilihan_a']) || empty($data['pilihan_b'])) {
                    $this->gagal++;
                    $this->errorLog[] = "Baris {$nomorBaris}: Soal PG minimal harus punya Pilihan A dan B.";
                    continue;
                }
                $jawaban = strtoupper(trim($data['jawaban_benar'] ?? ''));
                if (!in_array($jawaban, ['A', 'B', 'C', 'D', 'E'])) {
                    $this->gagal++;
                    $this->errorLog[] = "Baris {$nomorBaris}: Jawaban benar PG harus A, B, C, D, atau E.";
                    continue;
                }
            }

            if ($tipeSoal === 'bs') {
                $jawaban = ucfirst(strtolower(trim($data['jawaban_benar'] ?? '')));
                if (!in_array($jawaban, ['Benar', 'Salah'])) {
                    $this->gagal++;
                    $this->errorLog[] = "Baris {$nomorBaris}: Jawaban benar BS harus 'Benar' atau 'Salah'.";
                    continue;
                }
            }

            try {
                // Simpan soal utama ke tabel bank_soal
                $soal = BankSoal::create([
                    'guru_id'           => $this->guruId,
                    'mata_pelajaran_id' => $this->mataPelajaranId,
                    'pertanyaan'        => $data['pertanyaan'],
                    'tipe_soal'         => $tipeSoal,
                    'tingkat_kesulitan' => $data['tingkat_kesulitan'],
                    'bobot_nilai'       => (int) $data['bobot_nilai'],
                    'kunci_essay'       => $data['kunci_essay'] ?? null,
                    'is_aktif'          => true,
                ]);

                // Simpan pilihan jawaban berdasarkan tipe soal
                if ($tipeSoal === 'pg') {
                    $this->simpanPilihanPG($soal, $data);
                }

                if ($tipeSoal === 'bs') {
                    $this->simpanPilihanBS($soal, $data);
                }

                // Essay tidak perlu pilihan jawaban

                $this->berhasil++;

            } catch (\Exception $e) {
                $this->gagal++;
                $this->errorLog[] = "Baris {$nomorBaris}: Gagal disimpan — " . $e->getMessage();
            }
        }
    }

    /**
     * Simpan pilihan jawaban untuk soal Pilihan Ganda
     */
    private function simpanPilihanPG(BankSoal $soal, array $data): void
    {
        $labelPilihan = ['A', 'B', 'C', 'D', 'E'];
        $kolomPilihan = ['pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'pilihan_e'];
        $jawabanBenar = strtoupper(trim($data['jawaban_benar']));

        foreach ($labelPilihan as $i => $label) {
            $teks = trim($data[$kolomPilihan[$i]] ?? '');

            // Skip jika pilihan kosong (misal tidak ada pilihan E)
            if (empty($teks)) continue;

            PilihanJawaban::create([
                'soal_id'      => $soal->id,
                'label'        => $label,
                'teks_pilihan' => $teks,
                'is_benar'     => ($label === $jawabanBenar),
            ]);
        }
    }

    /**
     * Simpan pilihan jawaban untuk soal Benar/Salah
     * Selalu buat 2 pilihan: Benar dan Salah
     */
    private function simpanPilihanBS(BankSoal $soal, array $data): void
    {
        $jawabanBenar = ucfirst(strtolower(trim($data['jawaban_benar'])));

        PilihanJawaban::create([
            'soal_id'      => $soal->id,
            'label'        => 'Benar',
            'teks_pilihan' => 'Benar',
            'is_benar'     => ($jawabanBenar === 'Benar'),
        ]);

        PilihanJawaban::create([
            'soal_id'      => $soal->id,
            'label'        => 'Salah',
            'teks_pilihan' => 'Salah',
            'is_benar'     => ($jawabanBenar === 'Salah'),
        ]);
    }

    /**
     * Baca file Excel per 200 baris agar tidak memory overflow untuk file besar
     */
    public function chunkSize(): int
    {
        return 200;
    }
}
