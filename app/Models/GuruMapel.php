<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperGuruMapel
 */
class GuruMapel extends Model
{
    protected $table = 'guru_mapel';

    protected $fillable = ['user_id', 'mata_pelajaran_id'];

    /** Relasi ke guru (user) */
    public function guru()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Relasi ke mata pelajaran */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}
