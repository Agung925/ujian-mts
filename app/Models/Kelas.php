<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperKelas
 * @method static \Illuminate\Database\Eloquent\Builder aktif()
 */
class Kelas extends Model
{
    protected $fillable = ['nama_kelas', 'tingkat', 'tahun_ajaran_id', 'is_aktif'];

    protected $casts = ['is_aktif' => 'boolean'];

    /** Relasi ke tahun ajaran */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /** Relasi ke siswa melalui tabel pivot siswa_kelas */
    public function siswa()
    {
        return $this->belongsToMany(User::class, 'siswa_kelas', 'kelas_id', 'user_id');
    }

    /** Scope: ambil kelas yang aktif */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
