<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekap Bulanan Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #047857; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 16px; color: #047857; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #047857; color: #ffffff; font-size: 10px; text-transform: uppercase; }
        td { font-size: 10px; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; float: right; text-align: center; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SMK Hijau Muda</h2>
        <p>Laporan Akumulasi Kehadiran Bulanan / Periode Siswa</p>
        <p>Periode: {{ $startDate }} s/d {{ $endDate }} {{ isset($kelasFilter) && $kelasFilter ? '• Kelas: ' . $kelasFilter : '' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th class="text-center">Tepat Waktu</th>
                <th class="text-center">Terlambat</th>
                <th class="text-center">Total Hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapPerSiswa as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><b>{{ $item['student']->name }}</b></td>
                <td>{{ $item['student']->class_name }}</td>
                <td class="text-center">{{ $item['tepat_waktu'] }}</td>
                <td class="text-center">{{ $item['terlambat'] }}</td>
                <td class="text-center"><b>{{ $item['total_hadir'] }}</b></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Mengetahui,</p>
        <p><b>Wali Kelas</b></p>
        <br><br><br>
        <p><b>( _________________________ )</b></p>
    </div>
</body>
</html>