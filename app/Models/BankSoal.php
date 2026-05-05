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
        'kategori',
        'sub_kategori',
        'bobot_nilai',
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

    /**
     * Daftar kategori dan sub-kategori yang tersedia.
     * Format: ['Nama Kategori' => ['Sub 1', 'Sub 2', ...]]
     */
    public static function daftarKategori(): array
    {
        return [
            'Ujian Akhir Jenjang (Kelas 9)' => [
                'Asesmen Madrasah (AM)',
                'Asesmen Bakat Minat (ABM)',
                'Try Out Asesmen Madrasah',
                'Tes Kompetensi Akademik (TKA)',
            ],
            'Ujian Rutin (Semesteran)' => [
                'Asesmen Sumatif Akhir Semester (ASAS)',
                'Asesmen Sumatif Akhir Tahun (ASAT)',
                'Asesmen Sumatif Tengah Semester (ASTS)',
            ],
            'Evaluasi Nasional (Pemetaan)' => [
                'Asesmen Nasional Berbasis Komputer (ANBK)',
                'Asesmen Kompetensi Minimum (AKM)',
                'Survei Karakter & Lingkungan Belajar',
            ],
            'Penilaian Harian' => [
                'Asesmen Formatif (Harian)',
                'Asesmen Sumatif Lingkup Materi (Per Bab)',
            ],
        ];
    }

    /** Daftar semua sub-kategori dalam satu array flat (untuk validasi) */
    public static function semuaSubKategori(): array
    {
        return collect(self::daftarKategori())->flatten()->all();
    }

    /** URL gambar soal (null jika tidak ada) */
    public function getUrlGambarAttribute(): ?string
    {
        return $this->gambar ? Storage::url($this->gambar) : null;
    }
}
