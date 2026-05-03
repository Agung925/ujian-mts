<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => '2024/2025', 'semester' => '1', 'is_aktif' => false],
            ['nama' => '2024/2025', 'semester' => '2', 'is_aktif' => false],
            ['nama' => '2025/2026', 'semester' => '1', 'is_aktif' => true], // ← aktif
            ['nama' => '2025/2026', 'semester' => '2', 'is_aktif' => false],
        ];

        foreach ($data as $item) {
            TahunAjaran::updateOrCreate(
                ['nama' => $item['nama'], 'semester' => $item['semester']],
                $item
            );
        }

        $this->command->info('✅ Tahun Ajaran selesai di-seed!');
    }
}
