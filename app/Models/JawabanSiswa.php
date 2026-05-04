<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanSiswa extends Model
{
    protected $table = 'jawaban_siswa';

    protected $fillable = [
        'sesi_id', 'soal_id', 'pilihan_id',
        'jawaban_essay', 'is_benar', 'nilai',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
    ];

    public function sesi()
    {
        return $this->belongsTo(SesiUjian::class, 'sesi_id');
    }

    public function soal()
    {
        return $this->belongsTo(BankSoal::class, 'soal_id');
    }

    public function pilihan()
    {
        return $this->belongsTo(PilihanJawaban::class, 'pilihan_id');
    }
}
