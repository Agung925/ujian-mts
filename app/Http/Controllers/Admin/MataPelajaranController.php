<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index()
    {
        // Kelompokkan berdasarkan jenis untuk tampilan yang lebih rapi
        $umum      = MataPelajaran::jenis('umum')->orderBy('nama_mapel')->get();
        $keagamaan = MataPelajaran::jenis('keagamaan')->orderBy('nama_mapel')->get();
        return view('admin.mata_pelajaran.index', compact('umum', 'keagamaan'));
    }

    public function create()
    {
        return view('admin.mata_pelajaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajaran,kode_mapel',
            'jenis'      => 'required|in:umum,keagamaan',
        ], [
            'kode_mapel.unique' => 'Kode mapel sudah digunakan.',
        ]);

        MataPelajaran::create($request->only(['nama_mapel', 'kode_mapel', 'jenis']));

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function show(MataPelajaran $mataPelajaran)
    {
        return redirect()->route('admin.mata-pelajaran.index');
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        return view('admin.mata_pelajaran.edit', compact('mataPelajaran'));
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajaran,kode_mapel,' . $mataPelajaran->id,
            'jenis'      => 'required|in:umum,keagamaan',
            'is_aktif'   => 'required|boolean',
        ]);

        $mataPelajaran->update($request->only(['nama_mapel', 'kode_mapel', 'jenis', 'is_aktif']));

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        MataPelajaran::destroy($mataPelajaran->id);
        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
