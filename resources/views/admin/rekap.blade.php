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
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Laporan presensi lengkap dengan akumulasi jam mengajar guru otomatis.</p>
        </div>
        <div class="flex gap-2">
            <!-- Tombol Download Laporan PDF Resmi (Menggantikan Print Browser) -->
            <a :href="'{{ route('admin.rekap.pdf') }}?role=' + (activeTab === 'siswa' ? 'Siswa' : 'Guru') + '&date={{ $date }}&status={{ request('status') }}'" 
               class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-sm transition shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                <span>📥</span> Download Laporan PDF Resmi
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
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Siswa Hadir</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $totalSiswa }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Guru Hadir</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $totalGuru }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Akumulasi Jam Guru</p>
            <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $totalJamGuruAll ?? 0 }} <span class="text-sm font-normal text-slate-400">JP</span></h3>
        </div>
    </div>

    <!-- Filter Bar & Tab Pilihan (Siswa / Guru) -->
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
                👨‍🏫 Rekap Guru & Jam Mengajar
            </button>
        </div>

        <!-- Filter Tanggal & Status -->
        <form method="GET" action="{{ route('admin.rekap') }}" class="flex flex-wrap gap-2 w-full lg:w-auto items-center">
            <input type="hidden" name="role" x-model="activeTab">
            
            <!-- Filter Status Kehadiran -->
            <select name="status" class="p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold outline-none text-slate-800 dark:text-slate-200">
                <option value="">-- Semua Status --</option>
                <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Tepat Waktu (Hadir)</option>
                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
            </select>

            <!-- Filter Tanggal -->
            <input type="date" name="date" value="{{ $date }}" class="p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold outline-none text-slate-900 dark:text-white cursor-pointer">
            
            <button type="submit" class="px-5 py-2.5 bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition">Terapkan Filter</button>
        </form>
    </div>

    <!-- ================= TABEL SISWA ================= -->
    <div x-show="activeTab === 'siswa'" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Log Kehadiran Siswa</h3>
            <span class="text-xs text-slate-400">Total: {{ $attendances->where('attendable_type', App\Models\Student::class)->count() }} Data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="p-4 font-semibold">Nama Siswa</th>
                        <th class="p-4 font-semibold">Kelas</th>
                        <th class="p-4 font-semibold text-center">Waktu Masuk</th>
                        <th class="p-4 font-semibold text-center">Waktu Pulang</th>
                        <th class="p-4 font-semibold text-center">Status</th>
                        <th class="p-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($attendances->where('attendable_type', App\Models\Student::class) as $data)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $data->attendable->name ?? '-' }}</td>
                        <td class="p-4 text-slate-600 dark:text-slate-300 text-sm font-medium">
                            <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                                {{ $data->attendable->class_name ?? '-' }}
                            </span>
                        </td>
                        <td class="p-4 text-center font-mono text-slate-600 dark:text-slate-300 text-sm">{{ $data->time_in ?? '-' }}</td>
                        <td class="p-4 text-center font-mono text-slate-600 dark:text-slate-300 text-sm">{{ $data->time_out ?? '-' }}</td>
                        <td class="p-4 text-center">
                            @if($data->status == 'hadir')
                                <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-full uppercase">Tepat Waktu</span>
                            @elseif($data->status == 'terlambat')
                                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-full uppercase">Terlambat</span>
                            @else
                                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold rounded-full uppercase">{{ $data->status }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.rekap.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data absensi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold transition">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">Belum ada data absensi siswa sesuai filter pada tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= TABEL GURU & AKUMULASI JAM MENGAJAR ================= -->
    <div x-show="activeTab === 'guru'" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors" style="display: none;">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Log Kehadiran Guru & Akumulasi Jam Mengajar</h3>
            <span class="text-xs text-slate-400">Total: {{ $attendances->where('attendable_type', App\Models\Teacher::class)->count() }} Guru Hadir</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="p-4 font-semibold">Nama & Gelar Guru</th>
                        <th class="p-4 font-semibold text-center">Waktu Masuk</th>
                        <th class="p-4 font-semibold text-center">Waktu Pulang</th>
                        <th class="p-4 font-semibold text-center">Status Kehadiran</th>
                        <th class="p-4 font-semibold text-center">Jumlah Jam Mengajar (JP)</th>
                        <th class="p-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($attendances->where('attendable_type', App\Models\Teacher::class) as $data)
                    @php
                        // Ambil jam mengajar otomatis dengan pengamanan null (mencegah error 500)
                        $jamMengajarHariIni = 0;
                        if ($data->attendable && method_exists($data->attendable, 'shifts')) {
                            $dayEnglish = \Carbon\Carbon::parse($data->date)->format('l');
                            $teacherShift = $data->attendable->shifts->where('day', $dayEnglish)->first();
                            $jamMengajarHariIni = $teacherShift->total_hours ?? 0;
                        }
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $data->attendable->name ?? 'Data Guru Terhapus' }}</td>
                        <td class="p-4 text-center font-mono text-slate-600 dark:text-slate-300 text-sm">{{ $data->time_in ?? '-' }}</td>
                        <td class="p-4 text-center font-mono text-slate-600 dark:text-slate-300 text-sm">{{ $data->time_out ?? '-' }}</td>
                        <td class="p-4 text-center">
                            @if($data->status == 'hadir')
                                <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-full uppercase">Tepat Waktu</span>
                            @elseif($data->status == 'terlambat')
                                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-full uppercase">Terlambat</span>
                            @else
                                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold rounded-full uppercase">{{ $data->status }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 rounded-xl font-mono font-bold text-xs">
                                +{{ $jamMengajarHariIni }} Jam (JP)
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.rekap.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data absensi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold transition">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">Belum ada data absensi guru sesuai filter pada tanggal ini.</td>
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