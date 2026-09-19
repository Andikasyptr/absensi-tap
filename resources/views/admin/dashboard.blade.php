@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="dashboardManager('{{ $selectedDate }}')">
    <!-- Header & Live Clock / Date Filter Banner -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Dashboard Overview</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Ringkasan aktivitas kehadiran sistem SIFAT.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Filter Tanggal Interaktif -->
            <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/60 px-4 py-2 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                <span class="text-xs font-bold text-slate-400 uppercase">Pilih Tanggal:</span>
                <input type="date" 
                       x-model="selectedDate" 
                       @change="filterByDate()" 
                       class="bg-transparent text-sm font-semibold text-slate-800 dark:text-slate-200 focus:outline-none cursor-pointer">
            </div>

            <!-- Jam Server -->
            <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/60 px-4 py-2.5 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-400 uppercase">Waktu Server</p>
                    <p class="text-sm font-mono font-bold text-slate-800 dark:text-slate-200" x-text="currentTime"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Status Tanggal yang Sedang Ditampilkan -->
    <div class="bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/50 px-5 py-3 rounded-2xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-xl">📅</span>
            <p class="text-sm font-medium text-blue-900 dark:text-blue-200">
                Menampilkan data rekapitulasi kehadiran untuk tanggal: <strong class="underline font-bold" x-text="formatDate(selectedDate)"></strong>
            </p>
        </div>
        <button @click="resetToToday()" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
            Reset ke Hari Ini
        </button>
    </div>

    <!-- Stats Grid Utama (Data dari Database sesuai tanggal terpilih) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Kehadiran -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center text-xl font-bold">
                    📊
                </div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-full">Periode Ini</span>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Kehadiran</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $totalHadir }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Dari {{ $totalSiswaCount + $totalGuruCount }} total terdaftar</p>
        </div>

        <!-- Siswa Hadir -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center text-xl font-bold">
                    👥
                </div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-full">Siswa</span>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Siswa Hadir</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $siswaHadir }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Total siswa: {{ $totalSiswaCount }}</p>
        </div>

        <!-- Guru Hadir -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center text-xl font-bold">
                    👨‍🏫
                </div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-full">Guru</span>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Guru Hadir</p>
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $guruHadir }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Total guru: {{ $totalGuruCount }}</p>
        </div>

        <!-- Terlambat -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 text-amber-500 rounded-2xl flex items-center justify-center text-xl font-bold">
                    ⚠️
                </div>
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 rounded-full">Perhatian</span>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Terlambat</p>
            <h3 class="text-3xl font-extrabold text-amber-500 mt-1">{{ $totalTerlambat }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Masuk melewati batas waktu</p>
        </div>
    </div>

    <!-- Quick Actions & Recent Shortcut Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Panel Selamat Datang / Info Cepat -->
        <div class="lg:col-span-2 bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-3xl text-white shadow-xl shadow-blue-600/20 flex flex-col justify-between">
            <div>
                <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-bold uppercase tracking-wider">Portal Administrator</span>
                <h3 class="text-2xl font-bold mt-3">Kelola Sistem Presensi Lebih Mudah & Cepat</h3>
                <p class="text-blue-100 text-sm mt-2 leading-relaxed">
                    Sistem Tap Kartu RFID dan QR Code aktif dan berjalan lancar. Gunakan filter kalender di atas untuk memantau data historis absensi harian secara spesifik.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 mt-8">
                <a href="{{ route('admin.rekap') }}" class="px-5 py-2.5 bg-white text-blue-700 hover:bg-blue-50 rounded-xl font-bold text-sm transition shadow-md">
                    📅 Lihat Rekap Absen Lengkap
                </a>
                <a href="{{ route('admin.data.index') }}" class="px-5 py-2.5 bg-blue-500/40 hover:bg-blue-500/60 text-white border border-white/20 rounded-xl font-bold text-sm transition">
                    👥 Kelola Siswa & Guru
                </a>
            </div>
        </div>

        <!-- Quick Links / Shortcuts Card -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="font-bold text-slate-900 dark:text-white text-base mb-4">Aksi Cepat</h4>
                <div class="space-y-3">
                    <a href="{{ route('attendance.kiosk') }}" target="_blank" class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-2xl transition border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">🚀</span>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Buka Layar Kiosk</span>
                        </div>
                        <span class="text-xs text-slate-400 font-bold">↗</span>
                    </a>

                    <a href="{{ route('admin.data.index') }}" class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-2xl transition border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">➕</span>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Daftar Kartu Baru</span>
                        </div>
                        <span class="text-xs text-slate-400 font-bold">→</span>
                    </a>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
                <p class="text-xs text-slate-400">SIFAT v1.0 • Running smoothly</p>
            </div>
        </div>
    </div>
</div>

<script>
    function dashboardManager(initialDate) {
        return {
            currentTime: '',
            selectedDate: initialDate,
            init() {
                this.updateClock();
                setInterval(() => { this.updateClock(); }, 1000);
            },
            updateClock() {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false });
            },
            filterByDate() {
                if (this.selectedDate) {
                    window.location.href = "{{ route('admin.dashboard') }}?date=" + this.selectedDate;
                }
            },
            resetToToday() {
                const today = new Date().toISOString().split('T')[0];
                window.location.href = "{{ route('admin.dashboard') }}?date=" + today;
            },
            formatDate(dateString) {
                if (!dateString) return '';
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            }
        }
    }
</script>
@endsection