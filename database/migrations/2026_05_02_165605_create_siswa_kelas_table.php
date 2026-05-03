<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel relasi: satu siswa masuk ke satu kelas per tahun ajaran
        Schema::create('siswa_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('kelas_id')
                  ->constrained('kelas')
                  ->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')
                  ->constrained('tahun_ajaran')
                  ->onDelete('cascade');
            $table->timestamps();

            // Satu siswa hanya boleh ada di satu kelas per tahun ajaran
            $table->unique(['user_id', 'tahun_ajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_kelas');
    }
};
