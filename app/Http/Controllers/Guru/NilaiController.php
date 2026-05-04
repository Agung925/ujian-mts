<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Exports\NilaiUjianExport;
use App\Models\JawabanSiswa;
use App\Models\Ujian;
use App\Models\SesiUjian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class NilaiController extends Controller
{
    /**
     * Halaman rekap nilai — daftar semua siswa beserta nilai untuk satu ujian
     */
    public function rekapNilai(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        // Ambil semua sesi yang sudah selesai, urutkan dari nilai tertinggi
        $sesiList = SesiUjian::where('ujian_id', $ujian->id)
            ->with('siswa')
            ->where('status', 'selesai')
            ->orderByDesc('nilai_akhir')
            ->get();

        // Hitung statistik dasar
        $totalPeserta  = $sesiList->count();
        $rataRata      = $totalPeserta > 0 ? round($sesiList->avg('nilai_akhir'), 1) : 0;
        $nilaiTertinggi = $totalPeserta > 0 ? $sesiList->max('nilai_akhir') : 0;
        $nilaiTerendah  = $totalPeserta > 0 ? $sesiList->min('nilai_akhir') : 0;

        // Hitung berapa siswa yang lulus (nilai >= 75) dan tidak lulus
        $jumlahLulus      = $sesiList->where('nilai_akhir', '>=', 75)->count();
        $jumlahTidakLulus = $totalPeserta - $jumlahLulus;

        // Cek apakah ada essay yang belum dikoreksi
        $adaEssayBelumDikoreksi = JawabanSiswa::whereHas('sesi', fn ($q) => $q->where('ujian_id', $ujian->id))
            ->whereNotNull('jawaban_essay')
            ->whereNull('is_benar')
            ->exists();

        $ujian->load(['mataPelajaran', 'kelas', 'guru']);

        return view('guru.ujian.rekap_nilai', compact(
            'ujian', 'sesiList',
            'totalPeserta', 'rataRata', 'nilaiTertinggi', 'nilaiTerendah',
            'jumlahLulus', 'jumlahTidakLulus', 'adaEssayBelumDikoreksi'
        ));
    }

    /**
     * Koreksi satu jawaban essay — guru menentukan benar/salah
     * Setelah koreksi, nilai_akhir sesi dihitung ulang
     */
    public function koreksiEssay(Request $request, Ujian $ujian, SesiUjian $sesi)
    {
        $this->authorizeGuru($ujian);

        // Pastikan sesi ini memang milik ujian ini
        abort_if($sesi->ujian_id !== $ujian->id, 404);

        $request->validate([
            'jawaban_id' => 'required|exists:jawaban_siswa,id',
            'is_benar'   => 'required|in:0,1',
        ]);

        // Ambil jawaban dan soal terkait (untuk nilai bobotnya)
        $jawaban = JawabanSiswa::where('id', $request->jawaban_id)
            ->where('sesi_id', $sesi->id) // pastikan jawaban milik sesi ini
            ->firstOrFail();

        $soal  = $jawaban->soal;
        $bobot = $ujian->soal->firstWhere('id', $soal->id)?->pivot->bobot_nilai ?? 1;

        // Tandai benar/salah dan set nilai
        $isBenar = (bool) $request->is_benar;
        $jawaban->update([
            'is_benar' => $isBenar,
            'nilai'    => $isBenar ? $bobot : 0,
        ]);

        // Hitung ulang nilai_akhir sesi berdasarkan semua jawaban
        $this->hitungUlangNilai($sesi, $ujian);

        return redirect()->route('guru.ujian.detail-siswa', [$ujian, $sesi])
            ->with('success', 'Koreksi essay berhasil disimpan. Nilai diperbarui.');
    }

    /**
     * Export nilai semua siswa ke Excel
     */
    public function exportExcel(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        $ujian->load(['mataPelajaran', 'kelas']);

        // Nama file: nilai_[judul-ujian]_[tanggal].xlsx
        $namaFile = 'nilai_' . \Str::slug($ujian->judul) . '_' . now()->format('Ymd') . '.xlsx';

        return Excel::download(new NilaiUjianExport($ujian), $namaFile);
    }

    /**
     * Export nilai semua siswa ke PDF (cetak rapor/daftar nilai)
     */
    public function exportPdf(Ujian $ujian)
    {
        $this->authorizeGuru($ujian);

        $sesiList = SesiUjian::where('ujian_id', $ujian->id)
            ->with('siswa')
            ->where('status', 'selesai')
            ->orderBy('siswa_id')
            ->get();

        $ujian->load(['mataPelajaran', 'kelas', 'guru']);

        // Render PDF menggunakan DomPDF
        $pdf = Pdf::loadView('guru.ujian.cetak_nilai', compact('ujian', 'sesiList'))
                   ->setPaper('a4', 'portrait');

        $namaFile = 'nilai_' . \Str::slug($ujian->judul) . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($namaFile);
    }

    // =============================================
    // PRIVATE HELPER
    // =============================================

    /**
     * Hitung ulang nilai_akhir berdasarkan total nilai jawaban yang benar
     * Rumus: (total nilai benar / total bobot semua soal) * 100
     */
    private function hitungUlangNilai(SesiUjian $sesi, Ujian $ujian): void
    {
        // Total bobot semua soal dalam ujian ini
        $totalBobot = $ujian->soal->sum('pivot.bobot_nilai');

        if ($totalBobot <= 0) return;

        // Total nilai yang didapat siswa
        $totalDapat = $sesi->jawabanSiswa()->sum('nilai');

        // Rumus nilai akhir skala 0–100
        $nilaiAkhir = round(($totalDapat / $totalBobot) * 100, 2);

        $sesi->update(['nilai_akhir' => $nilaiAkhir]);
    }

    /** Helper: pastikan hanya guru pemilik ujian atau super_admin yang bisa akses */
    private function authorizeGuru(Ujian $ujian): void
    {
        if (Auth::user()->role !== 'super_admin' && $ujian->guru_id !== Auth::id()) {
            abort(403, 'Kamu tidak memiliki akses ke ujian ini.');
        }
    }
}
