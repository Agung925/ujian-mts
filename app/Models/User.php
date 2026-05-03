<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi massal (mass assignment)
     */
    protected $fillable = [
        'name',
        'nis',
        'email',
        'password',
        'role',
        'is_aktif',
        'jenis_kelamin',
        'no_telp',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data kolom
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_aktif'          => 'boolean',
        ];
    }

    // =============================================
    // SCOPE — Filter berdasarkan role
    // =============================================

    /** Scope untuk mengambil hanya data siswa */
    public function scopeSiswa($query)
    {
        return $query->where('role', 'siswa');
    }

    /** Scope untuk mengambil hanya data guru */
    public function scopeGuru($query)
    {
        return $query->where('role', 'guru');
    }

    /** Scope untuk mengambil hanya akun yang aktif */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    // =============================================
    // HELPER — Cek role pengguna
    // =============================================

    /** Cek apakah user adalah super admin */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /** Cek apakah user adalah guru */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /** Cek apakah user adalah siswa */
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    // =============================================
    // RELASI — Data Master (Step 3)
    // =============================================

    /** Relasi: guru mengajar banyak mata pelajaran */
    public function mataPelajaran()
    {
        return $this->belongsToMany(MataPelajaran::class, 'guru_mapel', 'user_id', 'mata_pelajaran_id');
    }

    /** Relasi: siswa terdaftar di kelas */
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'siswa_kelas', 'user_id', 'kelas_id');
    }
}
