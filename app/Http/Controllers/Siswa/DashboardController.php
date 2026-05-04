<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\SesiUjian;
use App\Models\Ujian;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /** Tampilkan halaman dashboard siswa beserta ujian aktif dan riwayat */
    public function index()
    {
        $siswa = Auth::user();

        // Ambil ID kelas yang diikuti siswa ini
        $kelasIds = $siswa->kelas()->pluck('kelas.id')->toArray();

        // Ujian aktif yang ditujukan untuk kelas siswa ini
        $ujianAktif = Ujian::where('status', 'aktif')
                           ->whereIn('kelas_id', $kelasIds)
                           ->with(['mataPelajaran', 'kelas', 'guru'])
                           ->latest('dibuka_pada')
                           ->get();

        // Riwayat ujian yang sudah selesai dikerjakan siswa ini
        $riwayatUjian = SesiUjian::where('siswa_id', $siswa->id)
                                  ->where('status', 'selesai')
                                  ->with(['ujian.mataPelajaran'])
                                  ->latest('waktu_selesai')
                                  ->get();

        // Sesi yang sedang berjalan (status: sedang)
        $sesiAktif = SesiUjian::where('siswa_id', $siswa->id)
                               ->where('status', 'sedang')
                               ->with('ujian')
                               ->first();

        return view('siswa.dashboard', compact('ujianAktif', 'riwayatUjian', 'sesiAktif', 'kelasIds'));
    }
}
