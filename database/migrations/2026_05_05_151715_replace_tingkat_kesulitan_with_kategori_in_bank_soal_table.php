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
            // Hapus kolom tingkat_kesulitan yang lama
            $table->dropColumn('tingkat_kesulitan');

            // Tambah kolom kategori utama (mis: Ujian Akhir Jenjang)
            $table->string('kategori', 100)->default('Penilaian Harian')->after('tipe_soal');

            // Tambah kolom sub kategori (mis: Asesmen Madrasah)
            $table->string('sub_kategori', 100)->default('Asesmen Formatif (Harian)')->after('kategori');
        });
    }

    public function down(): void
    {
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'sub_kategori']);
            $table->string('tingkat_kesulitan', 20)->default('mudah')->after('tipe_soal');
        });
    }
};
