<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperTahunAjaran
 * @method static \Illuminate\Database\Eloquent\Builder aktif()
 */
class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';

    protected $fillable = ['nama', 'semester', 'is_aktif'];

    protected $casts = ['is_aktif' => 'boolean'];

    /** Relasi ke kelas */
    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    /** Label lengkap tahun ajaran, contoh: 2024/2025 Semester Ganjil */
    public function getLabelAttribute(): string
    {
        $semester = $this->semester == '1' ? 'Ganjil' : 'Genap';
        return "{$this->nama} Semester {$semester}";
    }

    /** Scope: ambil tahun ajaran yang sedang aktif */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Pastikan hanya satu tahun ajaran yang aktif saat ini.
     * Nonaktifkan semua yang lain sebelum mengaktifkan yang baru.
     */
    public static function aktifkanSatu(int $id): void
    {
        self::query()->update(['is_aktif' => false]);
        self::query()->where('id', $id)->update(['is_aktif' => true]);
    }
}
