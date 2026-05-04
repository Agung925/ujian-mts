<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sesi_ujian', function (Blueprint $table) {
            // Jumlah pelanggaran anti-cheat (pindah tab, keluar fullscreen, dll)
            $table->unsignedSmallInteger('jumlah_pelanggaran')->default(0)->after('urutan_soal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_ujian', function (Blueprint $table) {
            $table->dropColumn('jumlah_pelanggaran');
        });
    }
};
