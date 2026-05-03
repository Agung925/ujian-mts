<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom NIS untuk siswa (nullable karena guru tidak pakai NIS)
            $table->string('nis', 20)->nullable()->unique()->after('name');

            // Ubah email menjadi nullable karena siswa tidak wajib punya email
            $table->string('email')->nullable()->change();

            // Tambah role pengguna
            $table->enum('role', ['super_admin', 'guru', 'siswa'])->default('siswa')->after('email');

            // Status akun aktif/nonaktif
            $table->boolean('is_aktif')->default(true)->after('role');

            // Jenis kelamin
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('is_aktif');

            // Nomor telepon (opsional)
            $table->string('no_telp', 15)->nullable()->after('jenis_kelamin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nis', 'role', 'is_aktif', 'jenis_kelamin', 'no_telp']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
