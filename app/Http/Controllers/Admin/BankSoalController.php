<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Http\Request;

class BankSoalController extends Controller
{
    /**
     * Admin bisa melihat SEMUA soal dari semua guru.
     * Dilengkapi filter per guru, per mapel, dan per tipe soal.
     */
    public function index(Request $request)
    {
        $query = BankSoal::query()->with(['guru', 'mataPelajaran']);

        // Filter berdasarkan guru
        if ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }

        // Filter berdasarkan mata pelajaran
        if ($request->filled('mapel_id')) {
            $query->where('mata_pelajaran_id', $request->mapel_id);
        }

        // Filter berdasarkan tipe soal
        if ($request->filled('tipe_soal')) {
            $query->where('tipe_soal', $request->tipe_soal);
        }

        $soal = $query->latest()->paginate(20)->withQueryString();

        // Data untuk dropdown filter
        $semuaGuru  = User::query()->where('role', 'guru')->where('is_aktif', true)->orderBy('name')->get();
        $semuaMapel = MataPelajaran::query()->where('is_aktif', true)->orderBy('nama_mapel')->get();

        // Statistik jumlah soal per tipe
        $statsBase = BankSoal::query();
        if ($request->filled('guru_id')) {
            $statsBase->where('guru_id', $request->guru_id);
        }
        $stats = [
            'total_pg'    => (clone $statsBase)->where('tipe_soal', 'pg')->count(),
            'total_bs'    => (clone $statsBase)->where('tipe_soal', 'bs')->count(),
            'total_essay' => (clone $statsBase)->where('tipe_soal', 'essay')->count(),
        ];

        return view('admin.bank_soal.index', compact('soal', 'semuaGuru', 'semuaMapel', 'stats'));
    }
}
