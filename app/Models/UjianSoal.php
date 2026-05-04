<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianSoal extends Model
{
    protected $table = 'ujian_soal';

    protected $fillable = ['ujian_id', 'soal_id', 'nomor_urut', 'bobot_nilai'];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function soal()
    {
        return $this->belongsTo(BankSoal::class, 'soal_id');
    }
}
