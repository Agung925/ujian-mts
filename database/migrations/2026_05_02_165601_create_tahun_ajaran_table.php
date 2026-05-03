<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            // Contoh format: 2024/2025
            $table->string('nama', 20);
            // Semester 1 (Ganjil) atau 2 (Genap)
            $table->enum('semester', ['1', '2']);
            // Hanya satu tahun ajaran yang aktif dalam satu waktu
            $table->boolean('is_aktif')->default(false);
            $table->timestamps();

            // Kombinasi nama + semester harus unik
            $table->unique(['nama', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};
