<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Akumulasi Jam Mengajar Guru & Staf TU</title>
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

        /* Style khusus untuk form filter di Web (tidak ikut tercetak di PDF) */
        .web-filter-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .web-filter-box input { padding: 8px; font-size: 11px; border: 1px solid #cbd5e1; border-radius: 4px; margin-right: 10px; }
        .web-filter-box button, .web-filter-box a { padding: 8px 14px; background: #064e3b; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 11px; border: none; cursor: pointer; display: inline-block; }
        .web-filter-box a { background: #0284c7; margin-left: 5px; }
    </style>
</head>
<body>

    <!-- FORM FILTER RENTANG TANGGAL (Hanya tampil di Web, otomatis disembunyikan saat PDF) -->
    @if(!isset($isPdf))
    <div class="web-filter-box">
        <form method="GET" action="{{ route('admin.rekap.bulanan.guru') }}">
            <label><strong>Dari:</strong></label>
            <input type="date" name="start_date" value="{{ $startDate }}">
            <label><strong>Sampai:</strong></label>
            <input type="date" name="end_date" value="{{ $endDate }}">
            <button type="submit">🔍 Filter Tanggal</button>
            <a href="{{ route('admin.rekap.bulanan.guru.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}">📥 Download PDF</a>
            <a href="{{ route('admin.rekap') }}" style="background: #64748b;">← Kembali</a>
        </form>
    </div>
    @endif

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <h2>{{ \App\Models\Setting::getVal('school_foundation', 'Yayasan Pendidikan SMK Hijau Muda') }}</h2>
        <h1>{{ \App\Models\Setting::getVal('school_name', 'SMK Hijau Muda') }}</h1>
        <p>{{ \App\Models\Setting::getVal('school_tagline', 'Portal Sistem Informasi Presensi Terpadu') }}</p>
        <p>Alamat: {{ \App\Models\Setting::getVal('school_address', 'Jl. Pendidikan No. 1') }}</p>
    </div>

    <!-- JUDUL -->
    <div class="judul-laporan">
        <h3>Laporan Akumulasi Kehadiran Guru & Staf Tenaga Kependidikan (TU)</h3>
        <p>Rentang Tanggal: {{ $startDate }} s.d {{ $endDate }}</p>
    </div>

    <!-- METADATA -->
    <table class="meta-table">
        <tr>
            <td width="20%"><strong>Kategori</strong></td>
            <td width="30%">: Akumulasi Kehadiran & JP Guru/TU</td>
            <td width="25%"><strong>Total Pegawai</strong></td>
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
                <th width="6%" class="text-center">No</th>
                <th width="34%">Nama & Gelar</th>
                <th width="15%" class="text-center">Peran / Jabatan</th>
                <th width="12%" class="text-center">Total Hadir</th>
                <th width="13%" class="text-center">Tepat Waktu</th>
                <th width="20%" class="text-center">Akumulasi Jam (JP)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapPerGuru as $index => $data)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $data['person']->name }}</strong></td>
                <td class="text-center">
                    @if($data['role_type'] == 'Guru')
                        <span style="color: #064e3b; font-weight: bold;">GURU</span>
                    @else
                        <span style="color: #0284c7; font-weight: bold;">{{ $data['person']->position ?? 'STAF TU' }}</span>
                    @endif
                </td>
                <td class="text-center">{{ $data['total_hadir'] }} Hari</td>
                <td class="text-center">{{ $data['tepat_waktu'] }}</td>
                <td class="text-center">
                    @if($data['role_type'] == 'Guru')
                        <strong>+{{ $data['total_jp'] }} JP</strong>
                    @else
                        <span style="color: #64748b;">0 JP</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 15px; color: #64748b;">
                    Tidak ada data rekapitulasi pada periode ini.
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
                    <p><strong>Kepala Sekolah</strong></p>
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