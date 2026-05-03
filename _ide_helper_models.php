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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperGuruMapel {}
}

namespace App\Models{
/**
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas aktif()
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKelas {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_mapel
 * @property string $kode_mapel
 * @property string $jenis
 * @property bool $is_aktif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $guru
 * @property-read int|null $guru_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MataPelajaran aktif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MataPelajaran jenis(string $jenis)
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMataPelajaran {}
}

namespace App\Models{
/**
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSiswaKelas {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama
 * @property string $semester
 * @property bool $is_aktif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kelas> $kelas
 * @property-read int|null $kelas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran aktif()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereIsAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereSemester($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTahunAjaran {}
}

namespace App\Models{
/**
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

