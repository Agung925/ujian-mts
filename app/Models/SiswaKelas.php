<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSiswaKelas
 */
class SiswaKelas extends Model
{
    protected $table = 'siswa_kelas';

    protected $fillable = ['user_id', 'kelas_id', 'tahun_ajaran_id'];

    /** Relasi ke siswa */
    public function siswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Relasi ke kelas */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    /** Relasi ke tahun ajaran */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
