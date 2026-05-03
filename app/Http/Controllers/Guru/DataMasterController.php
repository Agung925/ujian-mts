<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;

class DataMasterController extends Controller
{
    /** Guru hanya bisa melihat daftar kelas (read only) */
    public function kelas()
    {
        $tahunAktif = TahunAjaran::aktif()->first();
        $kelas      = Kelas::aktif()
                           ->where('tahun_ajaran_id', $tahunAktif?->id)
                           ->orderBy('tingkat')
                           ->orderBy('nama_kelas')
                           ->get();

        return view('guru.data_master.kelas', compact('kelas', 'tahunAktif'));
    }

    /** Guru hanya bisa melihat daftar mata pelajaran (read only) */
    public function mataPelajaran()
    {
        $umum      = MataPelajaran::jenis('umum')->aktif()->orderBy('nama_mapel')->get();
        $keagamaan = MataPelajaran::jenis('keagamaan')->aktif()->orderBy('nama_mapel')->get();

        return view('guru.data_master.mata_pelajaran', compact('umum', 'keagamaan'));
    }
}
