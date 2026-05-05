<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @mixin IdeHelperBankSoal
 * @method static \Illuminate\Database\Eloquent\Builder milikGuru(int $guruId)
 * @method static \Illuminate\Database\Eloquent\Builder mapel(int $mapelId)
 * @method static \Illuminate\Database\Eloquent\Builder tipe(string $tipe)
 * @method static \Illuminate\Database\Eloquent\Builder aktif()
 * @property int $id
 * @property int $guru_id
 * @property int $mata_pelajaran_id
 * @property string $pertanyaan
 * @property string $tipe_soal
 * @property string|null $gambar
 * @property string $tingkat_kesulitan
 * @property int $bobot_nilai
 * @property string|null $kunci_essay
 * @property bool $is_aktif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $label_kesulitan
 * @property-read string $label_tipe
 * @property-read string|null $url_gambar
 * @property-read string $warna_badge_kesulitan
 * @property-read \App\Models\User $guru
 * @property-read \App\Models\PilihanJawaban|null $jawabanBenar
 * @property-read \App\Models\MataPelajaran $mataPelajaran
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PilihanJawaban> $pilihanJawaban
 * @property-read int|null $pilihan_jawaban_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal whereBobotNilai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal whereGambar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal whereGuruId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal whereIsAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal whereKunciEssay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal whereMataPelajaranId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal wherePertanyaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal whereTingkatKesulitan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal whereTipeSoal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankSoal whereUpdatedAt($value)
 */
	class BankSoal extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperGuruMapel
 * @property int $id
 * @property int $user_id
 * @property int $mata_pelajaran_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $guru
 * @property-read \App\Models\MataPelajaran $mataPelajaran
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel whereMataPelajaranId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuruMapel whereUserId($value)
 */
	class GuruMapel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sesi_id
 * @property int $soal_id
 * @property int|null $pilihan_id
 * @property string|null $jawaban_essay
 * @property bool|null $is_benar
 * @property numeric $nilai
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PilihanJawaban|null $pilihan
 * @property-read \App\Models\SesiUjian $sesi
 * @property-read \App\Models\BankSoal $soal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JawabanSiswa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JawabanSiswa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JawabanSiswa query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JawabanSiswa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JawabanSiswa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JawabanSiswa whereIsBenar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JawabanSiswa whereJawabanEssay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JawabanSiswa whereNilai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JawabanSiswa wherePilihanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JawabanSiswa whereSesiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JawabanSiswa whereSoalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JawabanSiswa whereUpdatedAt($value)
 */
	class JawabanSiswa extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperKelas
 * @method static \Illuminate\Database\Eloquent\Builder aktif()
 * @property int $id
 * @property string $nama_kelas
 * @property string $tingkat
 * @property int $tahun_ajaran_id
 * @property bool $is_aktif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $siswa
 * @property-read int|null $siswa_count
 * @property-read \App\Models\TahunAjaran $tahunAjaran
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereIsAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereNamaKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereTahunAjaranId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereTingkat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereUpdatedAt($value)
 */
	class Kelas extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperMataPelajaran
 * @method static \Illuminate\Database\Eloquent\Builder aktif()
 * @method static \Illuminate\Database\Eloquent\Builder jenis(string $jenis)
 * @property int $id
 * @property string $nama_mapel
 * @property string $kode_mapel
 * @property string $jenis
 * @property bool $is_aktif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $guru
 * @property-read int|null $guru_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MataPelajaran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MataPelajaran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MataPelajaran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MataPelajaran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MataPelajaran whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MataPelajaran whereIsAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MataPelajaran whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MataPelajaran whereKodeMapel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MataPelajaran whereNamaMapel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MataPelajaran whereUpdatedAt($value)
 */
	class MataPelajaran extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperPilihanJawaban
 * @property int $id
 * @property int $soal_id
 * @property string $label
 * @property string $teks_pilihan
 * @property bool $is_benar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankSoal $soal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PilihanJawaban newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PilihanJawaban newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PilihanJawaban query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PilihanJawaban whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PilihanJawaban whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PilihanJawaban whereIsBenar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PilihanJawaban whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PilihanJawaban whereSoalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PilihanJawaban whereTeksPilihan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PilihanJawaban whereUpdatedAt($value)
 */
	class PilihanJawaban extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $ujian_id
 * @property int $siswa_id
 * @property \Illuminate\Support\Carbon|null $waktu_mulai
 * @property \Illuminate\Support\Carbon|null $waktu_selesai
 * @property string $status
 * @property numeric|null $nilai_akhir
 * @property array<array-key, mixed>|null $urutan_soal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $jumlah_pelanggaran
 * @property-read int $sisa_waktu_detik
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JawabanSiswa> $jawabanSiswa
 * @property-read int|null $jawaban_siswa_count
 * @property-read \App\Models\User $siswa
 * @property-read \App\Models\Ujian $ujian
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian whereJumlahPelanggaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian whereNilaiAkhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian whereSiswaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian whereUjianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian whereUrutanSoal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian whereWaktuMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SesiUjian whereWaktuSelesai($value)
 */
	class SesiUjian extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperSiswaKelas
 * @property int $id
 * @property int $user_id
 * @property int $kelas_id
 * @property int $tahun_ajaran_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Kelas $kelas
 * @property-read \App\Models\User $siswa
 * @property-read \App\Models\TahunAjaran $tahunAjaran
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiswaKelas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiswaKelas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiswaKelas query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiswaKelas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiswaKelas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiswaKelas whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiswaKelas whereTahunAjaranId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiswaKelas whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SiswaKelas whereUserId($value)
 */
	class SiswaKelas extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperTahunAjaran
 * @method static \Illuminate\Database\Eloquent\Builder aktif()
 * @property int $id
 * @property string $nama
 * @property string $semester
 * @property bool $is_aktif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kelas> $kelas
 * @property-read int|null $kelas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereIsAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereSemester($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereUpdatedAt($value)
 */
	class TahunAjaran extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $judul
 * @property int $mata_pelajaran_id
 * @property int $kelas_id
 * @property int $guru_id
 * @property int $durasi_menit
 * @property string $token
 * @property string $status
 * @property bool $acak_soal
 * @property bool $acak_jawaban
 * @property int|null $jumlah_soal
 * @property string|null $deskripsi
 * @property \Illuminate\Support\Carbon|null $dibuka_pada
 * @property \Illuminate\Support\Carbon|null $ditutup_pada
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $total_nilai_maks
 * @property-read string $warna_badge_status
 * @property-read \App\Models\User $guru
 * @property-read \App\Models\Kelas $kelas
 * @property-read \App\Models\MataPelajaran $mataPelajaran
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SesiUjian> $sesiUjian
 * @property-read int|null $sesi_ujian_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BankSoal> $soal
 * @property-read int|null $soal_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian aktif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian milikGuru(int $guruId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereAcakJawaban($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereAcakSoal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereDibukaPada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereDitutupPada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereDurasiMenit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereGuruId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereJumlahSoal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereMataPelajaranId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ujian whereUpdatedAt($value)
 */
	class Ujian extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $ujian_id
 * @property int $soal_id
 * @property int $nomor_urut
 * @property int $bobot_nilai
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankSoal $soal
 * @property-read \App\Models\Ujian $ujian
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UjianSoal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UjianSoal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UjianSoal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UjianSoal whereBobotNilai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UjianSoal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UjianSoal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UjianSoal whereNomorUrut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UjianSoal whereSoalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UjianSoal whereUjianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UjianSoal whereUpdatedAt($value)
 */
	class UjianSoal extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperUser
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $nis
 * @property string $role
 * @property bool $is_aktif
 * @property string|null $jenis_kelamin
 * @property string|null $no_telp
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kelas> $kelas
 * @property-read int|null $kelas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MataPelajaran> $mataPelajaran
 * @property-read int|null $mata_pelajaran_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User aktif()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User guru()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User siswa()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNoTelp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

