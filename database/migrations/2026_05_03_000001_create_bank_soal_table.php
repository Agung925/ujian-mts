<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_soal', function (Blueprint $table) {
            $table->id();

            // Guru yang membuat soal ini
            $table->foreignId('guru_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Mata pelajaran soal ini
            $table->foreignId('mata_pelajaran_id')
                  ->constrained('mata_pelajaran')
                  ->onDelete('cascade');

            // Teks pertanyaan (bisa panjang, pakai text)
            $table->text('pertanyaan');

            // Tipe soal: pg = pilihan ganda, bs = benar/salah, essay
            $table->enum('tipe_soal', ['pg', 'bs', 'essay']);

            // Path gambar jika soal pakai gambar (opsional)
            $table->string('gambar')->nullable();

            // Tingkat kesulitan soal
            $table->enum('tingkat_kesulitan', ['mudah', 'sedang', 'sulit'])->default('sedang');

            // Bobot nilai default soal ini
            $table->unsignedSmallInteger('bobot_nilai')->default(1);

            // Khusus Essay: kunci jawaban/pedoman penilaian (opsional)
            $table->text('kunci_essay')->nullable();

            // Status soal: aktif bisa dipakai di ujian, nonaktif disembunyikan
            $table->boolean('is_aktif')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_soal');
    }
};
