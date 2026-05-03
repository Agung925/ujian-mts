<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel relasi: satu guru bisa mengajar banyak mapel
        Schema::create('guru_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')
                  ->constrained('mata_pelajaran')
                  ->onDelete('cascade');
            $table->timestamps();

            // Satu guru tidak boleh di-assign ke mapel yang sama dua kali
            $table->unique(['user_id', 'mata_pelajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_mapel');
    }
};
