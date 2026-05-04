<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreSoalRequest;
use App\Http\Requests\Guru\UpdateSoalRequest;
use App\Models\BankSoal;
use App\Models\PilihanJawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BankSoalController extends Controller
{
    /**
     * Daftar soal milik guru yang sedang login.
     * Guru hanya bisa melihat soal miliknya sendiri.
     */
    public function index(Request $request)
    {
        $guruId = Auth::id();

        // Ambil mapel yang diajarkan guru ini untuk dropdown filter
        $mapelGuru = Auth::user()->mataPelajaran()->aktif()->get();

        // Query soal milik guru ini dengan eager loading
        $query = BankSoal::query()
                         ->milikGuru($guruId)
                         ->with(['mataPelajaran', 'pilihanJawaban']);

        // Filter berdasarkan mata pelajaran (opsional)
        if ($request->filled('mapel_id')) {
            $query->mapel((int) $request->mapel_id);
        }

        // Filter berdasarkan tipe soal (opsional)
        if ($request->filled('tipe_soal')) {
            $query->tipe($request->tipe_soal);
        }

        // Filter berdasarkan tingkat kesulitan (opsional)
        if ($request->filled('kesulitan')) {
            $query->where('tingkat_kesulitan', $request->kesulitan);
        }

        $soal = $query->latest()->paginate(15)->withQueryString();

        return view('guru.bank_soal.index', compact('soal', 'mapelGuru'));
    }

    /** Tampilkan form tambah soal baru */
    public function create()
    {
        // Guru hanya bisa pilih mapel yang diajarnya
        $mapelGuru = Auth::user()->mataPelajaran()->aktif()->get();

        return view('guru.bank_soal.create', compact('mapelGuru'));
    }

    /**
     * Simpan soal baru ke database.
     * Logic berbeda untuk setiap tipe soal.
     */
    public function store(StoreSoalRequest $request)
    {
        // Upload gambar jika ada
        $pathGambar = null;
        if ($request->hasFile('gambar')) {
            $pathGambar = $request->file('gambar')->store('soal/gambar', 'public');
        }

        // Simpan soal utama
        $soal = BankSoal::create([
            'guru_id'           => Auth::id(),
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'pertanyaan'        => $request->pertanyaan,
            'tipe_soal'         => $request->tipe_soal,
            'gambar'            => $pathGambar,
            'tingkat_kesulitan' => $request->tingkat_kesulitan,
            'bobot_nilai'       => $request->bobot_nilai,
            'kunci_essay'       => $request->kunci_essay,
            'is_aktif'          => true,
        ]);

        // Simpan pilihan jawaban untuk soal Pilihan Ganda
        if ($request->tipe_soal === 'pg') {
            $labelPilihan = ['A', 'B', 'C', 'D', 'E'];
            foreach ($request->pilihan as $index => $pilihan) {
                PilihanJawaban::create([
                    'soal_id'      => $soal->id,
                    'label'        => $labelPilihan[$index],
                    'teks_pilihan' => $pilihan['teks'],
                    'is_benar'     => ($index == $request->jawaban_benar),
                ]);
            }
        }

        // Simpan pilihan jawaban untuk soal Benar/Salah
        if ($request->tipe_soal === 'bs') {
            PilihanJawaban::create([
                'soal_id'      => $soal->id,
                'label'        => 'Benar',
                'teks_pilihan' => 'Benar',
                'is_benar'     => ($request->jawaban_bs === 'benar'),
            ]);
            PilihanJawaban::create([
                'soal_id'      => $soal->id,
                'label'        => 'Salah',
                'teks_pilihan' => 'Salah',
                'is_benar'     => ($request->jawaban_bs === 'salah'),
            ]);
        }

        // Essay tidak punya pilihan jawaban

        return redirect()->route('guru.bank-soal.index')
            ->with('success', 'Soal berhasil ditambahkan ke bank soal.');
    }

    /** Tampilkan detail soal */
    public function show(BankSoal $bankSoal)
    {
        $this->authorizeGuru($bankSoal);

        $bankSoal->load(['mataPelajaran', 'pilihanJawaban', 'guru']);

        return view('guru.bank_soal.show', compact('bankSoal'));
    }

    /** Tampilkan form edit soal */
    public function edit(BankSoal $bankSoal)
    {
        $this->authorizeGuru($bankSoal);

        $mapelGuru = Auth::user()->mataPelajaran()->aktif()->get();
        $bankSoal->load('pilihanJawaban');

        return view('guru.bank_soal.edit', compact('bankSoal', 'mapelGuru'));
    }

    /** Update soal yang sudah ada */
    public function update(UpdateSoalRequest $request, BankSoal $bankSoal)
    {
        $this->authorizeGuru($bankSoal);

        // Handle upload gambar baru jika ada
        $pathGambar = $bankSoal->gambar;
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama dari storage
            if ($bankSoal->gambar) {
                Storage::disk('public')->delete($bankSoal->gambar);
            }
            $pathGambar = $request->file('gambar')->store('soal/gambar', 'public');
        }

        // Update data soal utama (tipe_soal tidak bisa diubah)
        $bankSoal->update([
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'pertanyaan'        => $request->pertanyaan,
            'gambar'            => $pathGambar,
            'tingkat_kesulitan' => $request->tingkat_kesulitan,
            'bobot_nilai'       => $request->bobot_nilai,
            'kunci_essay'       => $request->kunci_essay,
        ]);

        // Update pilihan jawaban soal Pilihan Ganda
        if ($bankSoal->tipe_soal === 'pg') {
            $bankSoal->pilihanJawaban()->delete();
            $labelPilihan = ['A', 'B', 'C', 'D', 'E'];
            foreach ($request->pilihan as $index => $pilihan) {
                PilihanJawaban::create([
                    'soal_id'      => $bankSoal->id,
                    'label'        => $labelPilihan[$index],
                    'teks_pilihan' => $pilihan['teks'],
                    'is_benar'     => ($index == $request->jawaban_benar),
                ]);
            }
        }

        // Update pilihan jawaban soal Benar/Salah
        if ($bankSoal->tipe_soal === 'bs') {
            $bankSoal->pilihanJawaban()->delete();
            PilihanJawaban::create([
                'soal_id'      => $bankSoal->id,
                'label'        => 'Benar',
                'teks_pilihan' => 'Benar',
                'is_benar'     => ($request->jawaban_bs === 'benar'),
            ]);
            PilihanJawaban::create([
                'soal_id'      => $bankSoal->id,
                'label'        => 'Salah',
                'teks_pilihan' => 'Salah',
                'is_benar'     => ($request->jawaban_bs === 'salah'),
            ]);
        }

        return redirect()->route('guru.bank-soal.index')
            ->with('success', 'Soal berhasil diperbarui.');
    }

    /** Hapus soal beserta gambarnya */
    public function destroy(BankSoal $bankSoal)
    {
        $this->authorizeGuru($bankSoal);

        // Hapus gambar dari storage jika ada
        if ($bankSoal->gambar) {
            Storage::disk('public')->delete($bankSoal->gambar);
        }

        BankSoal::destroy($bankSoal->id);

        return redirect()->route('guru.bank-soal.index')
            ->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Pastikan guru hanya bisa akses soal miliknya sendiri.
     * Super admin bisa akses semua soal.
     */
    private function authorizeGuru(BankSoal $bankSoal): void
    {
        if (Auth::user()->role !== 'super_admin' && $bankSoal->guru_id !== Auth::id()) {
            abort(403, 'Kamu tidak memiliki akses ke soal ini.');
        }
    }

    /**
     * Tampilkan form import soal via Excel
     */
    public function formImport()
    {
        // Guru hanya bisa import ke mapel yang diajarnya
        $mapelGuru = Auth::user()->mataPelajaran()->aktif()->get();

        return view('guru.bank_soal.import', compact('mapelGuru'));
    }

    /**
     * Proses upload dan import file Excel soal
     */
    public function prosesImport(Request $request)
    {
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'file_excel'        => 'required|mimes:xlsx,xls|max:5120', // maks 5MB
        ], [
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'file_excel.required'        => 'File Excel wajib diupload.',
            'file_excel.mimes'           => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'             => 'Ukuran file maksimal 5MB.',
        ]);

        // Pastikan guru hanya bisa import ke mapel yang diajarnya
        $mapelGuru = Auth::user()->mataPelajaran()->pluck('mata_pelajaran.id')->toArray();
        if (!in_array((int) $request->mata_pelajaran_id, $mapelGuru)) {
            return back()->with('error', 'Kamu tidak mengajar mata pelajaran ini.');
        }

        // Jalankan import
        $import = new \App\Imports\SoalImport(
            guruId:          Auth::id(),
            mataPelajaranId: (int) $request->mata_pelajaran_id
        );

        \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file_excel'));

        // Tampilkan hasil import
        $pesan = "Import selesai: {$import->berhasil} soal berhasil, {$import->gagal} gagal.";

        if (!empty($import->errorLog)) {
            session(['import_errors' => $import->errorLog]);
        }

        $tipe = $import->gagal > 0 ? 'warning' : 'success';

        return redirect()->route('guru.bank-soal.index')->with($tipe, $pesan);
    }

    /**
     * Download template Excel untuk import soal
     */
    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TemplateSoalExport(),
            'template_import_soal.xlsx'
        );
    }
}
