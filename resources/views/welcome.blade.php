<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIFAT - Sistem Informasi Presensi SMK Hijau Muda</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 font-sans antialiased transition-colors duration-300 min-h-screen flex flex-col justify-between overflow-x-hidden">

    <!-- Top Navigation / Header -->
    <header class="w-full max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-6 flex items-center justify-between">
        <div class="flex items-center gap-2.5 sm:gap-3">
            <div class="w-9 h-9 sm:w-10 sm:h-10 bg-emerald-600 rounded-2xl flex items-center justify-center font-extrabold text-white text-lg sm:text-xl shadow-lg shadow-emerald-500/30">🌱</div>
            <div>
                <span class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-white">SIFAT</span>
                <span class="text-[10px] sm:text-xs text-emerald-600 dark:text-emerald-400 font-semibold block">SMK Hijau Muda</span>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Tombol Switch Dark/Light Mode -->
            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" 
                    class="p-2 sm:p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition shadow-sm">
                <span x-text="darkMode ? '🌙' : '☀️'" class="text-xs sm:text-sm"></span>
            </button>

            @if (Route::has('login'))
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="px-4 sm:px-5 py-2 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs sm:text-sm transition shadow-lg shadow-emerald-600/20">
                        Dashboard Admin
                    </a>
                @endauth
            @endif
        </div>
    </header>

    <!-- Hero Section -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-12 lg:py-16 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
        <!-- Kiri: Copywriting & CTA -->
        <div class="lg:col-span-7 space-y-5 sm:space-y-6 text-center lg:text-left">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200/60 dark:border-emerald-900/60 rounded-full">
                <span class="w-2 h-2 bg-emerald-600 rounded-full animate-ping"></span>
                <span class-[11px] sm:text-xs font-bold text-emerald-700 dark:text-emerald-300 tracking-wide uppercase">Presensi Tap RFID & QR Code Guru & Siswa</span>
            </div>
            
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-[1.15]">
                Sistem Presensi Digital <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">SMK Hijau Muda.</span>
            </h1>
            
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-400 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                Kelola kehadiran guru dan siswa secara real-time dengan sistem tap kartu dan pindai QR. Mempermudah pencatatan shift harian, rekapitulasi otomatis, dan kedisiplinan sekolah.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 sm:gap-4 pt-2">
                <a href="{{ route('attendance.kiosk') }}" target="_blank" class="w-full sm:w-auto px-6 sm:px-8 py-3.5 sm:py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl transition shadow-xl shadow-emerald-600/30 flex items-center justify-center gap-3 text-sm sm:text-base">
                    <span>🚀 Buka Layar Kiosk Absensi</span>
                </a>
                
                @if (Route::has('login') && !auth()->check())
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 sm:px-8 py-3.5 sm:py-4 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-800 font-bold rounded-2xl transition flex items-center justify-center gap-2 text-sm sm:text-base">
                        <span>🔐 Portal Admin</span>
                    </a>
                @endif
            </div>

            <!-- Statistik Singkat / Keunggulan -->
            <div class="grid grid-cols-3 gap-3 sm:gap-4 pt-6 sm:pt-8 border-t border-slate-200 dark:border-slate-800/80 max-w-lg mx-auto lg:mx-0">
                <div>
                    <h4 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">Guru & Siswa</h4>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Terintegrasi Penuh</p>
                </div>
                <div>
                    <h4 class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">Shift Dinamis</h4>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Atur Jadwal Harian</p>
                </div>
                <div>
                    <h4 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">Real-time</h4>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Rekap Otomatis</p>
                </div>
            </div>
        </div>

        <!-- Kanan: Ilustrasi / Interactive Card Preview -->
        <div class="lg:col-span-5 w-full">
            <div class="relative bg-gradient-to-br from-emerald-600 to-teal-800 p-6 sm:p-8 rounded-3xl text-white shadow-2xl shadow-emerald-600/30 overflow-hidden">
                <!-- Background Decoration Shapes -->
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
                
                <div class="relative z-10 space-y-5 sm:space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-white/15 rounded-full text-[11px] sm:text-xs font-bold uppercase tracking-wider">SMK Hijau Muda</span>
                        <span class="flex items-center gap-2 text-[11px] sm:text-xs font-semibold bg-emerald-400/20 text-emerald-200 px-3 py-1 rounded-full border border-emerald-400/30">
                            <span class="w-2 h-2 bg-emerald-300 rounded-full animate-pulse"></span> Kiosk Aktif
                        </span>
                    </div>

                    <div class="space-y-3 bg-slate-900/40 backdrop-blur-md p-4 sm:p-5 rounded-2xl border border-white/10">
                        <p class="text-[11px] sm:text-xs font-semibold text-emerald-200 uppercase">Aktivitas Presensi Tap</p>
                        <div class="flex items-center justify-between text-xs sm:text-sm py-2 border-b border-white/10">
                            <span>Tap Guru (Shift Harian)</span>
                            <span class="font-mono text-emerald-300">Tepat Waktu ✓</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm py-2">
                            <span>Tap Siswa / Scan QR</span>
                            <span class="font-mono text-teal-200">Berhasil ✓</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-7xl mx-auto px-4 sm:px-6 py-6 border-t border-slate-200 dark:border-slate-800/80 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3 text-center sm:text-left">
        <p>&copy; 2026 SIFAT - SMK Hijau Muda. Hak Cipta Dilindungi.</p>
        <p>
            Developed BY
            <a href="https://web-portofolio-me.netlify.app/" target="_blank" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                Muhammad Andika Anjas S.Kom.
            </a>
        </p>
    </footer>

</body>
</html>