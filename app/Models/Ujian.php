<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ujian extends Model
{
    protected $table = 'ujian';

    protected $fillable = [
        'judul', 'mata_pelajaran_id', 'kelas_id', 'guru_id',
        'durasi_menit', 'token', 'status', 'acak_soal',
        'acak_jawaban', 'jumlah_soal', 'deskripsi',
        'dibuka_pada', 'ditutup_pada',
    ];

    protected $casts = [
        'acak_soal'    => 'boolean',
        'acak_jawaban' => 'boolean',
        'dibuka_pada'  => 'datetime',
        'ditutup_pada' => 'datetime',
    ];

    // =============================================
    // RELASI
    // =============================================

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /** Soal-soal yang ada dalam ujian ini via pivot ujian_soal */
    public function soal()
    {
        return $this->belongsToMany(BankSoal::class, 'ujian_soal', 'ujian_id', 'soal_id')
                    ->withPivot('nomor_urut', 'bobot_nilai')
                    ->orderBy('ujian_soal.nomor_urut');
    }

    public function sesiUjian()
    {
        return $this->hasMany(SesiUjian::class);
    }

    // =============================================
    // SCOPE
    // =============================================

    /** Filter ujian milik guru tertentu */
    public function scopeMilikGuru($query, int $guruId)
    {
        return $query->where('guru_id', $guruId);
    }

    /** Filter ujian yang sedang aktif */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // =============================================
    // HELPER
    // =============================================

    /** Cek apakah ujian sedang aktif/dibuka */
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /** Cek apakah ujian sudah selesai */
    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    /** Cek apakah ujian masih draft */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /** Hitung total nilai maksimal ujian ini dari semua soal */
    public function getTotalNilaiMaksAttribute(): int
    {
        return $this->soal->sum('pivot.bobot_nilai');
    }

    /** Warna badge status untuk Tailwind */
    public function getWarnaBadgeStatusAttribute(): string
    {
        return match($this->status) {
            'draft'   => 'bg-gray-100 text-gray-600',
            'aktif'   => 'bg-green-100 text-green-700',
            'selesai' => 'bg-blue-100 text-blue-700',
            default   => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Generate token unik format huruf+angka
     * Contoh: MTK-A3F2, IPA-9X12
     */
    public static function generateToken(string $kodeMapel = 'CBT'): string
    {
        do {
            // Ambil 3 karakter kode mapel (huruf besar saja)
            $prefix = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $kodeMapel), 0, 3));
            if (empty($prefix)) $prefix = 'CBT';

            // Generate 4 karakter random huruf+angka
            $suffix = strtoupper(Str::random(4));

            $token = "{$prefix}-{$suffix}";

        } while (self::where('token', $token)->exists()); // pastikan unik

        return $token;
    }
}
