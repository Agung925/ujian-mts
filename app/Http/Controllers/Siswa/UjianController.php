<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Services\UjianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UjianController extends Controller
{
    public function __construct(private UjianService $ujianService) {}

    /**
     * Proses masuk ujian — validasi token, buat/lanjutkan sesi siswa
     */
    public function masuk(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:20',
        ], [
            'token.required' => 'Token ujian wajib diisi.',
        ]);

        // Cari ujian berdasarkan token
        $ujian = Ujian::where('token', strtoupper(trim($request->token)))
                      ->where('status', 'aktif')
                      ->with('kelas')
                      ->first();

        if (!$ujian) {
            return back()->with('error', 'Token tidak valid atau ujian belum dibuka.');
        }

        // Pastikan siswa terdaftar di kelas yang dituju ujian ini
        $kelasIds = Auth::user()->kelas()->pluck('kelas.id')->toArray();
        if (!in_array($ujian->kelas_id, $kelasIds)) {
            return back()->with('error', 'Kamu tidak terdaftar di kelas untuk ujian ini.');
        }

        // Cek apakah sudah ada sesi yang sedang berjalan atau selesai
        $sesiAda = $ujian->sesiUjian()
                         ->where('siswa_id', Auth::id())
                         ->first();

        if ($sesiAda?->status === 'selesai') {
            return back()->with('error', 'Kamu sudah menyelesaikan ujian ini sebelumnya.');
        }

        // Buat sesi baru jika belum ada
        if (!$sesiAda) {
            $sesiAda = $this->ujianService->buatSesiSiswa($ujian, Auth::id());
        }

        // Redirect ke ruang ujian (akan diimplementasikan di Step 6)
        return redirect()->route('siswa.ujian.ruang', $sesiAda->id);
    }

    /**
     * Ruang ujian — halaman siswa mengerjakan soal
     * (Placeholder — detail diimplementasikan di Step 6)
     */
    public function ruang(int $sesiId)
    {
        $sesi = \App\Models\SesiUjian::where('id', $sesiId)
                                      ->where('siswa_id', Auth::id())
                                      ->with(['ujian.soal.pilihanJawaban', 'ujian.mataPelajaran', 'jawabanSiswa'])
                                      ->firstOrFail();

        if ($sesi->status === 'selesai') {
            return redirect()->route('siswa.dashboard')
                ->with('info', 'Ujian sudah selesai.');
        }

        // Susun soal sesuai urutan acak milik siswa ini
        $soalUrut = collect($sesi->urutan_soal)->map(function ($soalId) use ($sesi) {
            return $sesi->ujian->soal->firstWhere('id', $soalId);
        })->filter()->values();

        return view('siswa.ujian.ruang', compact('sesi', 'soalUrut'));
    }

    /**
     * Simpan jawaban siswa (AJAX / form submit per soal)
     */
    public function jawab(Request $request, int $sesiId)
    {
        $sesi = \App\Models\SesiUjian::where('id', $sesiId)
                                      ->where('siswa_id', Auth::id())
                                      ->firstOrFail();

        if ($sesi->status === 'selesai' || $sesi->isWaktuHabis()) {
            return response()->json(['pesan' => 'Waktu ujian telah habis.'], 422);
        }

        $request->validate([
            'soal_id'        => 'required|exists:bank_soal,id',
            'pilihan_id'     => 'nullable|exists:pilihan_jawaban,id',
            'jawaban_essay'  => 'nullable|string',
        ]);

        // Simpan atau update jawaban siswa untuk soal ini
        \App\Models\JawabanSiswa::updateOrCreate(
            ['sesi_id' => $sesi->id, 'soal_id' => $request->soal_id],
            [
                'pilihan_id'    => $request->pilihan_id,
                'jawaban_essay' => $request->jawaban_essay,
            ]
        );

        return response()->json(['berhasil' => true]);
    }

    /**
     * Submit ujian — siswa selesai mengerjakan
     */
    public function submit(int $sesiId)
    {
        $sesi = \App\Models\SesiUjian::where('id', $sesiId)
                                      ->where('siswa_id', Auth::id())
                                      ->where('status', 'sedang')
                                      ->firstOrFail();

        $this->ujianService->submitJawaban($sesi);

        return redirect()->route('siswa.ujian.hasil', $sesiId)
            ->with('success', 'Ujian berhasil dikumpulkan!');
    }

    /**
     * Halaman hasil ujian siswa setelah selesai
     */
    public function hasil(int $sesiId)
    {
        $sesi = \App\Models\SesiUjian::where('id', $sesiId)
                                      ->where('siswa_id', Auth::id())
                                      ->with(['ujian.mataPelajaran'])
                                      ->firstOrFail();

        // Hanya tampilkan halaman konfirmasi selesai — nilai & pembahasan hanya untuk guru
        return view('siswa.ujian.hasil', compact('sesi'));
    }
}
