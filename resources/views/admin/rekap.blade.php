@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="rekapManager()">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Rekap Absensi Harian</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Data presensi terintegrasi hasil scan QR Code Siswa dan Guru.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold text-sm transition">
                🖨️ Export PDF/Print
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi Flash Message (Opsional jika ada) -->
    @if(session('success'))
    <div class="p-4 bg-emerald-600 text-white rounded-2xl font-medium text-sm shadow-lg shadow-emerald-950">
        {{ session('success') }}
    </div>
    @endif

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-600 p-6 rounded-2xl text-white shadow-lg shadow-blue-600/20">
            <p class="text-blue-100 text-xs font-medium uppercase">Total Kehadiran Hari Ini</p>
            <h3 class="text-3xl font-bold mt-1">{{ $totalHadir }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase">Total Siswa Hadir</p>
            <h3 class="text-3xl font-bold text-slate-900 dark:text-white mt-1">{{ $totalSiswa }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase">Total Guru Hadir</p>
            <h3 class="text-3xl font-bold text-slate-900 dark:text-white mt-1">{{ $totalGuru }}</h3>
        </div>
    </div>

    <!-- Filter Bar & Tab Pilihan (Siswa / Guru) -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col md:flex-row justify-between gap-4 items-center">
        <!-- Tab Navigasi -->
        <div class="flex space-x-2 w-full md:w-auto">
            <button @click="activeTab = 'siswa'" 
                    :class="activeTab === 'siswa' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'"
                    class="px-6 py-2.5 rounded-xl font-bold text-sm transition flex-1 md:flex-none">
                👥 Rekap Siswa
            </button>
            <button @click="activeTab = 'guru'" 
                    :class="activeTab === 'guru' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'"
                    class="px-6 py-2.5 rounded-xl font-bold text-sm transition flex-1 md:flex-none">
                👨‍🏫 Rekap Guru
            </button>
        </div>

        <!-- Filter Tanggal -->
        <form method="GET" action="{{ route('admin.rekap') }}" class="flex gap-2 w-full md:w-auto">
            <input type="date" name="date" value="{{ $date }}" class="p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm outline-none text-slate-900 dark:text-white flex-1">
            <button type="submit" class="px-4 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white rounded-xl text-sm font-bold transition">Filter</button>
        </form>
    </div>

    <!-- ================= TABEL SISWA ================= -->
    <div x-show="activeTab === 'siswa'" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Log Kehadiran Siswa</h3>
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
                                <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-full uppercase">Hadir</span>
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
                        <td colspan="6" class="p-8 text-center text-slate-400">Belum ada data absensi siswa pada tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= TABEL GURU ================= -->
    <div x-show="activeTab === 'guru'" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors" style="display: none;">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Log Kehadiran Guru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="p-4 font-semibold">Nama & Gelar Guru</th>
                        <th class="p-4 font-semibold">Kategori</th>
                        <th class="p-4 font-semibold text-center">Waktu Masuk</th>
                        <th class="p-4 font-semibold text-center">Waktu Pulang</th>
                        <th class="p-4 font-semibold text-center">Status</th>
                        <th class="p-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($attendances->where('attendable_type', App\Models\Teacher::class) as $data)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $data->attendable->name ?? '-' }}</td>
                        <td class="p-4 text-slate-500 dark:text-slate-400 text-sm">Staf Pengajar / Guru</td>
                        <td class="p-4 text-center font-mono text-slate-600 dark:text-slate-300 text-sm">{{ $data->time_in ?? '-' }}</td>
                        <td class="p-4 text-center font-mono text-slate-600 dark:text-slate-300 text-sm">{{ $data->time_out ?? '-' }}</td>
                        <td class="p-4 text-center">
                            @if($data->status == 'hadir')
                                <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-full uppercase">Hadir</span>
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
                        <td colspan="6" class="p-8 text-center text-slate-400">Belum ada data absensi guru pada tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function rekapManager() {
        return {
            activeTab: 'siswa'
        }
    }
</script>
@endsection