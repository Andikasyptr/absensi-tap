<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Akumulasi Jam Mengajar Guru</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; font-size: 11px; line-height: 1.4; margin: 0; padding: 10px; }
        .kop-surat { width: 100%; border-bottom: 3px double #0f172a; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        .kop-surat h1 { margin: 0; font-size: 16px; font-weight: bold; text-transform: uppercase; color: #064e3b; }
        .kop-surat h2 { margin: 3px 0; font-size: 13px; font-weight: bold; text-transform: uppercase; color: #0f172a; }
        .kop-surat p { margin: 2px 0; font-size: 9px; color: #475569; }

        .judul-laporan { text-align: center; margin-bottom: 15px; }
        .judul-laporan h3 { margin: 0; font-size: 13px; text-transform: uppercase; text-decoration: underline; color: #0f172a; }
        .judul-laporan p { margin: 3px 0; font-size: 10px; color: #64748b; }

        .meta-table { width: 100%; margin-bottom: 15px; font-size: 11px; }
        .meta-table td { padding: 2px 0; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.data-table th, table.data-table td { border: 1px solid #94a3b8; padding: 6px 8px; text-align: left; }
        table.data-table th { background-color: #064e3b; color: #ffffff; font-size: 9px; text-transform: uppercase; text-align: center; }
        table.data-table td { font-size: 10px; }
        .text-center { text-align: center; }

        .ttd-container { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .ttd-table { width: 100%; font-size: 11px; }
        .ttd-box { width: 45%; text-align: center; vertical-align: top; }
        .space-ttd { height: 60px; }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <h2>{{ \App\Models\Setting::getVal('school_foundation', 'Yayasan Pendidikan SMK Hijau Muda') }}</h2>
        <h1>{{ \App\Models\Setting::getVal('school_name', 'SMK Hijau Muda') }}</h1>
        <p>{{ \App\Models\Setting::getVal('school_tagline', 'Portal Sistem Informasi Presensi Terpadu') }}</p>
        <p>Alamat: {{ \App\Models\Setting::getVal('school_address', 'Jl. Pendidikan No. 1') }}</p>
    </div>

    <!-- JUDUL -->
    <div class="judul-laporan">
        <h3>Laporan Akumulasi Jam Mengajar Guru (Periode Kustom)</h3>
        <p>Rentang Tanggal: {{ $startDate }} s.d {{ $endDate }}</p>
    </div>

    <!-- METADATA -->
    <table class="meta-table">
        <tr>
            <td width="20%"><strong>Kategori</strong></td>
            <td width="30%">: Akumulasi Beban Mengajar Guru</td>
            <td width="25%"><strong>Total Guru</strong></td>
            <td width="25%">: {{ count($rekapPerGuru) }} Orang</td>
        </tr>
        <tr>
            <td><strong>Tanggal Cetak</strong></td>
            <td colspan="3">: {{ date('d-m-Y H:i') }} WIB</td>
        </tr>
    </table>

    <!-- TABEL UTAMA -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="8%" class="text-center">No</th>
                <th width="42%">Nama & Gelar Guru</th>
                <th width="15%" class="text-center">Total Hadir</th>
                <th width="15%" class="text-center">Tepat Waktu</th>
                <th width="20%" class="text-center">Akumulasi Jam (JP)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapPerGuru as $index => $data)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $data['teacher']->name }}</strong></td>
                <td class="text-center">{{ $data['total_hadir'] }} Hari</td>
                <td class="text-center">{{ $data['tepat_waktu'] }}</td>
                <td class="text-center"><strong>+{{ $data['total_jp'] }} JP</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding: 15px; color: #64748b;">
                    Tidak ada data rekapitulasi guru pada periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="ttd-container">
        <table class="ttd-table">
            <tr>
                <td class="ttd-box" style="text-align: left;">
                    <p>Mengetahui,</p>
                    <p><strong>Kepala Sekolah{{ \App\Models\Setting::getVal('school_name', 'SMK Hijau Muda') }}</strong></p>
                    <div class="space-ttd"></div>
                    <p><strong><u>{{ \App\Models\Setting::getVal('headmaster_name', 'Kepala Sekolah') }}</u></strong></p>
                    <p>NIP. {{ \App\Models\Setting::getVal('headmaster_nip', '-') }}</p>
                </td>
                <td class="ttd-box" style="text-align: right;">
                    <p>Ditetapkan di : {{ \App\Models\Setting::getVal('city_location', 'Jakarta') }}</p>
                    <p>Pada Tanggal : {{ date('Y-m-d') }}</p>
                    <p><strong>Wa.Ka Kurikulum</strong></p>
                    <div class="space-ttd"></div>
                    <p><strong><u>(..................................)</u></strong></p>
                    <p>NIP. - </p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>