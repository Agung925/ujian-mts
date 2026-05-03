<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            // Nama lengkap mata pelajaran
            $table->string('nama_mapel', 100);
            // Kode singkat, contoh: MTK, BIN, ENG
            $table->string('kode_mapel', 10)->unique();
            // Jenis: umum atau keagamaan
            $table->enum('jenis', ['umum', 'keagamaan']);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran');
    }
};
