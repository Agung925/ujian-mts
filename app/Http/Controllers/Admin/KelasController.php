<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\SiswaKelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunAjaran::aktif()->first();
        $kelas      = Kelas::with('tahunAjaran')
                           ->where('tahun_ajaran_id', $tahunAktif?->id)
                           ->orderBy('tingkat')
                           ->orderBy('nama_kelas')
                           ->paginate(15);

        return view('admin.kelas.index', compact('kelas', 'tahunAktif'));
    }

    public function create()
    {
        $tahunAjaran = TahunAjaran::orderBy('nama', 'asc')->get();
        return view('admin.kelas.create', compact('tahunAjaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas'      => 'required|string|max:20',
            'tingkat'         => 'required|in:VII,VIII,IX',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        Kelas::create($request->only(['nama_kelas', 'tingkat', 'tahun_ajaran_id']));

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kelas)
    {
        return redirect()->route('admin.kelas.index');
    }

    public function edit(Kelas $kelas)
    {
        $tahunAjaran = TahunAjaran::orderBy('nama', 'asc')->get();
        return view('admin.kelas.edit', compact('kelas', 'tahunAjaran'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:20',
            'tingkat'    => 'required|in:VII,VIII,IX',
            'is_aktif'   => 'required|boolean',
        ]);

        $kelas->update($request->only(['nama_kelas', 'tingkat', 'is_aktif']));

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        Kelas::destroy($kelas->id);
        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    /** Halaman assign siswa ke kelas */
    public function assignSiswa(Kelas $kelas)
    {
        // Ambil siswa yang belum di-assign ke kelas manapun di tahun ajaran ini
        $siswaBelumAssign = User::siswa()->aktif()
            ->whereDoesntHave('kelas', function ($q) use ($kelas) {
                // Kualifikasi nama tabel agar tidak ambigu (kelas & siswa_kelas sama-sama punya kolom ini)
                $q->where('kelas.tahun_ajaran_id', $kelas->tahun_ajaran_id);
            })
            ->orderBy('name')
            ->get();

        // Ambil siswa yang sudah ada di kelas ini
        $siswaKelas = $kelas->siswa()->orderBy('name')->get();

        return view('admin.kelas.assign_siswa', compact('kelas', 'siswaBelumAssign', 'siswaKelas'));
    }

    /** Simpan assign siswa ke kelas */
    public function simpanAssignSiswa(Request $request, Kelas $kelas)
    {
        $request->validate([
            'siswa_ids'   => 'required|array',
            'siswa_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->siswa_ids as $siswaId) {
            SiswaKelas::updateOrCreate([
                'user_id'         => $siswaId,
                'tahun_ajaran_id' => $kelas->tahun_ajaran_id,
            ], [
                'kelas_id' => $kelas->id,
            ]);
        }

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Siswa berhasil di-assign ke kelas ' . $kelas->nama_kelas);
    }
}
