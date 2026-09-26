<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SIFAT SMK Hijau Muda</title>
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Background Glow Effects -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-teal-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full bg-slate-900/80 backdrop-blur-xl p-8 rounded-3xl shadow-2xl border border-slate-800 relative z-10">
        
        <!-- Header Brand -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-emerald-600 rounded-2xl flex items-center justify-center font-extrabold text-white text-2xl shadow-lg shadow-emerald-500/30 mx-auto mb-4">
                🌱
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Portal Admin SIFAT</h1>
            <p class="text-slate-400 text-sm mt-1">SMK Hijau Muda — Kelola Presensi & Data Terpadu</p>
        </div>

        <!-- Tampilkan Error Validasi / Login Gagal -->
        @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-300 rounded-2xl text-xs font-medium flex items-center gap-2">
            <span>⚠️</span>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Email Admin</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-500 text-sm">✉️</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full pl-11 pr-4 py-3.5 bg-slate-950/60 border border-slate-800 rounded-2xl text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white transition placeholder-slate-600"
                           placeholder="admin@hijaumuda.sch.id">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-500 text-sm">🔒</span>
                    <input type="password" name="password" required
                           class="w-full pl-11 pr-4 py-3.5 bg-slate-950/60 border border-slate-800 rounded-2xl text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white transition placeholder-slate-600"
                           placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-slate-900">
                    <span class="font-medium">Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-bold text-sm shadow-lg shadow-emerald-600/30 transition transform active:scale-[0.98]">
                Masuk ke Dashboard
            </button>
        </form>

        <!-- Footer Kecil -->
        <div class="mt-8 pt-6 border-t border-slate-800/80 text-center">
            <a href="{{ url('/') }}" class="text-xs text-slate-500 hover:text-emerald-400 transition font-medium">
                ← Kembali ke Beranda Utama
            </a>
        </div>
    </div>

</body>
</html>