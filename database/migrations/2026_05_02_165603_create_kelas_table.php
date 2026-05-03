<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            // Contoh: VII-A, VIII-B, IX-C
            $table->string('nama_kelas', 20);
            // Tingkat kelas: VII, VIII, atau IX
            $table->enum('tingkat', ['VII', 'VIII', 'IX']);
            // Relasi ke tahun ajaran
            $table->foreignId('tahun_ajaran_id')
                  ->constrained('tahun_ajaran')
                  ->onDelete('cascade');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            // Nama kelas + tahun ajaran harus unik
            $table->unique(['nama_kelas', 'tahun_ajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
