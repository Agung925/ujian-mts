<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class SiswaImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    /** Hitung baris yang berhasil diimport */
    public int $importedCount = 0;

    /** Hitung baris yang dilewati (NIS duplikat atau data tidak valid) */
    public int $skippedCount = 0;

    /**
     * Pemetaan setiap baris Excel ke Model User.
     * Kolom wajib: nis, name | Opsional: jenis_kelamin, no_telp
     */
    public function model(array $row)
    {
        // Lewati baris jika kolom wajib kosong
        if (empty($row['nis']) || empty($row['name'])) {
            $this->skippedCount++;
            return null;
        }

        // Lewati baris jika NIS sudah terdaftar
        if (User::query()->where('nis', trim($row['nis']))->exists()) {
            $this->skippedCount++;
            return null;
        }

        $this->importedCount++;

        // Password default = NIS (cukup string biasa, Model sudah ada cast 'hashed')
        return new User([
            'nis'           => trim($row['nis']),
            'name'          => trim($row['name']),
            'email'         => null,
            'password'      => $row['nis'], // cast 'hashed' di Model akan otomatis hash ini
            'role'          => 'siswa',
            'is_aktif'      => true,
            'jenis_kelamin' => isset($row['jenis_kelamin']) && in_array(strtoupper($row['jenis_kelamin']), ['L', 'P'])
                                ? strtoupper($row['jenis_kelamin'])
                                : null,
            'no_telp'       => $row['no_telp'] ?? null,
        ]);
    }
}
