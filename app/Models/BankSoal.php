<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperBankSoal
 * @method static \Illuminate\Database\Eloquent\Builder milikGuru(int $guruId)
 * @method static \Illuminate\Database\Eloquent\Builder mapel(int $mapelId)
 * @method static \Illuminate\Database\Eloquent\Builder tipe(string $tipe)
 * @method static \Illuminate\Database\Eloquent\Builder aktif()
 */
class BankSoal extends Model
{
    protected $table = 'bank_soal';

    protected $fillable = [
        'guru_id',
        'mata_pelajaran_id',
        'pertanyaan',
        'tipe_soal',
        'gambar',
        'tingkat_kesulitan',
        'bobot_nilai',
        'kunci_essay',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    // =============================================
    // RELASI
    // =============================================

    /** Relasi ke guru yang membuat soal */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /** Relasi ke mata pelajaran */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    /** Relasi ke pilihan jawaban, diurutkan by label */
    public function pilihanJawaban()
    {
        return $this->hasMany(PilihanJawaban::class, 'soal_id')->orderBy('label');
    }

    /** Ambil hanya satu jawaban yang benar */
    public function jawabanBenar()
    {
        return $this->hasOne(PilihanJawaban::class, 'soal_id')->where('is_benar', true);
    }

    // =============================================
    // SCOPE
    // =============================================

    /** Scope: soal milik guru tertentu */
    public function scopeMilikGuru($query, int $guruId)
    {
        return $query->where('guru_id', $guruId);
    }

    /** Scope: filter berdasarkan mata pelajaran */
    public function scopeMapel($query, int $mapelId)
    {
        return $query->where('mata_pelajaran_id', $mapelId);
    }

    /** Scope: filter berdasarkan tipe soal */
    public function scopeTipe($query, string $tipe)
    {
        return $query->where('tipe_soal', $tipe);
    }

    /** Scope: hanya soal yang aktif */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    // =============================================
    // ACCESSOR / HELPER
    // =============================================

    /** Label tipe soal dalam Bahasa Indonesia */
    public function getLabelTipeAttribute(): string
    {
        return match($this->tipe_soal) {
            'pg'    => 'Pilihan Ganda',
            'bs'    => 'Benar / Salah',
            'essay' => 'Essay',
            default => '-',
        };
    }

    /** Label tingkat kesulitan */
    public function getLabelKesulitanAttribute(): string
    {
        return match($this->tingkat_kesulitan) {
            'mudah'  => 'Mudah',
            'sedang' => 'Sedang',
            'sulit'  => 'Sulit',
            default  => '-',
        };
    }

    /** Warna badge Tailwind untuk tingkat kesulitan */
    public function getWarnaBadgeKesulitanAttribute(): string
    {
        return match($this->tingkat_kesulitan) {
            'mudah'  => 'bg-green-100 text-green-700',
            'sedang' => 'bg-yellow-100 text-yellow-700',
            'sulit'  => 'bg-red-100 text-red-700',
            default  => 'bg-gray-100 text-gray-700',
        };
    }

    /** URL gambar soal (null jika tidak ada) */
    public function getUrlGambarAttribute(): ?string
    {
        return $this->gambar ? Storage::url($this->gambar) : null;
    }
}
