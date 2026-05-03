<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\TahunAjaran;

class DashboardController extends Controller
{
    /** Tampilkan halaman dashboard super admin dengan statistik */
    public function index()
    {
        // Ambil statistik untuk ditampilkan di dashboard
        $tahunAktif = TahunAjaran::aktif()->first();

        $stats = [
            'total_guru'  => User::query()->where('role', 'guru')->where('is_aktif', true)->count(),
            'total_siswa' => User::query()->where('role', 'siswa')->where('is_aktif', true)->count(),
            'total_kelas' => Kelas::aktif()->when($tahunAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))->count(),
            'total_mapel' => MataPelajaran::aktif()->count(),
        ];

        return view('admin.dashboard', compact('stats', 'tahunAktif'));
    }
}
