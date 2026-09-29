<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekap Absensi Harian</title>
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

        .badge { padding: 3px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .badge-hadir { background-color: #d1fae5; color: #065f46; }
        .badge-terlambat { background-color: #fef3c7; color: #92400e; }
        .badge-alpha { background-color: #fee2e2; color: #991b1b; }

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
        <h3>Laporan Rekapitulasi Kehadiran Harian ({{ $roleFilter }})</h3>
        <p>Tanggal: {{ $date }}</p>
    </div>

    <!-- METADATA -->
    <table class="meta-table">
        <tr>
            <td width="20%"><strong>Kategori Filter</strong></td>
            <td width="30%">: Rekap Harian {{ $roleFilter }}</td>
            <td width="25%"><strong>Tanggal Cetak</strong></td>
            <td width="25%">: {{ date('d-m-Y H:i') }} WIB</td>
        </tr>
    </table>

    <!-- TABEL UTAMA -->
    @if($roleFilter == 'Siswa')
    <table class="data-table">
        <thead>
            <tr>
                <th width="6%" class="text-center">No</th>
                <th width="34%">Nama Siswa</th>
                <th width="15%" class="text-center">Kelas</th>
                <th width="15%" class="text-center">Waktu Masuk</th>
                <th width="15%" class="text-center">Waktu Pulang</th>
                <th width="15%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($studentRecaps ?? [] as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $item['student']->name }}</strong></td>
                <td class="text-center">{{ $item['student']->class_name }}</td>
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
                <td colspan="6" class="text-center" style="padding: 15px; color: #64748b;">Tidak ada data siswa.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @else
    <table class="data-table">
        <thead>
            <tr>
                <th width="6%" class="text-center">No</th>
                <th width="32%">Nama & Gelar</th>
                <th width="18%" class="text-center">Peran / Jabatan</th>
                <th width="12%" class="text-center">Masuk</th>
                <th width="12%" class="text-center">Pulang</th>
                <th width="10%" class="text-center">Status</th>
                <th width="10%" class="text-center">JP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teacherRecaps ?? [] as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $item['person']->name }}</strong></td>
                <td class="text-center">
                    @if($item['role_type'] == 'Guru')
                        <span style="color: #064e3b; font-weight: bold;">GURU</span>
                    @else
                        <span style="color: #0284c7; font-weight: bold;">{{ $item['person']->position ?? 'STAF TU' }}</span>
                    @endif
                </td>
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
                <td class="text-center">
                    @if($item['role_type'] == 'Guru')
                        <strong>{{ $item['total_jp'] }} JP</strong>
                    @else
                        <span style="color: #64748b;">0 JP</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 15px; color: #64748b;">Tidak ada data guru atau staf TU.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endif

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