<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil tahun ajaran yang sedang aktif
        $tahunAjaran = TahunAjaran::query()->where('is_aktif', true)->first();

        if (!$tahunAjaran) {
            $this->command->warn('⚠️ Tidak ada tahun ajaran aktif. Jalankan TahunAjaranSeeder dulu!');
            return;
        }

        // Generate kelas VII-A sampai IX-C
        $tingkatan = ['VII', 'VIII', 'IX'];
        $rombel    = ['A', 'B', 'C'];

        foreach ($tingkatan as $tingkat) {
            foreach ($rombel as $huruf) {
                Kelas::updateOrCreate(
                    [
                        'nama_kelas'      => "{$tingkat}-{$huruf}",
                        'tahun_ajaran_id' => $tahunAjaran->id,
                    ],
                    [
                        'tingkat'  => $tingkat,
                        'is_aktif' => true,
                    ]
                );
            }
        }

        $this->command->info('✅ Kelas MTs (VII-A s/d IX-C) selesai di-seed!');
    }
}
