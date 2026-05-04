<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian', function (Blueprint $table) {
            $table->id();

            // Judul ujian, contoh: "Ulangan Harian Bab 1 - PPKn"
            $table->string('judul', 200);

            // Relasi ke mata pelajaran
            $table->foreignId('mata_pelajaran_id')
                  ->constrained('mata_pelajaran')
                  ->onDelete('cascade');

            // Relasi ke kelas yang mengikuti ujian ini
            $table->foreignId('kelas_id')
                  ->constrained('kelas')
                  ->onDelete('cascade');

            // Relasi ke guru yang membuat ujian
            $table->foreignId('guru_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Durasi ujian dalam menit
            $table->unsignedSmallInteger('durasi_menit')->default(60);

            // Token akses ujian — format huruf+angka, contoh: MTK-A3F2
            $table->string('token', 20)->unique();

            // Status ujian: draft (belum dibuka), aktif (sedang berlangsung), selesai
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');

            // Apakah urutan soal diacak per siswa?
            $table->boolean('acak_soal')->default(true);

            // Apakah urutan pilihan jawaban diacak per siswa?
            $table->boolean('acak_jawaban')->default(true);

            // Jumlah soal yang diambil (untuk mode random)
            // Null = ambil semua soal yang sudah dipilih
            $table->unsignedSmallInteger('jumlah_soal')->nullable();

            // Catatan/deskripsi ujian (opsional)
            $table->text('deskripsi')->nullable();

            // Kapan guru membuka ujian
            $table->timestamp('dibuka_pada')->nullable();

            // Kapan guru menutup ujian
            $table->timestamp('ditutup_pada')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian');
    }
};
