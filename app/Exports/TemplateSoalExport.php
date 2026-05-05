<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateSoalExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    /**
     * Data contoh untuk template — 3 baris contoh (PG, BS, Essay)
     */
    public function array(): array
    {
        return [
            // Contoh soal Pilihan Ganda
            [
                'pg',
                'Pancasila terdiri dari berapa sila?',
                'Penilaian Harian',
                'Asesmen Formatif (Harian)',
                '1',
                '3 Sila',
                '4 Sila',
                '5 Sila',
                '6 Sila',
                '',
                'C',
                '',
            ],
            // Contoh soal Benar/Salah
            [
                'bs',
                'Ibu kota Indonesia adalah Jakarta.',
                'Ujian Rutin (Semesteran)',
                'Asesmen Sumatif Tengah Semester (ASTS)',
                '1',
                '',
                '',
                '',
                '',
                '',
                'Benar',
                '',
            ],
            // Contoh soal Essay
            [
                'essay',
                'Jelaskan pengertian Pancasila sebagai dasar negara!',
                'Ujian Akhir Jenjang (Kelas 9)',
                'Asesmen Madrasah (AM)',
                '5',
                '',
                '',
                '',
                '',
                '',
                '',
                'Pancasila adalah dasar negara Indonesia yang terdiri dari 5 sila...',
            ],
        ];
    }

    /**
     * Header kolom Excel — harus sesuai persis dengan yang dibaca SoalImport
     */
    public function headings(): array
    {
        return [
            'tipe_soal',
            'pertanyaan',
            'kategori',
            'sub_kategori',
            'bobot_nilai',
            'pilihan_a',
            'pilihan_b',
            'pilihan_c',
            'pilihan_d',
            'pilihan_e',
            'jawaban_benar',
            'kunci_essay',
        ];
    }

    /**
     * Judul sheet Excel
     */
    public function title(): string
    {
        return 'Template Soal';
    }

    /**
     * Style: header bold hijau, baris contoh diberi warna berbeda
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            // Row 1 = header: bold, background hijau, teks putih
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '16a34a']],
            ],
            // Row 2 = contoh PG (background kuning muda)
            2 => [
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFFDE7']],
            ],
            // Row 3 = contoh BS (background biru muda)
            3 => [
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E3F2FD']],
            ],
            // Row 4 = contoh Essay (background oranye muda)
            4 => [
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFF3E0']],
            ],
        ];
    }

    /**
     * Lebar kolom agar mudah dibaca dan diedit
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // tipe_soal
            'B' => 50,  // pertanyaan
            'C' => 35,  // kategori
            'D' => 45,  // sub_kategori
            'E' => 12,  // bobot_nilai
            'F' => 25,  // pilihan_a
            'G' => 25,  // pilihan_b
            'H' => 25,  // pilihan_c
            'I' => 25,  // pilihan_d
            'J' => 25,  // pilihan_e
            'K' => 15,  // jawaban_benar
            'L' => 50,  // kunci_essay
        ];
    }
}
