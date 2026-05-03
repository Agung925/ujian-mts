<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /** Tampilkan halaman dashboard guru beserta mapel yang diajar */
    public function index()
    {
        // Ambil daftar mata pelajaran yang diampu guru yang sedang login
        $mapelDiajar = Auth::user()->mataPelajaran()->where('is_aktif', true)->get();

        return view('guru.dashboard', compact('mapelDiajar'));
    }
}
