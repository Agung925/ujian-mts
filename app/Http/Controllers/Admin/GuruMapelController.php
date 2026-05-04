<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruMapel;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Http\Request;

class GuruMapelController extends Controller
{
    /** Halaman assign mata pelajaran ke guru */
    public function index(Request $request)
    {
        $guru      = User::guru()->aktif()->with('mataPelajaran')->orderBy('name')->get();
        $allMapel  = MataPelajaran::aktif()->orderBy('nama_mapel')->get();

        // Jika ada query ?guru_id=X, pre-fill form dengan data guru tersebut
        $selectedGuruId  = $request->query('guru_id');
        $selectedMapelIds = [];
        if ($selectedGuruId) {
            $selectedGuruObj  = $guru->firstWhere('id', $selectedGuruId);
            $selectedMapelIds = $selectedGuruObj
                ? $selectedGuruObj->mataPelajaran->pluck('id')->toArray()
                : [];
        }

        return view('admin.guru_mapel.index', compact('guru', 'allMapel', 'selectedGuruId', 'selectedMapelIds'));
    }

    /** Simpan assign mapel ke guru */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'              => 'required|exists:users,id',
            'mata_pelajaran_ids'   => 'nullable|array',
            'mata_pelajaran_ids.*' => 'exists:mata_pelajaran,id',
        ]);

        // Hapus assign lama untuk guru ini, lalu buat yang baru
        GuruMapel::query()->where('user_id', $request->user_id)->delete();

        foreach ($request->mata_pelajaran_ids as $mapelId) {
            GuruMapel::create([
                'user_id'           => $request->user_id,
                'mata_pelajaran_id' => $mapelId,
            ]);
        }

        return redirect()->route('admin.guru-mapel.index')
            ->with('success', 'Mata pelajaran berhasil di-assign ke guru.');
    }
}
