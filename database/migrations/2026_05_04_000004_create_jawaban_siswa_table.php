<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_siswa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sesi_id')
                  ->constrained('sesi_ujian')
                  ->onDelete('cascade');

            $table->foreignId('soal_id')
                  ->constrained('bank_soal')
                  ->onDelete('cascade');

            // Jawaban untuk soal PG dan BS: ID dari pilihan_jawaban yang dipilih
            $table->foreignId('pilihan_id')
                  ->nullable()
                  ->constrained('pilihan_jawaban')
                  ->onDelete('set null');

            // Jawaban untuk soal Essay: teks bebas
            $table->text('jawaban_essay')->nullable();

            // Apakah jawaban ini benar? (null = belum dikoreksi/essay)
            $table->boolean('is_benar')->nullable();

            // Nilai yang didapat untuk soal ini
            $table->decimal('nilai', 5, 2)->default(0);

            $table->timestamps();

            // Satu siswa hanya boleh punya satu jawaban per soal dalam satu sesi
            $table->unique(['sesi_id', 'soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_siswa');
    }
};
