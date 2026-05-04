<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Guru\DashboardController as GuruDashboard;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\UjianController as SiswaUjianController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\GuruMapelController;
use App\Http\Controllers\Guru\DataMasterController;
use App\Http\Controllers\Guru\BankSoalController as GuruBankSoalController;
use App\Http\Controllers\Admin\BankSoalController as AdminBankSoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Guru\UjianController as GuruUjianController;

// =============================================
// ROUTE PUBLIK — Redirect ke dashboard sesuai role setelah login
// =============================================
Route::get('/', function () {
    if (Auth::check()) {
        // Arahkan ke dashboard sesuai role pengguna
        $routeName = match(Auth::user()->role) {
            'super_admin' => 'admin.dashboard',
            'guru'        => 'guru.dashboard',
            'siswa'       => 'siswa.dashboard',
            default       => 'login',
        };
        return redirect()->route($routeName);
    }
    return redirect()->route('login');
});

// =============================================
// ROUTE SUPER ADMIN
// =============================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.superadmin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Manajemen User (Step 2)
    Route::get('users/template-excel', [UserController::class, 'downloadTemplate'])->name('users.template-excel');
    Route::post('users/import-siswa', [UserController::class, 'importSiswa'])->name('users.import-siswa');
    Route::resource('users', UserController::class);

    // ===== Data Master: Tahun Ajaran (Step 3) =====
    Route::post('tahun-ajaran/{tahunAjaran}/aktifkan', [TahunAjaranController::class, 'aktifkan'])->name('tahun-ajaran.aktifkan');
    Route::resource('tahun-ajaran', TahunAjaranController::class);

    // ===== Data Master: Mata Pelajaran (Step 3) =====
    Route::resource('mata-pelajaran', MataPelajaranController::class);

    // ===== Data Master: Kelas (Step 3) =====
    Route::get('kelas/{kelas}/assign-siswa', [KelasController::class, 'assignSiswa'])->name('kelas.assign-siswa');
    Route::post('kelas/{kelas}/simpan-assign-siswa', [KelasController::class, 'simpanAssignSiswa'])->name('kelas.simpan-assign-siswa');
    Route::resource('kelas', KelasController::class);

    // ===== Data Master: Guru Mapel (Step 3) =====
    Route::get('guru-mapel', [GuruMapelController::class, 'index'])->name('guru-mapel.index');
    Route::post('guru-mapel', [GuruMapelController::class, 'store'])->name('guru-mapel.store');

    // ===== Bank Soal (Step 4) — Admin hanya monitor, tidak bisa edit =====
    Route::get('bank-soal', [AdminBankSoalController::class, 'index'])->name('bank-soal.index');
});

// =============================================
// ROUTE GURU
// =============================================
Route::prefix('guru')->name('guru.')->middleware(['auth', 'role.guru'])->group(function () {
    Route::get('/dashboard', [GuruDashboard::class, 'index'])->name('dashboard');

    // ===== Data Master: Read Only untuk Guru (Step 3) =====
    Route::get('data-master/kelas', [DataMasterController::class, 'kelas'])->name('data-master.kelas');
    Route::get('data-master/mata-pelajaran', [DataMasterController::class, 'mataPelajaran'])->name('data-master.mata-pelajaran');

    // ===== Bank Soal (Step 4) — Guru CRUD soal milik sendiri =====
    // WAJIB: Route spesifik import SEBELUM resource agar tidak bentrok dengan {bankSoal} param
    Route::get('bank-soal/import', [GuruBankSoalController::class, 'formImport'])->name('bank-soal.form-import');
    Route::post('bank-soal/import', [GuruBankSoalController::class, 'prosesImport'])->name('bank-soal.proses-import');
    Route::get('bank-soal/template-excel', [GuruBankSoalController::class, 'downloadTemplate'])->name('bank-soal.template-excel');
    Route::resource('bank-soal', GuruBankSoalController::class);

    // ===== Ujian (Step 5) — Guru CRUD ujian + buka/tutup + tambah soal =====
    Route::resource('ujian', GuruUjianController::class);
    Route::post('ujian/{ujian}/tambah-soal-manual', [GuruUjianController::class, 'tambahSoalManual'])->name('ujian.tambah-soal-manual');
    Route::post('ujian/{ujian}/tambah-soal-random', [GuruUjianController::class, 'tambahSoalRandom'])->name('ujian.tambah-soal-random');
    Route::delete('ujian/{ujian}/hapus-soal/{soalId}', [GuruUjianController::class, 'hapusSoal'])->name('ujian.hapus-soal');
    Route::post('ujian/{ujian}/buka', [GuruUjianController::class, 'buka'])->name('ujian.buka');
    Route::post('ujian/{ujian}/tutup', [GuruUjianController::class, 'tutup'])->name('ujian.tutup');
    Route::get('ujian/{ujian}/siswa/{sesi}/detail', [GuruUjianController::class, 'detailSiswa'])->name('ujian.detail-siswa');
});

// =============================================
// ROUTE SISWA
// =============================================
Route::prefix('siswa')->name('siswa.')->middleware(['auth', 'role.siswa'])->group(function () {
    Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('dashboard');

    // Masuk ujian dengan token
    Route::post('/ujian/masuk', [SiswaUjianController::class, 'masuk'])->name('ujian.masuk');

    // Ruang ujian — mengerjakan soal
    Route::get('/ujian/{sesiId}/ruang', [SiswaUjianController::class, 'ruang'])->name('ujian.ruang');

    // Simpan jawaban per soal (AJAX)
    Route::post('/ujian/{sesiId}/jawab', [SiswaUjianController::class, 'jawab'])->name('ujian.jawab');

    // Submit (kumpulkan) ujian
    Route::post('/ujian/{sesiId}/submit', [SiswaUjianController::class, 'submit'])->name('ujian.submit');

    // Halaman hasil ujian
    Route::get('/ujian/{sesiId}/hasil', [SiswaUjianController::class, 'hasil'])->name('ujian.hasil');
});

// =============================================
// ROUTE AUTH (Login, Logout — dari Breeze)
// =============================================
require __DIR__ . '/auth.php';

// =============================================
// ROUTE PROFILE (Edit profil pengguna)
// =============================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

