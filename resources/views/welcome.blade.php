<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIFAT - Sistem Informasi Presensi Cepat & Terpadu</title>
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
<body class="bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 font-sans antialiased transition-colors duration-300 min-h-screen flex flex-col justify-between">

    <!-- Top Navigation / Header -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-2xl flex items-center justify-center font-extrabold text-white text-xl shadow-lg shadow-blue-500/30">S</div>
            <div>
                <span class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">SIFAT</span>
                <span class="text-xs text-blue-600 dark:text-blue-400 font-semibold block">Presensi Pintar</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Tombol Switch Dark/Light Mode -->
            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" 
                    class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition shadow-sm">
                <span x-text="darkMode ? '🌙' : '☀️'" class="text-sm"></span>
            </button>

            @if (Route::has('login'))
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-lg shadow-blue-600/20">
                        Dashboard Admin
                    </a>
                @else
                    <!-- <a href="{{ route('login') }}" class="px-5 py-2.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white font-semibold rounded-xl text-sm transition shadow-sm">
                        Masuk Admin
                    </a> -->
                @endauth
            @endif
        </div>
    </header>

    <!-- Hero Section -->
    <main class="max-w-7xl mx-auto px-6 py-12 lg:py-20 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <!-- Kiri: Copywriting & CTA -->
        <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-blue-50 dark:bg-blue-950/60 border border-blue-200/60 dark:border-blue-900/60 rounded-full">
                <span class="w-2 h-2 bg-blue-600 rounded-full animate-ping"></span>
                <span class="text-xs font-bold text-blue-700 dark:text-blue-300 tracking-wide uppercase">Teknologi RFID & QR Code Terpadu</span>
            </div>
            
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-[1.15]">
                Sistem Presensi Masa Kini yang <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Cepat & Akurat.</span>
            </h1>
            
            <p class="text-lg text-slate-600 dark:text-slate-400 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                Kelola kehadiran siswa dan guru secara real-time. Mempermudah rekapitulasi harian, meminimalisir kecurangan, dan menghadirkan efisiensi total bagi institusi Anda.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                <a href="{{ route('attendance.kiosk') }}" target="_blank" class="w-full sm:w-auto px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl transition shadow-xl shadow-blue-600/30 flex items-center justify-center gap-3 text-base">
                    <span>🚀 Buka Layar Kiosk Absensi</span>
                </a>
                
                @if (Route::has('login') && !auth()->check())
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-800 font-bold rounded-2xl transition flex items-center justify-center gap-2 text-base">
                        <span>🔐 Portal Admin</span>
                    </a>
                @endif
            </div>

            <!-- Statistik Singkat / Keunggulan -->
            <div class="grid grid-cols-3 gap-4 pt-8 border-t border-slate-200 dark:border-slate-800/80 max-w-lg mx-auto lg:mx-0">
                <div>
                    <h4 class="text-2xl font-extrabold text-slate-900 dark:text-white">100%</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Real-time Data</p>
                </div>
                <div>
                    <h4 class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">&lt; 1 Detik</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Kecepatan Tap</p>
                </div>
                <div>
                    <h4 class="text-2xl font-extrabold text-slate-900 dark:text-white">Aman</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Sistem Terenkripsi</p>
                </div>
            </div>
        </div>

        <!-- Kanan: Ilustrasi / Interactive Card Preview -->
        <div class="lg:col-span-5">
            <div class="relative bg-gradient-to-br from-blue-600 to-indigo-800 p-8 rounded-3xl text-white shadow-2xl shadow-blue-600/30 overflow-hidden">
                <!-- Background Decoration Shapes -->
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
                
                <div class="relative z-10 space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-white/15 rounded-full text-xs font-bold uppercase tracking-wider">Status Sistem</span>
                        <span class="flex items-center gap-2 text-xs font-semibold bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full border border-emerald-500/30">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span> Online & Siap
                        </span>
                    </div>

                    <div class="space-y-3 bg-slate-900/40 backdrop-blur-md p-5 rounded-2xl border border-white/10">
                        <p class="text-xs font-semibold text-blue-200 uppercase">Aktivitas Terbaru</p>
                        <div class="flex items-center justify-between text-sm py-2 border-b border-white/10">
                            <span>Siswa Tap Masuk</span>
                            <span class="font-mono text-emerald-300">Tepat Waktu ✓</span>
                        </div>
                        <div class="flex items-center justify-between text-sm py-2">
                            <span>Validasi Kartu RFID</span>
                            <span class="font-mono text-blue-300">Berhasil ✓</span>
                        </div>
                    </div>

                    <!-- <div class="pt-2">
                        <a href="{{ route('attendance.kiosk') }}" target="_blank" class="block w-full py-3.5 bg-white text-blue-700 hover:bg-blue-50 font-bold rounded-xl text-center transition shadow-lg text-sm">
                            Mulai Presensi Sekarang →
                        </a>
                    </div> -->
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-7xl mx-auto px-6 py-6 border-t border-slate-200 dark:border-slate-800/80 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
        <p>&copy; 2026 SIFAT. Hak Cipta Dilindungi.</p>
        <p>Sistem Informasi Presensi Cepat & Terpadu v1.0</p>
    </footer>

</body>
</html>