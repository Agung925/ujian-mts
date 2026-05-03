<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperMataPelajaran
 * @method static \Illuminate\Database\Eloquent\Builder aktif()
 * @method static \Illuminate\Database\Eloquent\Builder jenis(string $jenis)
 */
class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';

    protected $fillable = ['nama_mapel', 'kode_mapel', 'jenis', 'is_aktif'];

    protected $casts = ['is_aktif' => 'boolean'];

    /** Relasi ke guru melalui tabel pivot guru_mapel */
    public function guru()
    {
        return $this->belongsToMany(User::class, 'guru_mapel', 'mata_pelajaran_id', 'user_id');
    }

    /** Scope: ambil hanya mapel yang aktif */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    /** Scope: filter berdasarkan jenis (umum/keagamaan) */
    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis', $jenis);
    }
}
