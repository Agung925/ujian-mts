<?php

namespace App\Exports;

use App\Models\SesiUjian;
use App\Models\Ujian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class NilaiUjianExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function __construct(private Ujian $ujian) {}

    /**
     * Judul sheet Excel
     */
    public function title(): string
    {
        return 'Nilai Ujian';
    }

    /**
     * Baris header kolom
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Waktu Mulai',
            'Waktu Selesai',
            'Durasi (menit)',
            'Nilai Akhir',
            'Keterangan',
            'Pelanggaran',
        ];
    }

    /**
     * Data baris dari sesi ujian yang sudah selesai
     */
    public function collection()
    {
        $sesiList = SesiUjian::where('ujian_id', $this->ujian->id)
            ->with('siswa')
            ->where('status', 'selesai')
            ->orderBy('siswa_id')
            ->get();

        // Ubah ke format baris Excel
        return $sesiList->map(function (SesiUjian $sesi, int $index) {
            // Hitung durasi pengerjaan dalam menit
            $durasi = $sesi->waktu_mulai && $sesi->waktu_selesai
                ? $sesi->waktu_mulai->diffInMinutes($sesi->waktu_selesai)
                : '-';

            // Keterangan lulus/tidak lulus berdasarkan KKM 75
            $keterangan = ($sesi->nilai_akhir !== null && $sesi->nilai_akhir >= 75) ? 'Lulus' : 'Tidak Lulus';

            return [
                $index + 1,
                $sesi->siswa?->name ?? '-',
                $sesi->waktu_mulai?->format('d/m/Y H:i') ?? '-',
                $sesi->waktu_selesai?->format('d/m/Y H:i') ?? '-',
                $durasi,
                $sesi->nilai_akhir !== null ? number_format($sesi->nilai_akhir, 1) : '-',
                $keterangan,
                $sesi->jumlah_pelanggaran ?? 0,
            ];
        });
    }

    /**
     * Gaya tampilan Excel: header bold + background hijau
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            // Baris ke-1 (header): bold, background hijau, teks putih, rata tengah
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '16A34A'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    /**
     * Lebar kolom agar mudah dibaca
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 20,
            'D' => 20,
            'E' => 15,
            'F' => 12,
            'G' => 14,
            'H' => 14,
        ];
    }
}
