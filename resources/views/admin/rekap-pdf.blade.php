<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Resmi Rekapitulasi Presensi</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; font-size: 11px; line-height: 1.4; margin: 0; padding: 10px; }
        
        /* Kop Surat Resmi */
        .kop-surat { width: 100%; border-bottom: 3px double #0f172a; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        .kop-surat h1 { margin: 0; font-size: 16px; font-weight: bold; text-transform: uppercase; color: #064e3b; letter-spacing: 0.5px; }
        .kop-surat h2 { margin: 3px 0; font-size: 13px; font-weight: bold; text-transform: uppercase; color: #0f172a; }
        .kop-surat p { margin: 2px 0; font-size: 9px; color: #475569; }

        /* Judul Laporan */
        .judul-laporan { text-align: center; margin-bottom: 15px; }
        .judul-laporan h3 { margin: 0; font-size: 13px; text-transform: uppercase; text-decoration: underline; color: #0f172a; }
        .judul-laporan p { margin: 3px 0; font-size: 10px; color: #64748b; }

        /* Informasi Metadata */
        .meta-table { width: 100%; margin-bottom: 15px; font-size: 11px; }
        .meta-table td { padding: 2px 0; }

        /* Tabel Data Utama */
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.data-table th, table.data-table td { border: 1px solid #94a3b8; padding: 5px 6px; text-align: left; }
        table.data-table th { background-color: #064e3b; color: #ffffff; font-size: 9px; text-transform: uppercase; text-align: center; }
        table.data-table td { font-size: 10px; }
        .text-center { text-align: center; }
        
        /* Badge Status */
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .badge-hadir { background-color: #d1fae5; color: #065f46; border: 1px solid #34d399; }
        .badge-terlambat { background-color: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
        .badge-alpha { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        /* Bagian Tanda Tangan */
        .ttd-container { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .ttd-table { width: 100%; font-size: 11px; }
        .ttd-box { width: 45%; text-align: center; vertical-align: top; }
        .space-ttd { height: 60px; }
    </style>
</head>
<body>

    <!-- KOP SURAT SEKOLAH -->
    <div class="kop-surat">
        <h2>{{ \App\Models\Setting::getVal('school_foundation', 'Yayasan Pendidikan SMK Hijau Muda') }}</h2>
        <h1>{{ \App\Models\Setting::getVal('school_name', 'SMK Hijau Muda') }}</h1>
        <p>{{ \App\Models\Setting::getVal('school_tagline', 'Portal Sistem Informasi Presensi Terpadu (SIFAT)') }}</p>
        <p>Alamat: {{ \App\Models\Setting::getVal('school_address', 'Jl. Pendidikan No. 1') }}</p>
    </div>

    <!-- JUDUL LAPORAN -->
    <div class="judul-laporan">
        <h3>Laporan Resmi Rekapitulasi Presensi {{ $roleFilter == 'Guru' ? 'Guru Terjadwal & Beban Mengajar' : 'Siswa Terdaftar' }}</h3>
        <p>Periode Tanggal: {{ $date }}</p>
    </div>

    <!-- DETAIL INFORMASI / METADATA -->
    <table class="meta-table">
        <tr>
            <td width="20%"><strong>Kategori</strong></td>
            <td width="30%">: Rekapitulasi {{ $roleFilter }}</td>
            <td width="25%"><strong>Total Data</strong></td>
            <td width="25%">: {{ $roleFilter == 'Siswa' ? count($studentRecaps ?? []) . ' Siswa' : count($teacherRecaps ?? []) . ' Guru' }}</td>
        </tr>
        <tr>
            <td><strong>Status Filter</strong></td>
            <td>: {{ $statusFilter ? ucfirst(str_replace('_', ' ', $statusFilter)) : 'Semua Status' }}</td>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ date('d-m-Y H:i') }} WIB</td>
        </tr>
        @if($roleFilter == 'Guru')
        <tr>
            <td><strong>Akumulasi Beban</strong></td>
            <td colspan="3">: <strong>{{ $totalJamGuruAll ?? 0 }} Jam (JP)</strong> total akumulasi mengajar guru yang hadir pada tanggal ini.</td>
        </tr>
        @endif
    </table>

    <!-- TABEL DATA ABSENSI -->
    @if($roleFilter == 'Siswa')
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="42%">Nama Lengkap Siswa</th>
                <th width="20%">Kelas / Rombel</th>
                <th width="13%" class="text-center">Jam Masuk</th>
                <th width="13%" class="text-center">Jam Pulang</th>
                <th width="12%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($studentRecaps ?? [] as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $item['student']->name }}</strong></td>
                <td>{{ $item['student']->class_name }}</td>
                <td class="text-center">{{ $item['attendance']->time_in ?? '-' }}</td>
                <td class="text-center">{{ $item['attendance']->time_out ?? '-' }}</td>
                <td class="text-center">
                    @if($item['status'] == 'hadir')
                        <span class="badge badge-hadir">Tepat Waktu</span>
                    @elseif($item['status'] == 'terlambat')
                        <span class="badge badge-terlambat">Terlambat</span>
                    @else
                        <span class="badge badge-alpha">Belum Absen</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 15px; color: #64748b;">
                    Tidak ada data siswa yang tercatat pada kriteria ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @else
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="37%">Nama Lengkap & Gelar Guru</th>
                <th width="20%">Kategori Pegawai</th>
                <th width="13%" class="text-center">Jam Masuk</th>
                <th width="13%" class="text-center">Jam Pulang</th>
                <th width="12%" class="text-center">Status</th>
                <th width="15%" class="text-center">Jam Mengajar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teacherRecaps ?? [] as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $item['teacher']->name }}</strong></td>
                <td>Staf Pengajar / Guru</td>
                <td class="text-center">{{ $item['attendance']->time_in ?? '-' }}</td>
                <td class="text-center">{{ $item['attendance']->time_out ?? '-' }}</td>
                <td class="text-center">
                    @if($item['status'] == 'hadir')
                        <span class="badge badge-hadir">Tepat Waktu</span>
                    @elseif($item['status'] == 'terlambat')
                        <span class="badge badge-terlambat">Terlambat</span>
                    @else
                        <span class="badge badge-alpha">Belum Absen</span>
                    @endif
                </td>
                <td class="text-center"><strong>+{{ $item['total_jp'] }} JP</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 15px; color: #64748b;">
                    Tidak ada jadwal guru pada hari ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <!-- TANDA TANGAN KEPALA SEKOLAH DINAMIS DARI DB -->
    <div class="ttd-container">
        <table class="ttd-table">
            <tr>
                <td class="ttd-box" style="text-align: left;">
                    <p>Mengetahui,</p>
                    <p><strong>Kepala {{ \App\Models\Setting::getVal('school_name', 'SMK Hijau Muda') }}</strong></p>
                    <div class="space-ttd"></div>
                    <p><strong><u>{{ \App\Models\Setting::getVal('headmaster_name', 'Kepala Sekolah') }}</u></strong></p>
                    <p>NIP. {{ \App\Models\Setting::getVal('headmaster_nip', '-') }}</p>
                </td>
                <td class="ttd-box" style="text-align: right;">
                    <p>Ditetapkan di : {{ \App\Models\Setting::getVal('city_location', 'Jakarta') }}</p>
                    <p>Pada Tanggal : {{ $date }}</p>
                    <p><strong>Administrator / Petugas Piket</strong></p>
                    <div class="space-ttd"></div>
                    <p><strong><u>(..................................)</u></strong></p>
                    <p>NIP. - </p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>