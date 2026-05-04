<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Nilai — {{ $ujian->judul }}</title>
    <style>
        /* Reset & font dasar untuk DomPDF */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
            padding: 20px 30px;
        }

        /* HEADER SURAT */
        .header-surat {
            text-align: center;
            border-bottom: 3px double #16a34a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-surat .nama-sekolah {
            font-size: 15px;
            font-weight: bold;
            color: #15803d;
            letter-spacing: 0.5px;
        }
        .header-surat .sub-header {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        /* INFO UJIAN */
        .info-ujian {
            margin-bottom: 14px;
        }
        .info-ujian table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-ujian td {
            padding: 2px 4px;
            vertical-align: top;
            font-size: 10.5px;
        }
        .info-ujian td:first-child {
            width: 130px;
            font-weight: bold;
            color: #374151;
        }
        .info-ujian td.colon { width: 10px; }

        /* JUDUL TABEL */
        .judul-tabel {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #15803d;
        }

        /* TABEL NILAI */
        .tabel-nilai {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .tabel-nilai thead tr {
            background-color: #16a34a;
            color: #fff;
        }
        .tabel-nilai thead th {
            padding: 6px 8px;
            text-align: center;
            font-size: 10.5px;
            border: 1px solid #15803d;
        }
        .tabel-nilai tbody tr:nth-child(even) {
            background-color: #f0fdf4;
        }
        .tabel-nilai tbody td {
            padding: 5px 8px;
            border: 1px solid #d1d5db;
            font-size: 10.5px;
        }
        .tabel-nilai .col-no { text-align: center; width: 30px; }
        .tabel-nilai .col-nilai { text-align: center; font-weight: bold; }
        .tabel-nilai .col-ket { text-align: center; }
        .lulus { color: #16a34a; }
        .tidak-lulus { color: #dc2626; }

        /* STATISTIK RINGKASAN */
        .ringkasan {
            margin-bottom: 20px;
            font-size: 10.5px;
        }
        .ringkasan table {
            border-collapse: collapse;
        }
        .ringkasan td {
            padding: 2px 8px;
        }
        .ringkasan td:first-child {
            font-weight: bold;
            color: #374151;
        }

        /* TANDA TANGAN */
        .ttd {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        .ttd-block {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: top;
        }
        .ttd-block .label { font-size: 10px; color: #555; margin-bottom: 50px; }
        .ttd-block .garis { border-top: 1px solid #374151; padding-top: 4px; font-size: 10.5px; font-weight: bold; }
        .ttd-block .jabatan { font-size: 9.5px; color: #666; }

        /* FOOTER halaman */
        .footer-cetak {
            margin-top: 14px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    {{-- ============================================================
         HEADER SURAT
    ============================================================ --}}
    <div class="header-surat">
        <div class="nama-sekolah">MADRASAH TSANAWIYAH</div>
        <div class="sub-header">DAFTAR NILAI UJIAN</div>
    </div>

    {{-- ============================================================
         INFORMASI UJIAN
    ============================================================ --}}
    <div class="info-ujian">
        <table>
            <tr>
                <td>Nama Ujian</td>
                <td class="colon">:</td>
                <td>{{ $ujian->judul }}</td>
            </tr>
            <tr>
                <td>Mata Pelajaran</td>
                <td class="colon">:</td>
                <td>{{ $ujian->mataPelajaran->nama_mapel ?? '-' }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td class="colon">:</td>
                <td>{{ $ujian->kelas->nama_kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td>Guru Pengampu</td>
                <td class="colon">:</td>
                <td>{{ $ujian->guru->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td class="colon">:</td>
                <td>{{ now()->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Jumlah Peserta</td>
                <td class="colon">:</td>
                <td>{{ $sesiList->count() }} siswa</td>
            </tr>
        </table>
    </div>

    {{-- ============================================================
         TABEL NILAI
    ============================================================ --}}
    <div class="judul-tabel">DAFTAR NILAI PESERTA UJIAN</div>

    <table class="tabel-nilai">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th style="text-align:left">Nama Siswa</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Nilai</th>
                <th>Keterangan</th>
                <th>Pelanggaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sesiList as $i => $sesi)
            @php
                $nilaiAkhir = $sesi->nilai_akhir;
                $lulus      = $nilaiAkhir !== null && $nilaiAkhir >= 75;
            @endphp
            <tr>
                <td class="col-no">{{ $i + 1 }}</td>
                <td>{{ $sesi->siswa?->name ?? '-' }}</td>
                <td class="col-ket">{{ $sesi->waktu_mulai?->format('H:i') ?? '-' }}</td>
                <td class="col-ket">{{ $sesi->waktu_selesai?->format('H:i') ?? '-' }}</td>
                <td class="col-nilai {{ $lulus ? 'lulus' : 'tidak-lulus' }}">
                    {{ $nilaiAkhir !== null ? number_format($nilaiAkhir, 1) : '-' }}
                </td>
                <td class="col-ket {{ $lulus ? 'lulus' : 'tidak-lulus' }}">
                    {{ $nilaiAkhir !== null ? ($lulus ? 'Lulus' : 'Tidak Lulus') : '-' }}
                </td>
                <td class="col-ket">{{ $sesi->jumlah_pelanggaran ?? 0 }}x</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; color:#9ca3af; padding: 12px;">
                    Belum ada siswa yang menyelesaikan ujian.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ============================================================
         RINGKASAN STATISTIK
    ============================================================ --}}
    @if($sesiList->count() > 0)
    @php
        $rataRata       = round($sesiList->avg('nilai_akhir'), 1);
        $tertinggi      = $sesiList->max('nilai_akhir');
        $terendah       = $sesiList->min('nilai_akhir');
        $jumlahLulus    = $sesiList->where('nilai_akhir', '>=', 75)->count();
        $persen         = round(($jumlahLulus / $sesiList->count()) * 100, 1);
    @endphp
    <div class="ringkasan">
        <table>
            <tr><td>Nilai Rata-rata</td><td>: {{ $rataRata }}</td></tr>
            <tr><td>Nilai Tertinggi</td><td>: {{ $tertinggi }}</td></tr>
            <tr><td>Nilai Terendah</td><td>: {{ $terendah }}</td></tr>
            <tr><td>Jumlah Lulus</td><td>: {{ $jumlahLulus }} siswa ({{ $persen }}%)</td></tr>
        </table>
    </div>
    @endif

    {{-- ============================================================
         TANDA TANGAN
    ============================================================ --}}
    <div class="ttd">
        <div class="ttd-block"></div>
        <div class="ttd-block">
            <div class="label">Mengetahui,<br>Kepala Madrasah</div>
            <div class="garis">___________________________</div>
            <div class="jabatan">NIP. ________________________</div>
        </div>
        <div class="ttd-block">
            <div class="label">{{ now()->translatedFormat('d F Y') }},<br>Guru Pengampu</div>
            <div class="garis">{{ $ujian->guru->name ?? '___________________________' }}</div>
            <div class="jabatan">NIP. ________________________</div>
        </div>
    </div>

    <div class="footer-cetak">
        Dicetak pada {{ now()->format('d/m/Y H:i') }} &mdash; Sistem CBT MTs
    </div>

</body>
</html>
