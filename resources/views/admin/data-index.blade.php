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
            <p class="text-slate-500 dark:text-slate-400 text-sm">Registrasi kartu chip RFID & QR Code, pengaturan shift jam dinamis per hari untuk guru, dan data siswa.</p>
        </div>
    </div>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-emerald-600 text-white p-4 rounded-2xl mb-6 shadow-lg shadow-emerald-950 flex items-center gap-3">
            <span>✨</span>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Container Utama dengan Alpine.js -->
    <div x-data="dataManager()" class="space-y-6">
        
        <!-- Tab Navigation Buttons -->
        <div class="flex space-x-2 border-b border-slate-200 dark:border-slate-800 pb-4">
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
                            <input type="text" name="class_name" required placeholder="Contoh: XII RPL 1" class="w-full mt-1 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-sm">
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
                                        <!-- Desain Kartu Hybrid Style (Nuansa Emerald) -->
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
            
            <!-- Kolom Kiri: Form Tambah Guru & Form Atur Shift per Hari untuk Guru Tertentu -->
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

                        <!-- INPUT JUMLAH JAM MENGAJAR BARU -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Jumlah Jam Mengajar (JP)</label>
                            <input type="number" name="total_hours" min="1" max="12" placeholder="Contoh: 4" class="w-full mt-1 p-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none">
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs transition shadow-md shadow-emerald-600/20">
                            Simpan Shift Harian
                        </button>
                    </form>
                </div>
                @endisset
            </div>

            <!-- Kolom Kanan: Tabel Daftar Guru & Ringkasan Shift Berbeda Setiap Hari -->
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
                                            <!-- Kartu ID Guru Hybrid Style (Nuansa Emerald) -->
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

                                            <!-- List Shift Berbeda Tiap Hari untuk Guru Ini -->
                                            <div class="max-w-md bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                                                <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-2">📅 Jam Masuk & Jam Mengajar Berdasarkan Hari:</p>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    @isset($teacher->shifts)
                                                        @forelse($teacher->shifts as $ts)
                                                            <div class="bg-white dark:bg-slate-800 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700 text-[11px]">
                                                                <span class="font-semibold text-emerald-600 dark:text-emerald-400 block">{{ $ts->day }}</span>
                                                                <span class="font-mono text-slate-700 dark:text-slate-200 block">Masuk: {{ $ts->shift_start }}</span>
                                                                @if(isset($ts->total_hours))
                                                                    <span class="text-xs font-bold text-blue-500 mt-1 block">📌 {{ $ts->total_hours }} Jam Pelajaran</span>
                                                                @endif
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

                generateRandomStudent() {
                    let randomNum = Math.floor(100000 + Math.random() * 900000);
                    this.studentCode = 'HYBRID-STU-' + randomNum;
                },

                generateRandomTeacher() {
                    let randomNum = Math.floor(100000 + Math.random() * 900000);
                    this.teacherCode = 'HYBRID-TCH-' + randomNum;
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