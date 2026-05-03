<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateSiswaExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * Isi tabel — hanya satu baris contoh
     */
    public function array(): array
    {
        return [
            ['2024001', 'Nama Siswa Contoh', 'L', '08123456789'],
        ];
    }

    /**
     * Baris header kolom
     */
    public function headings(): array
    {
        return ['nis', 'name', 'jenis_kelamin', 'no_telp'];
    }

    /**
     * Terapkan style pada baris header (tebal & background hijau)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF16A34A']],
            ],
        ];
    }
}
