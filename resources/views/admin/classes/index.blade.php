@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Manajemen Data Kelas</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Kelola daftar rombongan belajar / kelas untuk referensi data siswa.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-600 text-white rounded-2xl font-medium text-sm shadow-lg">
        ✨ {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Tambah Kelas -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm h-fit">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4">Tambah Kelas Baru</h3>
            <form action="{{ route('admin.classes.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Kelas:</label>
                    <input type="text" name="name" placeholder="Contoh: XII TKJ 1" required class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold outline-none text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tingkat:</label>
                    <select name="level" class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold outline-none text-slate-900 dark:text-white">
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-emerald-600/20">
                    💾 Simpan Kelas
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Kelas -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Daftar Kelas Terdaftar</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-xs">
                        <tr>
                            <th class="p-4 font-semibold text-center w-16">No</th>
                            <th class="p-4 font-semibold">Nama Kelas</th>
                            <th class="p-4 font-semibold">Tingkat</th>
                            <th class="p-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($classes as $index => $cls)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="p-4 text-center text-slate-500 font-mono">{{ $index + 1 }}</td>
                            <td class="p-4 font-bold text-slate-900 dark:text-white">{{ $cls->name }}</td>
                            <td class="p-4 text-slate-600 dark:text-slate-300 font-medium">
                                <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-xs">{{ $cls->level ?? '-' }}</span>
                            </td>
                            <td class="p-4 text-center">
                                <form action="{{ route('admin.classes.destroy', $cls->id) }}" method="POST" onsubmit="return confirm('Hapus kelas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold transition">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-400">Belum ada data kelas yang ditambahkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection