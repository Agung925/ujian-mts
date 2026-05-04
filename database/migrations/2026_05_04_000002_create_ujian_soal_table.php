<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel relasi antara ujian dan soal-soal yang dipilih
        Schema::create('ujian_soal', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ujian_id')
                  ->constrained('ujian')
                  ->onDelete('cascade');

            $table->foreignId('soal_id')
                  ->constrained('bank_soal')
                  ->onDelete('cascade');

            // Nomor urut soal dalam ujian ini
            $table->unsignedSmallInteger('nomor_urut')->default(1);

            // Bobot nilai soal dalam ujian ini (bisa berbeda dari bobot default soal)
            $table->unsignedTinyInteger('bobot_nilai')->default(1);

            $table->timestamps();

            // Satu soal tidak boleh muncul dua kali dalam ujian yang sama
            $table->unique(['ujian_id', 'soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_soal');
    }
};
