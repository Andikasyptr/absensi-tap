<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Sistem Absensi</title>
    <!-- Gunakan Tailwind CSS v3 CDN agar kelas dark: langsung berfungsi aktif -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 font-sans antialiased transition-colors duration-300">

    <div class="flex min-h-screen">
        <!-- Sidebar Modern -->
        <aside class="w-72 bg-slate-900 text-white p-6 shadow-xl flex flex-col justify-between border-r border-slate-800">
            <div>
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center font-bold text-lg shadow-lg shadow-blue-500/30">A</div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight text-white">Admin Portal</h1>
                        <p class="text-xs text-slate-400">Sistem Absensi Tap</p>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="space-y-1.5">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <span>📊 Dashboard</span>
                    </a>
                    <a href="{{ route('admin.data.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition {{ request()->routeIs('admin.data.index') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <span>👥 Siswa & Guru</span>
                    </a>
                    <a href="{{ route('admin.rekap') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition {{ request()->routeIs('admin.rekap') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <span>📅 Rekap Absensi</span>
                    </a>
                </nav>
            </div>

            <!-- Bagian Bawah Sidebar (Toggle Theme, Kiosk, & Logout) -->
            <div class="space-y-3 pt-6 border-t border-slate-800">
                <!-- Tombol Switch Mode Terang/Gelap -->
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" 
                        class="flex items-center justify-between w-full px-4 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-medium transition text-sm border border-slate-700/50">
                    <span x-text="darkMode ? '🌙 Mode Gelap' : '☀️ Mode Terang'"></span>
                    <div class="w-8 h-4 bg-slate-700 rounded-full relative p-0.5 transition">
                        <div class="w-3 h-3 bg-white rounded-full transition-transform" :class="darkMode ? 'translate-x-4 bg-blue-400' : 'translate-x-0'"></div>
                    </div>
                </button>

                <!-- Quick Action to Kiosk -->
                <a href="{{ route('attendance.kiosk') }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-slate-800 hover:bg-slate-700 text-blue-400 hover:text-white rounded-xl font-semibold transition text-sm border border-slate-700/50">
                    <span>🚀 Buka Layar Kiosk</span>
                </a>

                <!-- Tombol Logout -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center justify-center gap-2 w-full py-3 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 rounded-xl font-semibold transition text-sm border border-rose-500/20">
                        <span>🚪 Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 p-10 overflow-y-auto bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300">
            @yield('content')
        </main>
    </div>

</body>
</html>