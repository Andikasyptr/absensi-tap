@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="rekapManager('{{ $roleFilter ?? 'siswa' }}')">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm gap-4 print:hidden">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-900/50 rounded-full mb-2">
                <span class="w-2 h-2 bg-emerald-600 rounded-full animate-ping"></span>
                <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 tracking-wider uppercase">SMK Hijau Muda</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Rekap Absensi Harian & Jam Mengajar</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Pantau status kehadiran seluruh siswa, guru, & staf TU hari ini secara real-time.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <!-- Tombol Menuju Rekap Periode / Bulanan Siswa -->
            <a href="{{ route('admin.rekap.bulanan.siswa') }}" 
               class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl font-bold text-sm transition shadow-lg shadow-emerald-700/20 flex items-center gap-2">
                <span>📊</span> Siswa (Bulanan)
            </a>
            <!-- Tombol Menuju Rekap Periode / Bulanan Guru & TU -->
            <a href="{{ route('admin.rekap.bulanan.guru') }}" 
               class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white rounded-xl font-bold text-sm transition shadow-lg shadow-sky-600/20 flex items-center gap-2">
                <span>📊</span> Guru & TU (Bulanan)
            </a>
            <!-- Tombol Download Laporan PDF Harian -->
            <a :href="'{{ route('admin.rekap.pdf') }}?role=' + (activeTab === 'siswa' ? 'Siswa' : 'Guru') + '&date={{ $date }}&status={{ request('status') }}&class_name={{ request('class_name') }}'" 
               class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-sm transition shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                <span>📥</span> Download Rekap Harian
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi Flash Message -->
    @if(session('success'))
    <div class="p-4 bg-emerald-600 text-white rounded-2xl font-medium text-sm shadow-lg shadow-emerald-950 flex items-center gap-3 print:hidden">
        <span>✨</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 print:hidden">
        <div class="bg-gradient-to-br from-emerald-600 to-teal-800 p-6 rounded-3xl text-white shadow-lg shadow-emerald-600/20">
            <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider">Total Kehadiran</p>
            <h3 class="text-3xl font-extrabold mt-1">{{ $totalHadir }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Siswa Hadir / Terdaftar</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $totalSiswa }} <span class="text-xs font-normal text-slate-400">/ {{ $totalSiswaTerdaftar ?? 0 }}</span></h3>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Guru & TU Hadir / Total</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $totalGuruHadir }} <span class="text-xs font-normal text-slate-400">/ {{ $totalGuruJadwalHariIni ?? 0 }}</span></h3>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Akumulasi Jam Guru</p>
            <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $totalJamGuruAll ?? 0 }} <span class="text-sm font-normal text-slate-400">JP</span></h3>
        </div>
    </div>

    <!-- Filter Bar & Tab Pilihan (Siswa / Guru & TU) -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col lg:flex-row justify-between gap-4 items-center print:hidden">
        <!-- Tab Navigasi -->
        <div class="flex space-x-2 w-full lg:w-auto">
            <button @click="activeTab = 'siswa'" 
                    :class="activeTab === 'siswa' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'"
                    class="px-6 py-2.5 rounded-xl font-bold text-sm transition flex-1 lg:flex-none">
                👥 Rekap Siswa
            </button>
            <button @click="activeTab = 'guru'" 
                    :class="activeTab === 'guru' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'"
                    class="px-6 py-2.5 rounded-xl font-bold text-sm transition flex-1 lg:flex-none">
                👨‍🏫 Rekap Guru & Staf TU
            </button>
        </div>

        <!-- Filter Tanggal & Status -->
        <form method="GET" action="{{ route('admin.rekap') }}" class="flex flex-wrap gap-2 w-full lg:w-auto items-center">
            <input type="hidden" name="role" x-model="activeTab">
            
            <!-- Filter Kelas -->
            <select name="class_name" x-show="activeTab === 'siswa'" class="p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold outline-none text-slate-800 dark:text-slate-200">
                <option value="">-- Semua Kelas --</option>
                @foreach(\App\Models\SchoolClass::orderBy('name')->get() as $cls)
                    <option value="{{ $cls->name }}" {{ request('class_name') == $cls->name ? 'selected' : '' }}>{{ $cls->name }}</option>
                @endforeach
            </select>

            <!-- Filter Status Kehadiran -->
            <select name="status" class="p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold outline-none text-slate-800 dark:text-slate-200">
                <option value="">-- Semua Status --</option>
                <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Tepat Waktu</option>
                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="belum_absen" {{ request('status') == 'belum_absen' ? 'selected' : '' }}>Belum Absen / Alpha</option>
            </select>

            <!-- Filter Tanggal -->
            <input type="date" name="date" value="{{ $date }}" class="p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold outline-none text-slate-900 dark:text-white cursor-pointer">
            
            <button type="submit" class="px-5 py-2.5 bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition">Terapkan Filter</button>
        </form>
    </div>

    <!-- ================= TABEL KESELURUHAN SISWA ================= -->
    <div x-show="activeTab === 'siswa'" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Status Kehadiran Seluruh Siswa Terdaftar</h3>
            <span class="text-xs text-slate-400">Total Ditampilkan: {{ count($studentRecaps ?? []) }} Siswa</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="p-4 font-semibold">Nama Siswa</th>
                        <th class="p-4 font-semibold">Kelas</th>
                        <th class="p-4 font-semibold text-center">Waktu Masuk</th>
                        <th class="p-4 font-semibold text-center">Waktu Pulang</th>
                        <th class="p-4 font-semibold text-center">Status Kehadiran</th>
                        <th class="p-4 font-semibold text-center">Aksi Record</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($studentRecaps ?? [] as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $item['student']->name }}</td>
                        <td class="p-4 text-slate-600 dark:text-slate-300 text-sm font-medium">
                            <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                                {{ $item['student']->class_name }}
                            </span>
                        </td>
                        <td class="p-4 text-center font-mono text-slate-600 dark:text-slate-300 text-sm">{{ $item['attendance']->time_in ?? '-' }}</td>
                        <td class="p-4 text-center font-mono text-slate-600 dark:text-slate-300 text-sm">{{ $item['attendance']->time_out ?? '-' }}</td>
                        <td class="p-4 text-center">
                            @if($item['status'] == 'hadir')
                                <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-full uppercase">Tepat Waktu</span>
                            @elseif($item['status'] == 'terlambat')
                                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-full uppercase">Terlambat</span>
                            @else
                                <span class="px-3 py-1 bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 text-[10px] font-bold rounded-full uppercase">Belum Absen</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            @if($item['attendance'])
                            <form action="{{ route('admin.rekap.destroy', $item['attendance']->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data absensi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold transition">
                                    🗑️ Hapus Record
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-slate-400 italic">Tanpa Record</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada data siswa yang ditemukan sesuai filter ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= TABEL GURU & STAF TU ================= -->
    <div x-show="activeTab === 'guru'" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors" style="display: none;">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Status Kehadiran Guru & Staf TU Hari Ini</h3>
            <span class="text-xs text-slate-400">Total Terdaftar: {{ count($teacherRecaps ?? []) }} Orang</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="p-4 font-semibold">Nama & Gelar</th>
                        <th class="p-4 font-semibold">Peran / Jabatan</th>
                        <th class="p-4 font-semibold text-center">Waktu Masuk</th>
                        <th class="p-4 font-semibold text-center">Waktu Pulang</th>
                        <th class="p-4 font-semibold text-center">Status Kehadiran</th>
                        <th class="p-4 font-semibold text-center">Beban Mengajar (JP)</th>
                        <th class="p-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($teacherRecaps ?? [] as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $item['person']->name }}</td>
                        <td class="p-4">
                            @if($item['role_type'] == 'Guru')
                                <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-lg uppercase">Guru</span>
                            @else
                                <span class="px-2.5 py-1 bg-sky-50 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-900/50 text-sky-700 dark:text-sky-400 text-[10px] font-bold rounded-lg uppercase">{{ $item['person']->position ?? 'Staf TU' }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-center font-mono text-slate-600 dark:text-slate-300 text-sm">{{ $item['attendance']->time_in ?? '-' }}</td>
                        <td class="p-4 text-center font-mono text-slate-600 dark:text-slate-300 text-sm">{{ $item['attendance']->time_out ?? '-' }}</td>
                        <td class="p-4 text-center">
                            @if($item['status'] == 'hadir')
                                <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-full uppercase">Tepat Waktu</span>
                            @elseif($item['status'] == 'terlambat')
                                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-full uppercase">Terlambat</span>
                            @else
                                <span class="px-3 py-1 bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 text-[10px] font-bold rounded-full uppercase">Belum Absen</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            @if($item['role_type'] == 'Guru')
                                <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 rounded-xl font-mono font-bold text-xs">
                                    +{{ $item['total_jp'] }} JP
                                </span>
                            @else
                                <span class="text-xs font-mono font-bold text-slate-400">0 JP</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            @if($item['attendance'])
                            <form action="{{ route('admin.rekap.destroy', $item['attendance']->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data absensi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold transition">
                                    🗑️ Hapus Record
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-slate-400 italic">Tanpa Record</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">Tidak ada data guru atau staf TU yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function rekapManager(initialTab) {
        return {
            activeTab: initialTab || 'siswa'
        }
    }
</script>
@endsection