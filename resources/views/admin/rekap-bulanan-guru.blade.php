@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Kop Surat Resmi (Hanya Muncul Saat Cetak/Download PDF) -->
    <div style="display: none;" class="print:block" id="pdf-header">
        <div style="text-align: center; border-bottom: 3px double #0f172a; padding-bottom: 10px; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 13px; font-weight: bold; text-transform: uppercase; color: #0f172a;">{{ \App\Models\Setting::getVal('school_foundation', 'Yayasan Pendidikan SMK Hijau Muda') }}</h2>
            <h1 style="margin: 3px 0; font-size: 16px; font-weight: bold; text-transform: uppercase; color: #064e3b;">{{ \App\Models\Setting::getVal('school_name', 'SMK Hijau Muda') }}</h1>
            <p style="margin: 2px 0; font-size: 9px; color: #475569;">{{ \App\Models\Setting::getVal('school_tagline', 'Portal Sistem Informasi Presensi Terpadu') }}</p>
            <p style="margin: 2px 0; font-size: 9px; color: #475569;">Alamat: {{ \App\Models\Setting::getVal('school_address', 'Jl. Pendidikan No. 1') }}</p>
        </div>
        <div style="text-align: center; margin-bottom: 15px;">
            <h3 style="margin: 0; font-size: 13px; text-transform: uppercase; text-decoration: underline; color: #0f172a;">Laporan Akumulasi Jam Mengajar Guru (Periode Kustom)</h3>
            <p style="margin: 3px 0; font-size: 10px; color: #64748b;">Rentang Tanggal: {{ $startDate }} s.d {{ $endDate }}</p>
        </div>
    </div>

    <!-- Header Web (Disembunyikan saat cetak PDF) -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm gap-4 print:hidden">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-900/50 rounded-full mb-2">
                <span class="w-2 h-2 bg-emerald-600 rounded-full animate-ping"></span>
                <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 tracking-wider uppercase">SMK Hijau Muda</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Akumulasi Jam Mengajar Guru (Periode Kustom)</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Laporan rekapitulasi presensi dan total Jam Pelajaran (JP) berdasarkan rentang tanggal.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.rekap') }}" 
               class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-sm transition flex items-center gap-2">
                <span>←</span> Kembali ke Harian
            </a>
            <a href="{{ route('admin.rekap.bulanan.guru.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-sm transition shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                <span>📥</span> Download PDF Periode Ini
            </a>
        </div>
    </div>

    <!-- Filter Bar Rentang Tanggal (Disembunyikan saat cetak PDF) -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm print:hidden">
        <form method="GET" action="{{ route('admin.rekap.bulanan.guru') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Dari Tanggal:</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold outline-none text-slate-900 dark:text-white cursor-pointer">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Sampai Tanggal:</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold outline-none text-slate-900 dark:text-white cursor-pointer">
            </div>
            <div>
                <button type="submit" class="px-6 py-3 bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-md">
                    🔍 Terapkan Periode
                </button>
            </div>
        </form>
    </div>

    <!-- ================= TABEL AKUMULASI GURU ================= -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center print:hidden">
            <div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Hasil Akumulasi Jam Mengajar Guru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Rentang Periode: <strong>{{ $startDate }}</strong> s.d <strong>{{ $endDate }}</strong></p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 rounded-full">Total: {{ count($rekapPerGuru) }} Guru</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="p-4 font-semibold w-16 text-center" style="border: 1px solid #cbd5e1;">No</th>
                        <th class="p-4 font-semibold" style="border: 1px solid #cbd5e1;">Nama & Gelar Guru</th>
                        <th class="p-4 font-semibold text-center" style="border: 1px solid #cbd5e1;">Total Hadir</th>
                        <th class="p-4 font-semibold text-center" style="border: 1px solid #cbd5e1;">Tepat Waktu</th>
                        <th class="p-4 font-semibold text-center" style="border: 1px solid #cbd5e1;">Terlambat</th>
                        <th class="p-4 font-semibold text-center" style="border: 1px solid #cbd5e1;">Akumulasi Jam (JP)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($rekapPerGuru as $index => $data)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="p-4 text-center text-slate-500 font-mono" style="border: 1px solid #cbd5e1;">{{ $index + 1 }}</td>
                        <td class="p-4 font-bold text-slate-900 dark:text-white" style="border: 1px solid #cbd5e1;">{{ $data['teacher']->name }}</td>
                        <td class="p-4 text-center font-bold text-slate-800 dark:text-slate-200" style="border: 1px solid #cbd5e1;">{{ $data['total_hadir'] }} Hari</td>
                        <td class="p-4 text-center" style="border: 1px solid #cbd5e1;">
                            <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-full">
                                {{ $data['tepat_waktu'] }}
                            </span>
                        </td>
                        <td class="p-4 text-center" style="border: 1px solid #cbd5e1;">
                            <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-bold rounded-full">
                                {{ $data['terlambat'] }}
                            </span>
                        </td>
                        <td class="p-4 text-center" style="border: 1px solid #cbd5e1;">
                            <span class="px-3.5 py-1.5 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 rounded-xl font-mono font-extrabold text-sm shadow-sm">
                                +{{ $data['total_jp'] }} JP
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400" style="border: 1px solid #cbd5e1;">Belum ada data kehadiran guru pada rentang tanggal tersebut.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tanda Tangan Resmi (Hanya Muncul Saat PDF) -->
    <div style="display: none; margin-top: 30px;" class="print:block">
        <table style="width: 100%; font-size: 11px;">
            <tr>
                <td style="width: 50%; text-align: left; vertical-align: top;">
                    <p>Mengetahui,</p>
                    <p><strong>Kepala {{ \App\Models\Setting::getVal('school_name', 'SMK Hijau Muda') }}</strong></p>
                    <div style="height: 55px;"></div>
                    <p><strong><u>{{ \App\Models\Setting::getVal('headmaster_name', 'Kepala Sekolah') }}</u></strong></p>
                    <p>NIP. {{ \App\Models\Setting::getVal('headmaster_nip', '-') }}</p>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <p>{{ \App\Models\Setting::getVal('city_location', 'Jakarta') }}, {{ date('d-m-Y') }}</p>
                    <p><strong>Administrator / Kurikulum</strong></p>
                    <div style="height: 55px;"></div>
                    <p><strong><u>(..................................)</u></strong></p>
                    <p>NIP. -</p>
                </td>
            </tr>
        </table>
    </div>
</div>
@endsection