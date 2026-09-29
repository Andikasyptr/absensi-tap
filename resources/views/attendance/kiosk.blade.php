<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk Smart Attendance - SMK Hijau Muda</title>
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js untuk reaktivitas UI & Tabs -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Axios untuk request AJAX ke backend -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Library Html5-Qrcode untuk pemindai kamera -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 text-slate-100 h-full flex flex-col justify-between p-4 sm:p-6 overflow-x-hidden selection:bg-emerald-500 selection:text-white" x-data="kioskApp()" x-init="initApp()">

    <!-- Header / Top Bar Profesional Sekolah -->
    <header class="w-full max-w-5xl mx-auto flex flex-col sm:flex-row justify-between items-center bg-slate-900/90 backdrop-blur-xl border border-emerald-500/20 px-6 py-4 rounded-3xl shadow-2xl gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-emerald-600/30">
                🏫
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-extrabold tracking-tight text-white uppercase">SMK Hijau Muda</h2>
                    <span class="bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-[10px] font-bold px-2 py-0.5 rounded-full">Official Kiosk</span>
                </div>
                <p class="text-xs text-slate-400 font-medium">Sistem Kehadiran Terpadu Siswa, Guru & Staf TU (Hybrid Chip & QR)</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3 bg-slate-950/80 border border-slate-800 px-4 py-2 rounded-2xl shadow-inner">
            <div class="text-right">
                <div class="text-xs font-mono font-bold text-emerald-400" x-text="currentTime">00:00:00 WIB</div>
                <div class="text-[10px] text-slate-400 font-medium" x-text="currentDate">Senin, 01 Januari 2026</div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 text-sm">
                🕒
            </div>
        </div>
    </header>

    <!-- Main Container Card -->
    <main class="w-full max-w-2xl mx-auto my-auto py-4">
        <div class="bg-slate-900/95 backdrop-blur-2xl p-8 sm:p-10 rounded-3xl shadow-2xl border border-slate-800 text-center relative overflow-hidden">
            
            <!-- Elemen Dekoratif Aksen Cahaya -->
            <div class="absolute -top-32 -left-32 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -right-32 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                
                <!-- Status Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-emerald-950/60 border border-emerald-500/30 rounded-full mb-4 shadow-sm">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-ping"></span>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-300">Terminal Absensi Siap</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white mb-2">Absensi Kehadiran Sekolah</h1>
                <p class="text-slate-400 text-xs sm:text-sm mb-6 font-medium">Silakan tempelkan kartu identitas ber-chip pada reader atau pindai kode QR Anda.</p>

                <!-- Pilihan Menu / Tab Mode Interaktif -->
                <div class="grid grid-cols-2 gap-2 bg-slate-950 p-1.5 rounded-2xl mb-8 border border-slate-800/80 shadow-inner max-w-md mx-auto">
                    <button @click="switchMode('chip')" 
                            :class="mode === 'chip' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/30 scale-[1.02]' : 'text-slate-400 hover:text-white'"
                            class="py-3 rounded-xl font-bold text-xs transition-all duration-300 flex items-center justify-center gap-2">
                        <span>💳</span> Tap Kartu / USB
                    </button>
                    <button @click="switchMode('camera')" 
                            :class="mode === 'camera' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/30 scale-[1.02]' : 'text-slate-400 hover:text-white'"
                            class="py-3 rounded-xl font-bold text-xs transition-all duration-300 flex items-center justify-center gap-2">
                        <span>📷</span> Kamera QR
                    </button>
                </div>

                <!-- Kotak Informasi Status / Feedback Dinamis -->
                <div class="p-5 sm:p-6 rounded-2xl mb-8 transition-all duration-300 shadow-inner text-sm whitespace-pre-line border font-semibold flex flex-col items-center justify-center min-h-[110px]" 
                     :class="statusClass">
                    <span x-text="message" class="leading-relaxed">Menunggu kartu chip atau scan QR...</span>
                </div>

                <!-- ================= MODE 1: CHIP / RFID / USB SCANNER ================= -->
                <div x-show="mode === 'chip'" class="py-4 space-y-4">
                    <div class="w-24 h-24 bg-gradient-to-tr from-emerald-500/10 to-teal-500/20 text-emerald-400 rounded-3xl flex items-center justify-center mx-auto text-4xl border border-emerald-500/30 shadow-inner animate-pulse">
                        💳
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-emerald-300">Area Sensor Kartu Aktif</p>
                        <p class="text-[11px] text-slate-400">Tap kartu Anda di dekat mesin reader untuk merekam data kehadiran secara instan.</p>
                    </div>
                    
                    <form @submit.prevent="submitData(uid)">
                        <input type="text" x-model="uid" id="rfid-input" class="opacity-0 absolute" autofocus autocomplete="off">
                    </form>
                </div>

                <!-- ================= MODE 2: KAMERA / QR SCANNER ================= -->
                <div x-show="mode === 'camera'" class="space-y-4">
                    <div id="reader" class="w-full max-w-sm mx-auto rounded-2xl overflow-hidden border-2 border-emerald-500/40 bg-slate-950 shadow-inner"></div>
                    <p class="text-xs text-slate-400 font-medium">Arahkan QR Code kartu tepat ke area pemindai kamera.</p>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer / Bottom Info Sekolah -->
    <footer class="w-full max-w-5xl mx-auto flex flex-col sm:flex-row justify-between items-center text-slate-400 text-xs py-4 border-t border-slate-900 font-medium gap-2">
        <p>&copy; 2026 SMK Hijau Muda • Seluruh Hak Cipta Dilindungi.</p>
        <div class="flex items-center gap-4 text-[11px]">
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Server Terhubung</span>
            <span>•</span>
            <span>Versi 0.1 Hybrid Kiosk</span>
        </div>
    </footer>

    <!-- Script Kiosk Logika -->
    <script>
        function kioskApp() {
            return {
                mode: 'chip',
                uid: '',
                message: 'Silakan lakukan absensi kehadiran...',
                statusClass: 'bg-slate-950/80 text-slate-300 border-slate-800',
                html5QrCode: null,
                currentTime: '',
                currentDate: '',

                initApp() {
                    this.updateDateTime();
                    setInterval(() => this.updateDateTime(), 1000);

                    // Penjaga fokus input otomatis agar USB/RFID Reader selalu siap
                    setInterval(() => {
                        if (this.mode === 'chip') {
                            let input = document.getElementById('rfid-input');
                            if (input && document.activeElement !== input) {
                                input.focus();
                            }
                        }
                    }, 500);
                },

                updateDateTime() {
                    let now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                },

                switchMode(selectedMode) {
                    this.mode = selectedMode;
                    this.message = 'Silakan lakukan absensi kehadiran...';
                    this.statusClass = 'bg-slate-950/80 text-slate-300 border-slate-800';

                    if (this.mode === 'camera') {
                        setTimeout(() => { this.startCamera(); }, 200);
                    } else {
                        this.stopCamera();
                    }
                },

                startCamera() {
                    if (!this.html5QrCode) {
                        this.html5QrCode = new Html5Qrcode("reader");
                    }
                    
                    this.html5QrCode.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: { width: 200, height: 200 } },
                        (decodedText) => {
                            this.submitData(decodedText);
                        },
                        (errorMessage) => {}
                    ).catch(() => {
                        this.message = "⚠️ Gagal mengakses kamera. Pastikan izin kamera telah diaktifkan di browser.";
                        this.statusClass = 'bg-rose-950/60 text-rose-200 border-rose-800/80 shadow-rose-950/50';
                    });
                },

                stopCamera() {
                    if (this.html5QrCode && this.html5QrCode.isScanning) {
                        this.html5QrCode.stop().catch(err => console.log(err));
                    }
                },

                // Efek Suara Notifikasi (Audio Beep Menggunakan Web Audio API)
                playBeep(isSuccess = true) {
                    try {
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        
                        if (isSuccess) {
                            // Suara sukses: Nada ceria (dua nada naik)
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                            osc.frequency.setValueAtTime(880, audioCtx.currentTime + 0.1); // A5
                        } else {
                            // Suara error/peringatan: Nada rendah datar (sawtooth)
                            osc.type = 'sawtooth';
                            osc.frequency.setValueAtTime(220, audioCtx.currentTime); // A3
                            osc.frequency.setValueAtTime(164.81, audioCtx.currentTime + 0.1); // E3
                        }
                        
                        gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
                        
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        
                        osc.start();
                        osc.stop(audioCtx.currentTime + 0.3);
                    } catch (e) {
                        console.log("Audio Context diblokir browser sebelum interaksi awal.");
                    }
                },

                submitData(codeValue) {
                    if (!codeValue) return;

                    // Kirim data ke backend Laravel (/api/tap)
                    axios.post('/api/tap', { uid: codeValue })
                        .then(response => {
                            let res = response.data;

                            this.message = `✨ BERHASIL TERCATAT\n${res.name} (${res.role})\n${res.message} [Pukul ${res.time}]`;
                            this.statusClass = 'bg-emerald-950/80 text-emerald-200 border-emerald-600/50 shadow-emerald-950/60';
                            this.uid = '';

                            // Bunyikan suara sukses
                            this.playBeep(true);
                        })
                        .catch(error => {
                            let errorMsg = error.response?.data?.message || 'anda belum terdaftar diabsensi';
                            
                            this.message = `⚠️ PERINGATAN ABSENSI\n${errorMsg}`;
                            this.statusClass = 'bg-amber-950/80 text-amber-200 border-amber-600/50 shadow-amber-950/60';
                            this.uid = '';

                            // Bunyikan suara error/peringatan
                            this.playBeep(false);
                        });
                }
            }
        }
    </script>
</body>
</html>