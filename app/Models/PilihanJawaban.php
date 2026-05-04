<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPilihanJawaban
 */
class PilihanJawaban extends Model
{
    protected $table = 'pilihan_jawaban';

    protected $fillable = [
        'soal_id',
        'label',
        'teks_pilihan',
        'is_benar',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
    ];

    /** Relasi balik ke soal */
    public function soal()
    {
        return $this->belongsTo(BankSoal::class, 'soal_id');
    }
}
