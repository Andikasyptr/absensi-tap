@extends('layouts.admin')

@section('content')
    <!-- CDN Library QRCode.js untuk render QR Code secara visual -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <div class="mb-8 flex justify-between items-center">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-900/50 rounded-full mb-2">
                <span class="w-2 h-2 bg-emerald-600 rounded-full animate-ping"></span>
                <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 tracking-wider uppercase">SMK Hijau Muda</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Manajemen Data & Registrasi Hybrid (Chip & QR)</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Registrasi kartu chip RFID & QR Code, pengaturan shift jam dinamis per hari untuk guru, data siswa, dan tenaga kependidikan (TU).</p>
        </div>
    </div>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-emerald-600 text-white p-4 rounded-2xl mb-6 shadow-lg shadow-emerald-950 flex items-center gap-3">
            <span>✨</span>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-rose-600 text-white p-4 rounded-2xl mb-6 shadow-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Container Utama dengan Alpine.js -->
    <div x-data="dataManager()" class="space-y-6">
        
        <!-- Tab Navigation Buttons -->
        <div class="flex flex-wrap gap-2 border-b border-slate-200 dark:border-slate-800 pb-4">
            <button @click="activeTab = 'siswa'" 
                    :class="activeTab === 'siswa' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800'"
                    class="px-6 py-2.5 rounded-xl font-bold text-sm transition">
                👥 Data Siswa
            </button>
            <button @click="activeTab = 'guru'" 
                    :class="activeTab === 'guru' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800'"
                    class="px-6 py-2.5 rounded-xl font-bold text-sm transition">
                👨‍🏫 Data Guru & Shift Harian
            </button>
            <button @click="activeTab = 'tata_usaha'" 
                    :class="activeTab === 'tata_usaha' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800'"
                    class="px-6 py-2.5 rounded-xl font-bold text-sm transition">
                💼 Staf TU & Tenaga Kependidikan
            </button>
        </div>

        <!-- ================= SECTION SISWA ================= -->
        <div x-show="activeTab === 'siswa'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Tambah Siswa -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm transition-colors">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span>➕</span> Tambah Siswa Baru
                    </h3>
                    <form action="{{ route('admin.student.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">NIS</label>
                            <input type="text" name="nis" x-model="studentNis" required placeholder="Contoh: 10293" class="w-full mt-1 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="name" required placeholder="Nama lengkap siswa" class="w-full mt-1 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kelas</label>
                            <select name="class_name" required class="w-full mt-1 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500 text-slate-900 dark:text-white outline-none transition text-sm">
                                <option value="">-- Pilih Rombel / Kelas --</option>
                                @foreach($schoolClasses as $cls)
                                    <option value="{{ $cls->name }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">UID Chip RFID / QR Code</label>
                                <button type="button" @click="generateRandomStudent" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-medium">✨ Generate QR Manual</button>
                            </div>
                            <input type="text" name="rfid_uid" x-model="studentCode" required placeholder="Klik & Tap kartu ke USB Reader..." class="w-full p-3 bg-emerald-50/50 dark:bg-slate-800/60 border border-emerald-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-sm font-mono">
                            <p class="text-[10px] text-slate-400 mt-1">*Tempelkan kartu ke USB RFID Reader untuk mengisi otomatis, atau generate QR.</p>
                        </div>
                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-lg shadow-emerald-600/20 transition text-sm">
                            Simpan Data Siswa
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabel Daftar Siswa -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Daftar Kartu Siswa Terdaftar (Hybrid)</h3>
                    </div>
                    <div class="overflow-x-auto max-h-[600px]">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="p-4 font-semibold">Pratinjau Kartu Hybrid (Chip + QR)</th>
                                    <th class="p-4 font-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($students as $student)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                                    <td class="p-4">
                                        <div class="w-80 bg-gradient-to-br from-slate-900 via-slate-900 to-emerald-950 text-white rounded-2xl p-4 shadow-md border border-slate-700/80 flex flex-col justify-between">
                                            <div class="flex justify-between items-center border-b border-slate-700/60 pb-2">
                                                <span class="text-[9px] font-bold uppercase text-emerald-400">SMK HIJAU MUDA • HYBRID ID</span>
                                                <span class="text-[9px] bg-emerald-600 px-2 py-0.5 rounded text-white font-semibold">SISWA</span>
                                            </div>
                                            <div class="flex items-center gap-3 my-3">
                                                <div class="w-10 h-8 bg-gradient-to-tr from-amber-400 to-yellow-200 rounded border border-amber-500 flex items-center justify-center text-[8px] font-bold text-amber-900 shadow-inner shrink-0">CHIP</div>
                                                <div class="bg-white p-1 rounded shrink-0">
                                                    <div class="qrcode" data-value="{{ $student->rfid_uid }}" data-size="52"></div>
                                                </div>
                                                <div class="overflow-hidden">
                                                    <h4 class="font-bold text-xs text-white truncate">{{ $student->name }}</h4>
                                                    <p class="text-[10px] text-slate-300">Kelas: {{ $student->class_name }}</p>
                                                    <p class="text-[9px] text-slate-400">NIS: {{ $student->nis }}</p>
                                                </div>
                                            </div>
                                            <div class="border-t border-slate-700/60 pt-2 flex justify-between text-[9px] text-slate-400 font-mono">
                                                <span>ID: {{ $student->rfid_uid }}</span>
                                                <span class="text-emerald-400 font-sans">● AKTIF</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center align-middle">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click="openEditStudent({{ $student->id }}, '{{ addslashes($student->nis) }}', '{{ addslashes($student->name) }}', '{{ addslashes($student->class_name) }}', '{{ $student->rfid_uid }}')" 
                                                    class="px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold transition shadow-md shadow-sky-600/20 flex items-center gap-1">
                                                ✏️ Edit
                                            </button>
                                            <button type="button" onclick="printHybridCard('{{ addslashes($student->name) }}', 'Kelas: {{ addslashes($student->class_name) }} (NIS: {{ $student->nis }})', '{{ $student->rfid_uid }}', 'SISWA')" 
                                                    class="px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-md shadow-emerald-600/20 flex items-center gap-1">
                                                🖨️ Cetak
                                            </button>
                                            <form action="{{ route('admin.student.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa {{ addslashes($student->name) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-rose-600/20 flex items-center gap-1">
                                                    🗑️ Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="p-8 text-center text-slate-400 dark:text-slate-500">Belum ada data siswa yang terdaftar.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= SECTION GURU & SHIFT HARIAN BERBEDA ================= -->
        <div x-show="activeTab === 'guru'" class="grid grid-cols-1 lg:grid-cols-3 gap-8" style="display: none;">
            
            <div class="lg:col-span-1 space-y-6">
                <!-- Form Tambah Guru -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm transition-colors">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span>➕</span> Tambah Guru Baru
                    </h3>
                    <form action="{{ route('admin.teacher.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Lengkap & Gelar</label>
                            <input type="text" name="name" x-model="teacherName" required placeholder="Contoh: Budi Santoso, S.Pd" class="w-full mt-1 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-sm">
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">UID Chip RFID / QR Code</label>
                                <button type="button" @click="generateRandomTeacher" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-medium">✨ Generate QR Manual</button>
                            </div>
                            <input type="text" name="rfid_uid" x-model="teacherCode" required placeholder="Klik & Tap kartu ke USB Reader..." class="w-full p-3 bg-emerald-50/50 dark:bg-slate-800/60 border border-emerald-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-sm font-mono">
                            <p class="text-[10px] text-slate-400 mt-1">*Tempelkan kartu ke USB RFID Reader untuk mengisi otomatis.</p>
                        </div>
                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-lg shadow-emerald-600/20 transition text-sm">
                            Simpan Data Guru
                        </button>
                    </form>
                </div>

                <!-- Form Setting Shift Harian & Jumlah Jam Mengajar -->
                @isset($teachers)
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm transition-colors">
                    <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                        <span>⏰</span> Atur Shift & Jumlah Jam Guru
                    </h3>
                    <p class="text-xs text-slate-400 mb-4">Tentukan jam masuk, jam pulang, dan jumlah jam mengajar per hari.</p>
                    
                    <form action="{{ route('admin.shifts.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Pilih Guru</label>
                            <select name="teacher_id" required class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Hari Mengajar</label>
                            <select name="day" required class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none">
                                <option value="Monday">Senin (Monday)</option>
                                <option value="Tuesday">Selasa (Tuesday)</option>
                                <option value="Wednesday">Rabu (Wednesday)</option>
                                <option value="Thursday">Kamis (Thursday)</option>
                                <option value="Friday">Jumat (Friday)</option>
                                <option value="Saturday">Sabtu (Saturday)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Jam Masuk</label>
                                <input type="time" name="shift_start" required class="w-full mt-1 p-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-800 dark:text-slate-200">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Jam Pulang</label>
                                <input type="time" name="shift_end" class="w-full mt-1 p-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-800 dark:text-slate-200">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Jumlah Jam Mengajar (JP)</label>
                            <input type="number" name="total_hours" min="1" max="12" placeholder="Contoh: 4" class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none">
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs transition shadow-lg shadow-emerald-600/20">
                            Simpan Shift Harian
                        </button>
                    </form>
                </div>
                @endisset
            </div>

            <!-- Tabel Daftar Guru -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Daftar Guru & Jadwal Shift Per Hari</h3>
                    </div>
                    <div class="overflow-x-auto max-h-[600px]">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="p-4 font-semibold">Pratinjau Kartu Hybrid & Detail Shift Mingguan</th>
                                    <th class="p-4 font-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @isset($teachers)
                                    @forelse($teachers as $teacher)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition align-top">
                                        <td class="p-4 space-y-3">
                                            <div class="w-80 bg-gradient-to-br from-slate-900 via-slate-900 to-emerald-950 text-white rounded-2xl p-4 shadow-md border border-slate-700/80 flex flex-col justify-between">
                                                <div class="flex justify-between items-center border-b border-slate-700/60 pb-2">
                                                    <span class="text-[9px] font-bold uppercase text-emerald-400">SMK HIJAU MUDA • HYBRID ID</span>
                                                    <span class="text-[9px] bg-emerald-600 px-2 py-0.5 rounded text-white font-semibold">GURU</span>
                                                </div>
                                                <div class="flex items-center gap-3 my-3">
                                                    <div class="w-10 h-8 bg-gradient-to-tr from-amber-400 to-yellow-200 rounded border border-amber-500 flex items-center justify-center text-[8px] font-bold text-amber-900 shadow-inner shrink-0">CHIP</div>
                                                    <div class="bg-white p-1 rounded shrink-0">
                                                        <div class="qrcode" data-value="{{ $teacher->rfid_uid }}" data-size="52"></div>
                                                    </div>
                                                    <div class="overflow-hidden">
                                                        <h4 class="font-bold text-xs text-white truncate">{{ $teacher->name }}</h4>
                                                        <p class="text-[10px] text-slate-300">Staf Pengajar</p>
                                                    </div>
                                                </div>
                                                <div class="border-t border-slate-700/60 pt-2 flex justify-between text-[9px] text-slate-400 font-mono">
                                                    <span>ID: {{ $teacher->rfid_uid }}</span>
                                                    <span class="text-emerald-400 font-sans">● AKTIF</span>
                                                </div>
                                            </div>

                                            <div class="max-w-md bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                                                <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-2">📅 Jam Masuk & Jam Mengajar Berdasarkan Hari:</p>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    @isset($teacher->shifts)
                                                        @forelse($teacher->shifts as $ts)
                                                            <div class="bg-white dark:bg-slate-800 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700 text-[11px] flex flex-col justify-between">
                                                                <div>
                                                                    <div class="flex justify-between items-center">
                                                                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $ts->day }}</span>
                                                                        <div class="flex gap-1.5">
                                                                            <button type="button" @click="openEditShift({{ $ts->id }}, '{{ $ts->day }}', '{{ $ts->shift_start }}', '{{ $ts->shift_end }}', '{{ $ts->total_hours }}')" class="text-sky-600 hover:text-sky-500 font-bold text-xs" title="Edit Shift">✏️</button>
                                                                            <form action="{{ route('admin.shifts.destroy', $ts->id) }}" method="POST" onsubmit="return confirm('Hapus shift hari {{ $ts->day }} ini?')" class="inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="text-rose-600 hover:text-rose-500 font-bold text-xs" title="Hapus Shift">✕</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                    <span class="font-mono text-slate-700 dark:text-slate-200 block mt-1">Masuk: {{ $ts->shift_start }}</span>
                                                                    @if(isset($ts->total_hours))
                                                                        <span class="text-xs font-bold text-blue-500 mt-0.5 block">📌 {{ $ts->total_hours }} JP</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <p class="text-[11px] text-slate-400 col-span-2 italic">Belum ada jadwal shift harian diatur untuk guru ini.</p>
                                                        @endforelse
                                                    @endisset
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 text-center align-middle">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" @click="openEditTeacher({{ $teacher->id }}, '{{ addslashes($teacher->name) }}', '{{ $teacher->rfid_uid }}')" 
                                                        class="px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold transition shadow-md shadow-sky-600/20 flex items-center gap-1">
                                                    ✏️ Edit Guru
                                                </button>
                                                <button type="button" onclick="printHybridCard('{{ addslashes($teacher->name) }}', 'Kartu Pengajar Aktif', '{{ $teacher->rfid_uid }}', 'GURU')" 
                                                        class="px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-md shadow-emerald-600/20 flex items-center gap-1">
                                                    🖨️ Cetak
                                                </button>
                                                <form action="{{ route('admin.teacher.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data guru {{ addslashes($teacher->name) }} beserta seluruh jadwal shift-nya?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-rose-600/20 flex items-center gap-1">
                                                        🗑️ Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="p-8 text-center text-slate-400 dark:text-slate-500">Belum ada data guru yang terdaftar.</td>
                                    </tr>
                                    @endforelse
                                @endisset
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= SECTION TATA USAHA (STAFF TU) ================= -->
        <div x-show="activeTab === 'tata_usaha'" class="grid grid-cols-1 lg:grid-cols-3 gap-8" style="display: none;">
            
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm transition-colors">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span>➕</span> Tambah Staf TU Baru
                    </h3>
                    <form action="{{ route('admin.staff.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Lengkap & Gelar</label>
                            <input type="text" name="name" x-model="staffName" required placeholder="Contoh: Siti Aminah, A.Md" class="w-full mt-1 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jabatan / Posisi</label>
                            <input type="text" name="position" placeholder="Contoh: Staf Tata Usaha / Admin" class="w-full mt-1 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">UID Chip RFID / QR Code</label>
                                <button type="button" @click="generateRandomStaff" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-medium">✨ Generate QR Manual</button>
                            </div>
                            <input type="text" name="rfid_uid" x-model="staffCode" required placeholder="Klik & Tap kartu ke USB Reader..." class="w-full p-3 bg-emerald-50/50 dark:bg-slate-800/60 border border-emerald-200 dark:border-slate-700 rounded-xl text-sm font-mono text-slate-900 dark:text-white outline-none">
                        </div>
                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-lg transition text-sm">
                            Simpan Staf TU
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabel Daftar Staff TU -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Daftar Staf TU & Tenaga Kependidikan</h3>
                    </div>
                    <div class="overflow-x-auto max-h-[600px]">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="p-4 font-semibold">Pratinjau Kartu Hybrid (Chip + QR)</th>
                                    <th class="p-4 font-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @isset($staffList)
                                    @forelse($staffList as $staff)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                                        <td class="p-4">
                                            <div class="w-80 bg-gradient-to-br from-slate-900 via-slate-900 to-emerald-950 text-white rounded-2xl p-4 shadow-md border border-slate-700/80 flex flex-col justify-between">
                                                <div class="flex justify-between items-center border-b border-slate-700/60 pb-2">
                                                    <span class="text-[9px] font-bold uppercase text-emerald-400">SMK HIJAU MUDA • HYBRID ID</span>
                                                    <span class="text-[9px] bg-teal-600 px-2 py-0.5 rounded text-white font-semibold">TATA USAHA</span>
                                                </div>
                                                <div class="flex items-center gap-3 my-3">
                                                    <div class="w-10 h-8 bg-gradient-to-tr from-amber-400 to-yellow-200 rounded border border-amber-500 flex items-center justify-center text-[8px] font-bold text-amber-900 shadow-inner shrink-0">CHIP</div>
                                                    <div class="bg-white p-1 rounded shrink-0">
                                                        <div class="qrcode" data-value="{{ $staff->rfid_uid }}" data-size="52"></div>
                                                    </div>
                                                    <div class="overflow-hidden">
                                                        <h4 class="font-bold text-xs text-white truncate">{{ $staff->name }}</h4>
                                                        <p class="text-[10px] text-slate-300">{{ $staff->position ?? 'Staf TU' }}</p>
                                                    </div>
                                                </div>
                                                <div class="border-t border-slate-700/60 pt-2 flex justify-between text-[9px] text-slate-400 font-mono">
                                                    <span>ID: {{ $staff->rfid_uid }}</span>
                                                    <span class="text-emerald-400 font-sans">● AKTIF</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 text-center align-middle">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" @click="openEditStaff({{ $staff->id }}, '{{ addslashes($staff->name) }}', '{{ addslashes($staff->position ?? '') }}', '{{ $staff->rfid_uid }}')" 
                                                        class="px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold transition shadow-md flex items-center gap-1">
                                                    ✏️ Edit
                                                </button>
                                                <button type="button" onclick="printHybridCard('{{ addslashes($staff->name) }}', '{{ addslashes($staff->position ?? 'Staf TU') }}', '{{ $staff->rfid_uid }}', 'TATA USAHA')" 
                                                        class="px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-md flex items-center gap-1">
                                                    🖨️ Cetak
                                                </button>
                                                <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" onsubmit="return confirm('Hapus staf TU {{ addslashes($staff->name) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-md flex items-center gap-1">
                                                        🗑️ Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="p-8 text-center text-slate-400">Belum ada data tenaga kependidikan / TU.</td>
                                    </tr>
                                    @endforelse
                                @endisset
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= MODAL EDIT SISWA ================= -->
        <div x-show="showEditStudentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" style="display: none;">
            <div @click.away="showEditStudentModal = false" class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl w-full max-w-md space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Edit Data Siswa</h3>
                    <button @click="showEditStudentModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>
                <form :action="'{{ url('admin/student') }}/' + editStudentId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">NIS</label>
                        <input type="text" name="nis" x-model="editStudentNis" required class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="editStudentName" required class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kelas</label>
                        <select name="class_name" x-model="editStudentClass" required class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white outline-none">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($schoolClasses as $cls)
                                <option value="{{ $cls->name }}">{{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">UID Chip RFID / QR Code</label>
                        <input type="text" name="rfid_uid" x-model="editStudentUid" required class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono text-slate-900 dark:text-white outline-none">
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="showEditStudentModal = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition">Batal</button>
                        <button type="submit" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-emerald-600/20">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL EDIT GURU ================= -->
        <div x-show="showEditTeacherModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" style="display: none;">
            <div @click.away="showEditTeacherModal = false" class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl w-full max-w-md space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Edit Data Guru</h3>
                    <button @click="showEditTeacherModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>
                <form :action="'{{ url('admin/teacher') }}/' + editTeacherId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap & Gelar</label>
                        <input type="text" name="name" x-model="editTeacherName" required class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">UID Chip RFID / QR Code</label>
                        <input type="text" name="rfid_uid" x-model="editTeacherUid" required class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono text-slate-900 dark:text-white outline-none">
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="showEditTeacherModal = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition">Batal</button>
                        <button type="submit" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-emerald-600/20">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL EDIT STAFF TU ================= -->
        <div x-show="showEditStaffModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" style="display: none;">
            <div @click.away="showEditStaffModal = false" class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl w-full max-w-md space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Edit Data Staf TU</h3>
                    <button @click="showEditStaffModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>
                <form :action="'{{ url('admin/staff') }}/' + editStaffId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap & Gelar</label>
                        <input type="text" name="name" x-model="editStaffName" required class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Jabatan / Posisi</label>
                        <input type="text" name="position" x-model="editStaffPosition" class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">UID Chip RFID / QR Code</label>
                        <input type="text" name="rfid_uid" x-model="editStaffUid" required class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono text-slate-900 dark:text-white outline-none">
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="showEditStaffModal = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition">Batal</button>
                        <button type="submit" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-lg">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL EDIT SHIFT GURU ================= -->
        <div x-show="showEditShiftModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" style="display: none;">
            <div @click.away="showEditShiftModal = false" class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl w-full max-w-md space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Edit Shift (<span x-text="editShiftDay"></span>)</h3>
                    <button @click="showEditShiftModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                </div>
                <form :action="'{{ url('admin/shifts') }}/' + editShiftId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Jam Masuk</label>
                            <input type="time" name="shift_start" x-model="editShiftStart" required class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono text-slate-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Jam Pulang</label>
                            <input type="time" name="shift_end" x-model="editShiftEnd" class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono text-slate-900 dark:text-white outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Jumlah Jam Mengajar (JP)</label>
                        <input type="number" name="total_hours" x-model="editShiftHours" min="1" max="12" class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white outline-none">
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="showEditShiftModal = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition">Batal</button>
                        <button type="submit" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-emerald-600/20">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Script Render QR Code & Tombol Cetak Kartu Hybrid -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".qrcode").forEach((element) => {
                let value = element.getAttribute("data-value");
                let size = parseInt(element.getAttribute("data-size")) || 52;
                if (value) {
                    new QRCode(element, {
                        text: value,
                        width: size,
                        height: size,
                        colorDark : "#0f172a",
                        colorLight : "#ffffff",
                        correctLevel : QRCode.CorrectLevel.H
                    });
                }
            });
        });

        function dataManager() {
            return {
                activeTab: 'siswa',
                studentNis: '',
                studentCode: '',
                teacherCode: '',
                staffCode: '',

                // State Modal Edit Siswa
                showEditStudentModal: false,
                editStudentId: null,
                editStudentNis: '',
                editStudentName: '',
                editStudentClass: '',
                editStudentUid: '',

                // State Modal Edit Guru
                showEditTeacherModal: false,
                editTeacherId: null,
                editTeacherName: '',
                editTeacherUid: '',

                // State Modal Edit Staf TU
                showEditStaffModal: false,
                editStaffId: null,
                editStaffName: '',
                editStaffPosition: '',
                editStaffUid: '',

                // State Modal Edit Shift Guru
                showEditShiftModal: false,
                editShiftId: null,
                editShiftDay: '',
                editShiftStart: '',
                editShiftEnd: '',
                editShiftHours: '',

                openEditStudent(id, nis, name, className, uid) {
                    this.editStudentId = id;
                    this.editStudentNis = nis;
                    this.editStudentName = name;
                    this.editStudentClass = className;
                    this.editStudentUid = uid;
                    this.showEditStudentModal = true;
                },

                openEditTeacher(id, name, uid) {
                    this.editTeacherId = id;
                    this.editTeacherName = name;
                    this.editTeacherUid = uid;
                    this.showEditTeacherModal = true;
                },

                openEditStaff(id, name, position, uid) {
                    this.editStaffId = id;
                    this.editStaffName = name;
                    this.editStaffPosition = position;
                    this.editStaffUid = uid;
                    this.showEditStaffModal = true;
                },

                openEditShift(id, day, start, end, hours) {
                    this.editShiftId = id;
                    this.editShiftDay = day;
                    this.editShiftStart = start;
                    this.editShiftEnd = end;
                    this.editShiftHours = hours;
                    this.showEditShiftModal = true;
                },

                generateRandomStudent() {
                    let randomNum = Math.floor(100000 + Math.random() * 900000);
                    this.studentCode = 'HYBRID-STU-' + randomNum;
                },

                generateRandomTeacher() {
                    let randomNum = Math.floor(100000 + Math.random() * 900000);
                    this.teacherCode = 'HYBRID-TCH-' + randomNum;
                },

                generateRandomStaff() {
                    let randomNum = Math.floor(100000 + Math.random() * 900000);
                    this.staffCode = 'HYBRID-STF-' + randomNum;
                }
            }
        }

        function printHybridCard(name, subtitle, code, role) {
            let printWindow = window.open('', '_blank', 'width=400,height=300');
            
            let htmlContent = [
                '<html>',
                '<head>',
                '    <title>Cetak Kartu Hybrid - ' + name + '</title>',
                '    <style>',
                '        @page { size: 85.6mm 54mm; margin: 0; }',
                '        body { margin: 0; padding: 0; -webkit-print-color-adjust: exact; font-family: sans-serif; }',
                '        .id-card { width: 85.6mm; height: 54mm; box-sizing: border-box; padding: 4mm; background: linear-gradient(135deg, #0f172a 0%, #064e3b 100%); color: #ffffff; display: flex; flex-direction: column; justify-content: space-between; }',
                '        .card-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 2px; }',
                '        .school-name { font-size: 7px; font-weight: bold; color: #34d399; text-transform: uppercase; }',
                '        .card-role { font-size: 6px; background: #059669; padding: 1px 4px; border-radius: 3px; }',
                '        .card-body { display: flex; align-items: center; gap: 8px; margin: 4px 0; }',
                '        .chip-box { width: 30px; height: 24px; background: linear-gradient(135deg, #facc15 0%, #ca8a04 100%); border-radius: 3px; border: 1px solid #eab308; display: flex; align-items: center; justify-content: center; font-size: 7px; font-weight: bold; color: #713f12; }',
                '        .qr-wrapper { background: #ffffff; padding: 2px; border-radius: 3px; }',
                '        .user-info h3 { font-size: 10px; margin: 0; }',
                '        .user-info p { font-size: 7px; margin: 0; opacity: 0.8; }',
                '        .card-footer { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2px; display: flex; justify-content: space-between; font-size: 7px; color: #94a3b8; font-family: monospace; }',
                '    </style>',
                '    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>',
                '</head>',
                '<body>',
                '    <div class="id-card">',
                '        <div class="card-header">',
                '            <span class="school-name">SMK Hijau Muda (' + role + ')</span>',
                '            <span class="card-role">' + role + '</span>',
                '        </div>',
                '        <div class="card-body">',
                '            <div class="chip-box">CHIP</div>',
                '            <div class="qr-wrapper" id="qrcode"></div>',
                '            <div class="user-info">',
                '                <h3>' + name + '</h3>',
                '                <p>' + subtitle + '</p>',
                '            </div>',
                '        </div>',
                '        <div class="card-footer">',
                '            <span>ID: ' + code + '</span>',
                '            <span>● HYBRID READY</span>',
                '        </div>',
                '    </div>',
                '    <script>',
                '        new QRCode(document.getElementById("qrcode"), { text: "' + code + '", width: 48, height: 48, correctLevel: QRCode.CorrectLevel.H });',
                '        setTimeout(() => { window.print(); window.close(); }, 500);',
                '    <\/script>',
                '</body>',
                '</html>'
            ].join('\n');

            printWindow.document.write(htmlContent);
            printWindow.document.close();
        }
    </script>
@endsection