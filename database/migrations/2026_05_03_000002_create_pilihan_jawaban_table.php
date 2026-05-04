<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel ini untuk menyimpan pilihan jawaban soal PG dan BS
        // Essay tidak punya pilihan jawaban di tabel ini
        Schema::create('pilihan_jawaban', function (Blueprint $table) {
            $table->id();

            $table->foreignId('soal_id')
                  ->constrained('bank_soal')
                  ->onDelete('cascade');

            // Label pilihan: A, B, C, D, E (untuk PG) atau Benar, Salah (untuk BS)
            $table->string('label', 10);

            // Teks isi pilihan jawaban
            $table->text('teks_pilihan');

            // Apakah pilihan ini adalah jawaban yang benar?
            $table->boolean('is_benar')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilihan_jawaban');
    }
};
