<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi Kiosk</title>
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js untuk reaktivitas UI & Tabs -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Axios untuk request AJAX ke backend -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Library Html5-Qrcode untuk pemindai kamera -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex flex-col items-center justify-center p-4" x-data="kioskApp()" x-init="initApp()">

    <div class="max-w-md w-full bg-slate-800 p-8 rounded-3xl shadow-2xl text-center border border-slate-700">
        <h1 class="text-2xl font-bold mb-1">SIFAT (Sistem Absensi)</h1>
        <p class="text-slate-400 text-sm mb-6">Pilih metode absensi di bawah ini</p>

        <!-- Pilihan Menu / Tab Mode -->
        <div class="grid grid-cols-2 gap-2 bg-slate-900 p-1.5 rounded-2xl mb-6 border border-slate-700/50">
            <button @click="switchMode('tap')" 
                    :class="mode === 'tap' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:text-white'"
                    class="py-2.5 rounded-xl font-bold text-xs transition">
                ⚡ Tap Kartu / USB
            </button>
            <button @click="switchMode('camera')" 
                    :class="mode === 'camera' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:text-white'"
                    class="py-2.5 rounded-xl font-bold text-xs transition">
                📷 Kamera (QR Code)
            </button>
        </div>

        <!-- Kotak Informasi Status / Feedback -->
        <div class="p-4 rounded-2xl mb-6 transition-all duration-300 shadow-inner text-sm whitespace-pre-line" 
             :class="statusClass" 
             x-text="message">
             Silakan lakukan absensi...
        </div>

        <!-- ================= MODE 1: TAP / USB SCANNER ================= -->
        <div x-show="mode === 'tap'" class="py-4">
            <div class="w-16 h-16 bg-blue-500/10 text-blue-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl border border-blue-500/20 animate-pulse">
                💳
            </div>
            <p class="text-xs text-slate-400">Silakan tap kartu RFID atau gunakan Barcode Scanner pada mesin.</p>
            
            <!-- Form tersembunyi untuk menangkap ketikan dari USB / Barcode Scanner -->
            <form @submit.prevent="submitData(uid)">
                <input type="text" x-model="uid" id="rfid-input" class="opacity-0 absolute" autofocus autocomplete="off">
            </form>
        </div>

        <!-- ================= MODE 2: KAMERA / QR SCANNER ================= -->
        <div x-show="mode === 'camera'" class="space-y-4">
            <div id="reader" class="w-full rounded-2xl overflow-hidden border border-slate-700 bg-slate-900"></div>
            <p class="text-xs text-slate-400">Arahkan QR Code kartu ke depan kamera perangkat.</p>
        </div>
    </div>

    <script>
        function kioskApp() {
            return {
                mode: 'tap',
                uid: '',
                message: 'Menunggu kartu atau scan QR...',
                statusClass: 'bg-slate-700 text-slate-300 border border-slate-600',
                html5QrCode: null,
                lastScans: {}, 
                voices: [],

                initApp() {
                    // Memuat daftar suara browser agar siap digunakan
                    this.loadVoices();
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.onvoiceschanged = () => this.loadVoices();
                    }

                    // Penjaga fokus input otomatis
                    setInterval(() => {
                        if (this.mode === 'tap') {
                            let input = document.getElementById('rfid-input');
                            if (input && document.activeElement !== input) {
                                input.focus();
                            }
                        }
                    }, 500);
                },

                loadVoices() {
                    if ('speechSynthesis' in window) {
                        this.voices = window.speechSynthesis.getVoices();
                    }
                },

                switchMode(selectedMode) {
                    this.mode = selectedMode;
                    this.message = 'Silakan lakukan absensi...';
                    this.statusClass = 'bg-slate-700 text-slate-300 border border-slate-600';

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
                        { fps: 10, qrbox: { width: 220, height: 220 } },
                        (decodedText) => {
                            this.submitData(decodedText);
                        },
                        (errorMessage) => {}
                    ).catch(() => {
                        this.message = "Gagal mengakses kamera. Pastikan izin kamera diaktifkan.";
                        this.statusClass = 'bg-rose-600 text-white font-semibold';
                    });
                },

                stopCamera() {
                    if (this.html5QrCode && this.html5QrCode.isScanning) {
                        this.html5QrCode.stop().catch(err => console.log(err));
                    }
                },

                // Efek Audio Pendukung (Beep Pendek)
                playBeep(isSuccess = true) {
                    try {
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        
                        osc.type = isSuccess ? 'sine' : 'sawtooth';
                        osc.frequency.setValueAtTime(isSuccess ? 800 : 300, audioCtx.currentTime); // Frekuensi sukses vs gagal
                        
                        gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.15);
                        
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        
                        osc.start();
                        osc.stop(audioCtx.currentTime + 0.15);
                    } catch (e) {
                        // Abaikan jika browser memblokir audio context otomatis sebelum ada interaksi
                    }
                },

                // Fungsi Suara yang Lebih Natural & Menggunakan Pemilihan Suara Terbaik
                speak(text) {
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel();
                        
                        let utterance = new SpeechSynthesisUtterance(text);
                        utterance.lang = 'id-ID';
                        utterance.rate = 1.05; // Sedikit lebih dinamis/cepat agar tidak kaku
                        utterance.pitch = 1.0; // Nada suara normal

                        // Cari suara bahasa Indonesia terbaik yang tersedia di perangkat (utamakan Google / Natural jika ada)
                        let indonesianVoices = this.voices.filter(v => v.lang.includes('id') || v.lang.includes('ID'));
                        if (indonesianVoices.length > 0) {
                            // Prioritaskan suara perempuan atau suara berlabel natural/google jika tersedia
                            let selectedVoice = indonesianVoices.find(v => v.name.toLowerCase().includes('google') || v.name.toLowerCase().includes('natural') || v.name.toLowerCase().includes('female')) || indonesianVoices[0];
                            utterance.voice = selectedVoice;
                        }

                        window.speechSynthesis.speak(utterance);
                    }
                },

                submitData(codeValue) {
                    if (!codeValue) return;

                    let now = new Date().getTime();
                    let cooldownTime = 30 * 60 * 1000; // 30 menit

                    if (this.lastScans[codeValue]) {
                        let elapsed = now - this.lastScans[codeValue];
                        if (elapsed < cooldownTime) {
                            this.playBeep(false);
                            this.message = `⚠️ Peringatan!\nKartu ini baru saja melakukan absensi.\nScan berikutnya dapat dilakukan saat anda akan absensi pulang.`;
                            this.statusClass = 'bg-amber-600 text-white font-semibold shadow-amber-950';
                            this.speak("Kartu ini sudah melakukan absensi sebelumnya.");
                            this.uid = '';
                            return;
                        }
                    }

                    // Kirim ke backend Laravel
                    axios.post('/api/tap', { uid: codeValue })
                        .then(response => {
                            let res = response.data;
                            this.lastScans[codeValue] = new Date().getTime();

                            this.message = `${res.name} (${res.role})\n${res.message} [Pukul ${res.time}]`;
                            this.statusClass = 'bg-emerald-600 text-white font-semibold shadow-emerald-950';
                            this.uid = '';

                            // Bunyikan beep sukses, lalu ucapkan nama dan pesan ramah
                            this.playBeep(true);
                            this.speak(`Terima kasih, ${res.name}. ${res.message}. Selamat beraktivitas.`);
                        })
                        .catch(error => {
                            this.message = error.response?.data?.message || 'Kode/Kartu tidak dikenali!';
                            this.statusClass = 'bg-rose-600 text-white font-semibold shadow-rose-950';
                            this.uid = '';

                            // Bunyikan beep error
                            this.playBeep(false);
                            this.speak("Maaf, kartu atau kode QR tidak terdaftar di sistem.");
                        });
                }
            }
        }
    </script>
</body>
</html>