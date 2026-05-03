<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    /** Daftar semua tahun ajaran */
    public function index()
    {
        $tahunAjaran = TahunAjaran::latest('created_at')->paginate(10);
        return view('admin.tahun_ajaran.index', compact('tahunAjaran'));
    }

    /** Form tambah tahun ajaran */
    public function create()
    {
        return view('admin.tahun_ajaran.create');
    }

    /** Simpan tahun ajaran baru */
    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:20',
            'semester' => 'required|in:1,2',
        ], [
            'nama.required'     => 'Format tahun ajaran wajib diisi. Contoh: 2025/2026',
            'semester.required' => 'Semester wajib dipilih.',
        ]);

        // Cek duplikat kombinasi nama + semester
        $exists = TahunAjaran::query()->where('nama', $request->nama)
            ->where('semester', $request->semester)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Tahun ajaran dengan semester tersebut sudah ada.');
        }

        TahunAjaran::create([
            'nama'     => $request->nama,
            'semester' => $request->semester,
            'is_aktif' => false,
        ]);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /** Tampil detail — tidak digunakan, redirect ke index */
    public function show(TahunAjaran $tahunAjaran)
    {
        return redirect()->route('admin.tahun-ajaran.index');
    }

    /** Form edit tahun ajaran */
    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('admin.tahun_ajaran.edit', compact('tahunAjaran'));
    }

    /** Update tahun ajaran */
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'nama'     => 'required|string|max:20',
            'semester' => 'required|in:1,2',
        ]);

        $tahunAjaran->update($request->only(['nama', 'semester']));

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /** Aktifkan satu tahun ajaran (nonaktifkan yang lain) */
    public function aktifkan(TahunAjaran $tahunAjaran)
    {
        TahunAjaran::aktifkanSatu($tahunAjaran->id);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', "Tahun ajaran {$tahunAjaran->label} sekarang aktif.");
    }

    /** Hapus tahun ajaran */
    public function destroy(TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->is_aktif) {
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('error', 'Tidak bisa menghapus tahun ajaran yang sedang aktif.');
        }

        TahunAjaran::destroy($tahunAjaran->id);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
