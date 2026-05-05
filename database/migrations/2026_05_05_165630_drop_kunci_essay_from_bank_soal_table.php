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
        Schema::table('bank_soal', function (Blueprint $table) {
            // Hapus kolom kunci_essay — guru akan menilai essay secara manual
            $table->dropColumn('kunci_essay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_soal', function (Blueprint $table) {
            // Recover: tambah kembali kolom jika rollback
            $table->text('kunci_essay')->nullable()->after('jawaban_benar');
        });
    }
};
