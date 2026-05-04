<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sesi ujian = satu siswa mengerjakan satu ujian
        Schema::create('sesi_ujian', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ujian_id')
                  ->constrained('ujian')
                  ->onDelete('cascade');

            $table->foreignId('siswa_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Waktu siswa mulai mengerjakan
            $table->timestamp('waktu_mulai')->nullable();

            // Waktu siswa selesai (submit atau auto-submit)
            $table->timestamp('waktu_selesai')->nullable();

            // Status pengerjaan siswa
            $table->enum('status', ['belum_mulai', 'sedang', 'selesai'])->default('belum_mulai');

            // Nilai akhir siswa (dihitung setelah submit)
            $table->decimal('nilai_akhir', 5, 2)->nullable();

            // Urutan soal yang diacak khusus untuk siswa ini (disimpan sebagai JSON)
            $table->json('urutan_soal')->nullable();

            $table->timestamps();

            // Satu siswa hanya boleh punya satu sesi per ujian
            $table->unique(['ujian_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_ujian');
    }
};
