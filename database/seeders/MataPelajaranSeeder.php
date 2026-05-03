<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        // ===================================
        // Mata Pelajaran Umum (Standar MTs)
        // ===================================
        $mapelUmum = [
            ['kode_mapel' => 'MTK',  'nama_mapel' => 'Matematika'],
            ['kode_mapel' => 'BIN',  'nama_mapel' => 'Bahasa Indonesia'],
            ['kode_mapel' => 'ENG',  'nama_mapel' => 'Bahasa Inggris'],
            ['kode_mapel' => 'IPA',  'nama_mapel' => 'Ilmu Pengetahuan Alam'],
            ['kode_mapel' => 'IPS',  'nama_mapel' => 'Ilmu Pengetahuan Sosial'],
            ['kode_mapel' => 'PKN',  'nama_mapel' => 'PPKn'],
            ['kode_mapel' => 'PJOK', 'nama_mapel' => 'Pendidikan Jasmani, Olahraga dan Kesehatan'],
            ['kode_mapel' => 'SBD',  'nama_mapel' => 'Seni Budaya'],
            ['kode_mapel' => 'PRK',  'nama_mapel' => 'Prakarya'],
            ['kode_mapel' => 'INF',  'nama_mapel' => 'Informatika'],
        ];

        // ===================================
        // Mata Pelajaran Keagamaan Islam (Kemenag MTs)
        // ===================================
        $mapelKeagamaan = [
            ['kode_mapel' => 'AQH',  'nama_mapel' => 'Al-Qur\'an Hadis'],
            ['kode_mapel' => 'AAK',  'nama_mapel' => 'Akidah Akhlak'],
            ['kode_mapel' => 'FQH',  'nama_mapel' => 'Fikih'],
            ['kode_mapel' => 'SKI',  'nama_mapel' => 'Sejarah Kebudayaan Islam'],
            ['kode_mapel' => 'BAR',  'nama_mapel' => 'Bahasa Arab'],
        ];

        foreach ($mapelUmum as $mapel) {
            MataPelajaran::updateOrCreate(
                ['kode_mapel' => $mapel['kode_mapel']],
                array_merge($mapel, ['jenis' => 'umum', 'is_aktif' => true])
            );
        }

        foreach ($mapelKeagamaan as $mapel) {
            MataPelajaran::updateOrCreate(
                ['kode_mapel' => $mapel['kode_mapel']],
                array_merge($mapel, ['jenis' => 'keagamaan', 'is_aktif' => true])
            );
        }

        $this->command->info('✅ Mata Pelajaran Kemenag MTs selesai di-seed!');
    }
}
