<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /** Tampilkan halaman dashboard guru beserta mapel yang diajar */
    public function index()
    {
        $guruId = Auth::id();

        // Ambil daftar mata pelajaran yang diampu guru yang sedang login
        $mapelDiajar = Auth::user()->mataPelajaran()->where('is_aktif', true)->get();

        // Statistik bank soal milik guru ini per tipe
        $statsSoal = [
            'pg'    => BankSoal::query()->milikGuru($guruId)->where('tipe_soal', 'pg')->count(),
            'bs'    => BankSoal::query()->milikGuru($guruId)->where('tipe_soal', 'bs')->count(),
            'essay' => BankSoal::query()->milikGuru($guruId)->where('tipe_soal', 'essay')->count(),
        ];

        return view('guru.dashboard', compact('mapelDiajar', 'statsSoal'));
    }
}
