<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SIFAT</title>
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-slate-800 p-8 rounded-3xl shadow-2xl border border-slate-700">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold">SIFAT Admin</h1>
            <p class="text-slate-400 text-sm mt-1">Silakan masuk untuk mengelola sistem absensi</p>
        </div>

        <!-- Tampilkan Error Validasi / Login Gagal -->
        @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-600/30 border border-rose-500/50 text-rose-200 rounded-2xl text-xs font-medium">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Email Admin</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 text-white transition"
                       placeholder="admin@trindigi.com">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Password</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 text-white transition"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs text-slate-400">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-0">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-600/30 transition">
                Masuk ke Dashboard
            </button>
        </form>
    </div>

</body>
</html>