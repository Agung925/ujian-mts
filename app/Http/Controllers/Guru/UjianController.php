<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\Ujian;
use App\Models\UjianSoal;
use App\Services\UjianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UjianController extends Controller
{
    public function __construct(private UjianService $ujianService) {}

    /** Daftar semua ujian milik guru yang login */
    public function index()
    {
        $ujian = Ujian::milikGuru(Auth::id())
                      ->with(['mataPelajaran', 'kelas'])
                      ->withCount('soal')
                      ->latest()
                      ->paginate(15);

        return view('guru.ujian.index', compact('ujian'));
    }

    /** Form buat ujian baru */
    public function create()
    {
        // Guru hanya bisa pilih mapel yang diajarnya
        $mapelGuru  = Auth::user()->mataPelajaran()->aktif()->get();
        $tahunAktif = TahunAjaran::aktif()->first();
        $kelasList  = Kelas::aktif()
                           ->where('tahun_ajaran_id', $tahunAktif?->id)
                           ->orderBy('tingkat')
                           ->get();

        return view('guru.ujian.create', compact('mapelGuru', 'kelasList'));
    }

    /** Simpan ujian baru */
    public function store(Request $request)
    {
        $request->validate([
            'judul'             => 'required|string|max:200',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id'          => 'required|exists:kelas,id',
            'durasi_menit'      => 'required|integer|min:5|max:240',
            'acak_soal'         => 'boolean',
            'acak_jawaban'      => 'boolean',
            'deskripsi'         => 'nullable|string',
        ], [
            'judul.required'             => 'Judul ujian wajib diisi.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'kelas_id.required'          => 'Kelas wajib dipilih.',
            'durasi_menit.min'           => 'Durasi minimal 5 menit.',
            'durasi_menit.max'           => 'Durasi maksimal 240 menit (4 jam).',
        ]);

        // Ambil kode mapel untuk generate token
        $mapel = MataPelajaran::find($request->mata_pelajaran_id);
        $token = Ujian::generateToken($mapel->kode_mapel);

        Ujian::create([
            'judul'             => $request->judul,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'kelas_id'          => $request->kelas_id,
            'guru_id'           => Auth::id(),
            'durasi_menit'      => $request->durasi_menit,
            'token'             => $token,
            'status'            => 'draft',
            'acak_soal'         => $request->boolean('acak_soal', true),
            'acak_jawaban'      => $request->boolean('acak_jawaban', true),
            'deskripsi'         => $request->deskripsi,
        ]);

        return redirect()->route('guru.ujian.index')
            ->with('success', "Ujian berhasil dibuat! Token: {$token}");
    }

    /** Detail ujian + daftar soal yang sudah dipilih */
    public function show(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        $ujian->load(['mataPelajaran', 'kelas', 'soal.pilihanJawaban', 'sesiUjian.siswa']);

        // Soal dari bank soal guru ini yang belum masuk ujian
        $soalTersedia = BankSoal::milikGuru(Auth::id())
                                ->mapel($ujian->mata_pelajaran_id)
                                ->aktif()
                                ->whereNotIn('id', $ujian->soal->pluck('id'))
                                ->get();

        // Total soal tersedia untuk mode random
        $totalSoalTersedia = BankSoal::milikGuru(Auth::id())
                                     ->mapel($ujian->mata_pelajaran_id)
                                     ->aktif()
                                     ->count();

        return view('guru.ujian.show', compact('ujian', 'soalTersedia', 'totalSoalTersedia'));
    }

    /** Form edit ujian (hanya saat masih draft) */
    public function edit(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        if (!$ujian->isDraft()) {
            return redirect()->route('guru.ujian.show', $ujian)
                ->with('error', 'Ujian yang sudah aktif atau selesai tidak bisa diedit.');
        }

        $mapelGuru  = Auth::user()->mataPelajaran()->aktif()->get();
        $tahunAktif = TahunAjaran::aktif()->first();
        $kelasList  = Kelas::aktif()
                           ->where('tahun_ajaran_id', $tahunAktif?->id)
                           ->orderBy('tingkat')
                           ->get();

        return view('guru.ujian.edit', compact('ujian', 'mapelGuru', 'kelasList'));
    }

    /** Update data ujian */
    public function update(Request $request, Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        if (!$ujian->isDraft()) {
            return redirect()->route('guru.ujian.show', $ujian)
                ->with('error', 'Ujian tidak bisa diedit karena sudah aktif atau selesai.');
        }

        $request->validate([
            'judul'        => 'required|string|max:200',
            'durasi_menit' => 'required|integer|min:5|max:240',
            'deskripsi'    => 'nullable|string',
        ]);

        $ujian->update($request->only(['judul', 'durasi_menit', 'deskripsi', 'acak_soal', 'acak_jawaban']));

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', 'Ujian berhasil diperbarui.');
    }

    /** Hapus ujian (hanya saat masih draft) */
    public function destroy(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        if (!$ujian->isDraft()) {
            return redirect()->route('guru.ujian.index')
                ->with('error', 'Ujian yang sudah aktif tidak bisa dihapus.');
        }

        Ujian::destroy($ujian->id);

        return redirect()->route('guru.ujian.index')
            ->with('success', 'Ujian berhasil dihapus.');
    }

    /** Tambah soal ke ujian secara manual */
    public function tambahSoalManual(Request $request, Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        $request->validate([
            'soal_ids'   => 'required|array|min:1',
            'soal_ids.*' => 'exists:bank_soal,id',
        ], [
            'soal_ids.required' => 'Pilih minimal 1 soal untuk ditambahkan.',
        ]);

        $this->ujianService->tambahSoalManual($ujian, $request->soal_ids);

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', count($request->soal_ids) . ' soal berhasil ditambahkan ke ujian.');
    }

    /** Tambah soal ke ujian secara random */
    public function tambahSoalRandom(Request $request, Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        $request->validate([
            'jumlah_soal' => 'required|integer|min:1|max:100',
        ], [
            'jumlah_soal.required' => 'Jumlah soal wajib diisi.',
            'jumlah_soal.min'      => 'Minimal 1 soal.',
        ]);

        $jumlahDitambahkan = $this->ujianService->tambahSoalRandom($ujian, $request->jumlah_soal);

        if ($jumlahDitambahkan === 0) {
            return redirect()->route('guru.ujian.show', $ujian)
                ->with('error', 'Tidak ada soal tersedia di bank soal untuk mapel ini.');
        }

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', "{$jumlahDitambahkan} soal berhasil dipilih secara random.");
    }

    /** Hapus soal dari ujian */
    public function hapusSoal(Ujian $ujian, int $soalId)
    {
        $this->authorizeGuru($ujian);

        if (!$ujian->isDraft()) {
            return redirect()->route('guru.ujian.show', $ujian)
                ->with('error', 'Soal tidak bisa dihapus dari ujian yang sudah aktif.');
        }

        UjianSoal::where('ujian_id', $ujian->id)
                 ->where('soal_id', $soalId)
                 ->delete();

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', 'Soal berhasil dihapus dari ujian.');
    }

    /** Buka ujian — ubah status ke aktif */
    public function buka(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        try {
            $this->ujianService->bukaUjian($ujian);
        } catch (\Exception $e) {
            return redirect()->route('guru.ujian.show', $ujian)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', "Ujian berhasil dibuka! Token: {$ujian->token}");
    }

    /** Tutup ujian — ubah status ke selesai */
    public function tutup(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        $this->ujianService->tutupUjian($ujian);

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', 'Ujian berhasil ditutup. Semua jawaban siswa sudah diproses.');
    }

    /** Helper: pastikan guru hanya akses ujian miliknya */
    private function authorizeGuru(Ujian $ujian): void
    {
        if (Auth::user()->role !== 'super_admin' && $ujian->guru_id !== Auth::id()) {
            abort(403, 'Kamu tidak memiliki akses ke ujian ini.');
        }
    }
}
