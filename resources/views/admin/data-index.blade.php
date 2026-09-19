@extends('layouts.admin')

@section('content')
    <!-- CDN Library QRCode.js untuk render QR Code secara visual -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <div class="mb-8 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Manajemen Data & Registrasi</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Generate QR Code, pengaturan shift jam dinamis per hari untuk guru, dan data siswa.</p>
        </div>
    </div>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-emerald-500 text-white p-4 rounded-2xl mb-6 shadow-lg shadow-emerald-500/20 flex items-center gap-3">
            <span>✨</span>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Container Utama dengan Alpine.js -->
    <div x-data="dataManager()" class="space-y-6">
        
        <!-- Tab Navigation Buttons -->
        <div class="flex space-x-2 border-b border-slate-200 dark:border-slate-800 pb-4">
            <button @click="activeTab = 'siswa'" 
                    :class="activeTab === 'siswa' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800'"
                    class="px-6 py-2.5 rounded-xl font-bold text-sm transition">
                👥 Data Siswa
            </button>
            <button @click="activeTab = 'guru'" 
                    :class="activeTab === 'guru' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800'"
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
                            <input type="text" name="nis" x-model="studentNis" @input="generateStudentCode" required placeholder="Contoh: 10293" class="w-full mt-1 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="name" required placeholder="Nama lengkap siswa" class="w-full mt-1 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kelas</label>
                            <input type="text" name="class_name" required placeholder="Contoh: XII RPL 1" class="w-full mt-1 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-sm">
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode QR / UID Kartu</label>
                                <button type="button" @click="generateRandomStudent" class="text-xs text-blue-500 hover:underline font-medium">✨ Generate Random</button>
                            </div>
                            <input type="text" name="rfid_uid" x-model="studentCode" required placeholder="Otomatis terisi..." class="w-full p-3 bg-amber-50/50 dark:bg-slate-800/60 border border-amber-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-sm font-mono">
                        </div>
                        <button type="submit" class="w-full py-3 bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-slate-900/20 transition text-sm">
                            Simpan Data Siswa
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabel Daftar Siswa -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Daftar Kartu Siswa Terdaftar</h3>
                    </div>
                    <div class="overflow-x-auto max-h-[600px]">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="p-4 font-semibold">Pratinjau Kartu Identitas</th>
                                    <th class="p-4 font-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($students as $student)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                                    <td class="p-4">
                                        <div class="w-72 bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-4 shadow-md border border-slate-700 flex items-center gap-4">
                                            <div class="bg-white p-2 rounded-xl shrink-0">
                                                <div class="qrcode" data-value="{{ $student->rfid_uid }}" data-size="76"></div>
                                            </div>
                                            <div class="overflow-hidden">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-400">Kartu Siswa</span>
                                                <h4 class="font-bold text-sm text-white truncate">{{ $student->name }}</h4>
                                                <p class="text-xs text-slate-300">Kelas: {{ $student->class_name }}</p>
                                                <p class="text-[10px] text-slate-400">NIS: {{ $student->nis }}</p>
                                                <span class="inline-block mt-1 font-mono text-[9px] bg-slate-800 text-slate-300 px-2 py-0.5 rounded border border-slate-600">{{ $student->rfid_uid }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center align-middle">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" onclick="printSingleCard('{{ addslashes($student->name) }}', 'Kelas: {{ addslashes($student->class_name) }} (NIS: {{ $student->nis }})', '{{ $student->rfid_uid }}', 'SISWA')" 
                                                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-blue-600/20 flex items-center gap-1">
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
                            <input type="text" name="name" x-model="teacherName" required placeholder="Contoh: Budi Santoso, S.Pd" class="w-full mt-1 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-sm">
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode QR / UID Kartu</label>
                                <button type="button" @click="generateRandomTeacher" class="text-xs text-blue-500 hover:underline font-medium">✨ Generate Random</button>
                            </div>
                            <input type="text" name="rfid_uid" x-model="teacherCode" required placeholder="Otomatis terisi..." class="w-full p-3 bg-amber-50/50 dark:bg-slate-800/60 border border-amber-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-amber-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-sm font-mono">
                        </div>
                        <button type="submit" class="w-full py-3 bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-slate-900/20 transition text-sm">
                            Simpan Data Guru
                        </button>
                    </form>
                </div>

                <!-- Form Setting Shift Harian menggunakan ShiftController -->
                @isset($teachers)
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm transition-colors">
                    <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                        <span>⏰</span> Atur Shift Masuk Harian Guru
                    </h3>
                    <p class="text-xs text-slate-400 mb-4">Tentukan jam masuk spesifik untuk hari tertentu bagi masing-masing guru.</p>
                    
                   <form action="{{ route('admin.shifts.store') }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Pilih Guru</label>
                                <!-- PASTIKAN ADA attribute name="teacher_id" -->
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

                        <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs transition shadow-md shadow-blue-600/20">
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
                                    <th class="p-4 font-semibold">Pratinjau Kartu & Detail Shift Mingguan</th>
                                    <th class="p-4 font-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @isset($teachers)
                                    @forelse($teachers as $teacher)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition align-top">
                                        <td class="p-4 space-y-3">
                                            <!-- Kartu ID Guru -->
                                            <div class="w-80 bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-4 shadow-md border border-slate-700 flex items-center gap-4">
                                                <div class="bg-white p-2 rounded-xl shrink-0">
                                                    <div class="qrcode" data-value="{{ $teacher->rfid_uid }}" data-size="76"></div>
                                                </div>
                                                <div class="overflow-hidden">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">Kartu Guru</span>
                                                    <h4 class="font-bold text-sm text-white truncate">{{ $teacher->name }}</h4>
                                                    <p class="text-xs text-slate-300">Staf Pengajar</p>
                                                    <span class="inline-block mt-2 font-mono text-[9px] bg-slate-800 text-slate-300 px-2 py-0.5 rounded border border-slate-600">{{ $teacher->rfid_uid }}</span>
                                                </div>
                                            </div>

                                            <!-- List Shift Berbeda Tiap Hari untuk Guru Ini -->
                                            <div class="max-w-md bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                                                <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-2">📅 Jam Masuk Berdasarkan Hari:</p>
                                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                                    @isset($teacher->shifts)
                                                        @forelse($teacher->shifts as $ts)
                                                            <div class="bg-white dark:bg-slate-800 p-2 rounded-lg border border-slate-200 dark:border-slate-700 text-[11px]">
                                                                <span class="font-semibold text-blue-600 dark:text-blue-400 block">{{ $ts->day }}</span>
                                                                <span class="font-mono text-slate-700 dark:text-slate-200">Masuk: {{ $ts->shift_start }}</span>
                                                            </div>
                                                        @empty
                                                            <p class="text-[11px] text-slate-400 col-span-3 italic">Belum ada jadwal shift harian diatur untuk guru ini.</p>
                                                        @endforelse
                                                    @endisset
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 text-center align-middle">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" onclick="printSingleCard('{{ addslashes($teacher->name) }}', 'Kartu Pengajar Aktif', '{{ $teacher->rfid_uid }}', 'GURU')" 
                                                        class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-blue-600/20 flex items-center gap-1">
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

    <!-- Script Render QR Code & Tombol Cetak -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".qrcode").forEach((element) => {
                let value = element.getAttribute("data-value");
                let size = parseInt(element.getAttribute("data-size")) || 64;
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

                generateStudentCode() {
                    if (this.studentNis) {
                        this.studentCode = 'QR-STU-' + this.studentNis;
                    } else {
                        this.studentCode = '';
                    }
                },

                generateRandomStudent() {
                    let randomNum = Math.floor(100000 + Math.random() * 900000);
                    this.studentCode = 'QR-STU-' + randomNum;
                },

                generateRandomTeacher() {
                    let randomNum = Math.floor(100000 + Math.random() * 900000);
                    this.teacherCode = 'QR-TCH-' + randomNum;
                }
            }
        }

        function printSingleCard(name, subtitle, code, role) {
            let printWindow = window.open('', '_blank', 'width=400,height=300');
            
            let htmlContent = [
                '<html>',
                '<head>',
                '    <title>Cetak Kartu - ' + name + '</title>',
                '    <style>',
                '        @page { size: 85.6mm 54mm; margin: 0; }',
                '        body { margin: 0; padding: 0; -webkit-print-color-adjust: exact; }',
                '        .id-card { width: 85.6mm; height: 54mm; box-sizing: border-box; padding: 4mm; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; display: flex; flex-direction: column; justify-content: space-between; }',
                '        .card-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 2px; }',
                '        .school-name { font-size: 7px; font-weight: bold; color: #38bdf8; text-transform: uppercase; }',
                '        .card-role { font-size: 6px; background: #2563eb; padding: 1px 4px; border-radius: 3px; }',
                '        .card-body { display: flex; align-items: center; gap: 8px; margin: 5px 0; }',
                '        .qr-wrapper { background: #ffffff; padding: 3px; border-radius: 4px; }',
                '        .user-info h3 { font-size: 10px; margin: 0; }',
                '        .user-info p { font-size: 7px; margin: 0; opacity: 0.8; }',
                '        .card-footer { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2px; display: flex; justify-content: space-between; font-size: 7px; color: #94a3b8; }',
                '    </style>',
                '    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>',
                '</head>',
                '<body>',
                '    <div class="id-card">',
                '        <div class="card-header">',
                '            <span class="school-name">Identitas ' + role + '</span>',
                '            <span class="card-role">' + role + '</span>',
                '        </div>',
                '        <div class="card-body">',
                '            <div class="qr-wrapper" id="qrcode"></div>',
                '            <div class="user-info">',
                '                <h3>' + name + '</h3>',
                '                <p>' + subtitle + '</p>',
                '            </div>',
                '        </div>',
                '        <div class="card-footer">',
                '            <span>' + code + '</span>',
                '            <span>● AKTIF</span>',
                '        </div>',
                '    </div>',
                '    <script>',
                '        new QRCode(document.getElementById("qrcode"), { text: "' + code + '", width: 64, height: 64, correctLevel: QRCode.CorrectLevel.H });',
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