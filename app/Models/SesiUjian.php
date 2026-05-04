<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiUjian extends Model
{
    protected $table = 'sesi_ujian';

    protected $fillable = [
        'ujian_id', 'siswa_id', 'waktu_mulai', 'waktu_selesai',
        'status', 'nilai_akhir', 'urutan_soal',
    ];

    protected $casts = [
        'waktu_mulai'   => 'datetime',
        'waktu_selesai' => 'datetime',
        'urutan_soal'   => 'array', // otomatis parse JSON ke array PHP
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(JawabanSiswa::class, 'sesi_id');
    }

    /** Hitung sisa waktu ujian dalam detik */
    public function getSisaWaktuDetikAttribute(): int
    {
        if (!$this->waktu_mulai) return 0;

        $batasWaktu = $this->waktu_mulai->addMinutes($this->ujian->durasi_menit);
        $sisaDetik  = now()->diffInSeconds($batasWaktu, false);

        return max(0, $sisaDetik);
    }

    /** Cek apakah waktu ujian sudah habis */
    public function isWaktuHabis(): bool
    {
        return $this->getSisaWaktuDetikAttribute() <= 0;
    }
}
