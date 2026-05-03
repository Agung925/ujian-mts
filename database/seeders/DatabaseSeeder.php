<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            TahunAjaranSeeder::class,
            MataPelajaranSeeder::class,
            KelasSeeder::class,
        ]);
    }
}
