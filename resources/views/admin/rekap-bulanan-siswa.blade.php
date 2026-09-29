@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm gap-4 print:hidden">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-900/50 rounded-full mb-2">
                <span class="w-2 h-2 bg-emerald-600 rounded-full animate-ping"></span>
                <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 tracking-wider uppercase">SMK Hijau Muda</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Rekap Periode / Bulanan Kehadiran Siswa</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Akumulasi kehadiran tepat waktu dan keterlambatan siswa berdasarkan rentang tanggal.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.rekap') }}" 
               class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white rounded-xl font-bold text-sm transition flex items-center gap-2">
                <span>⬅️</span> Kembali ke Rekap Harian
            </a>
            <a href="{{ route('admin.rekap.bulanan.siswa.pdf') }}?start_date={{ $startDate }}&end_date={{ $endDate }}&class_name={{ $kelasFilter }}" 
               class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-sm transition shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                <span>📥</span> Download PDF Bulanan Siswa
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm print:hidden">
        <form method="GET" action="{{ route('admin.rekap.bulanan.siswa') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Dari Tanggal:</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Sampai Tanggal:</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Filter Kelas:</label>
                <select name="class_name" class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($schoolClasses as $cls)
                        <option value="{{ $cls->name }}" {{ $kelasFilter == $cls->name ? 'selected' : '' }}>{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="w-full py-3 bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-md">
                    🔍 Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Rekap -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Laporan Akumulasi Absensi Siswa ({{ $startDate }} s/d {{ $endDate }})</h3>
            <span class="text-xs text-slate-400">Total Siswa: {{ count($rekapPerSiswa) }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="p-4 font-semibold w-16 text-center">No</th>
                        <th class="p-4 font-semibold">Nama Siswa</th>
                        <th class="p-4 font-semibold">Kelas</th>
                        <th class="p-4 font-semibold text-center">Tepat Waktu</th>
                        <th class="p-4 font-semibold text-center">Terlambat</th>
                        <th class="p-4 font-semibold text-center">Total Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($rekapPerSiswa as $index => $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="p-4 text-center font-mono text-slate-500">{{ $index + 1 }}</td>
                        <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $item['student']->name }}</td>
                        <td class="p-4 text-slate-600 dark:text-slate-300 text-sm">
                            <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 font-medium">
                                {{ $item['student']->class_name }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold rounded-lg text-xs">
                                {{ $item['tepat_waktu'] }} Hari
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 font-bold rounded-lg text-xs">
                                {{ $item['terlambat'] }} Hari
                            </span>
                        </td>
                        <td class="p-4 text-center font-bold text-slate-900 dark:text-white">
                            {{ $item['total_hadir'] }} Hari
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada data rekap siswa pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection