<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Buat akun super admin default untuk pertama kali login.
     * Password default: Admin@12345 (wajib diganti setelah login pertama)
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ujianmts.sch.id'],
            [
                'name'          => 'Super Admin',
                'email'         => 'admin@ujianmts.sch.id',
                'password'      => Hash::make('Admin@12345'),
                'role'          => 'super_admin',
                'is_aktif'      => true,
                'jenis_kelamin' => null,
                'no_telp'       => null,
            ]
        );

        $this->command->info('Super Admin berhasil dibuat: admin@ujianmts.sch.id / Admin@12345');
    }
}
