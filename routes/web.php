<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\DataController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ClassController;

// Halaman Utama / Welcome
Route::get('/', function () {
    return view('welcome');
});

// --- RUTE AUTENTIKASI ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// --- RANGKAIAN ROUTE SISTEM ABSENSI ---

// 1. Halaman Kiosk (Tempat Tap Kartu RFID / Scan Barcode)
Route::get('/kiosk', [AttendanceController::class, 'kiosk'])->name('attendance.kiosk');

// 2. Endpoint API untuk memproses data saat kartu di-tap / scan
Route::post('/api/tap', [AttendanceController::class, 'store'])->name('attendance.store');


// --- AREA ADMIN (DILINDUNGI MIDDLEWARE AUTH) ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Data Siswa, Guru, & Staf TU (Tambah, Edit, Hapus)
    Route::get('/data', [DataController::class, 'index'])->name('data.index');
    
    Route::post('/student', [DataController::class, 'storeStudent'])->name('student.store');
    Route::put('/student/{id}', [DataController::class, 'updateStudent'])->name('student.update');
    Route::delete('/student/{id}', [DataController::class, 'destroyStudent'])->name('student.destroy');

    Route::post('/teacher', [DataController::class, 'storeTeacher'])->name('teacher.store');
    Route::put('/teacher/{id}', [DataController::class, 'updateTeacher'])->name('teacher.update');
    Route::delete('/teacher/{id}', [DataController::class, 'destroyTeacher'])->name('teacher.destroy');

    // Manajemen Staf TU & Tenaga Kependidikan
    Route::post('/staff', [DataController::class, 'storeStaff'])->name('staff.store');
    Route::put('/staff/{id}', [DataController::class, 'updateStaff'])->name('staff.update');
    Route::delete('/staff/{id}', [DataController::class, 'destroyStaff'])->name('staff.destroy');

    // Manajemen Data Kelas (Rombel)
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    Route::delete('/classes/{id}', [ClassController::class, 'destroy'])->name('classes.destroy');

    // Rekap Absensi Harian
    Route::get('/rekap', [AttendanceController::class, 'rekapIndex'])->name('rekap');
    Route::get('/rekap/pdf', [AttendanceController::class, 'exportPdf'])->name('rekap.pdf');
    Route::delete('/rekap/{id}', [AttendanceController::class, 'destroyRekap'])->name('rekap.destroy');

    // Rekap Periode / Bulanan Guru, TU, & Akumulasi JP
    Route::get('/rekap-bulanan-guru', [AttendanceController::class, 'rekapBulananGuru'])->name('rekap.bulanan.guru');
    Route::get('/rekap-bulanan-guru/pdf', [AttendanceController::class, 'exportPdfBulananGuru'])->name('rekap.bulanan.guru.pdf');

    // Rekap Periode / Bulanan Siswa
    Route::get('/rekap-bulanan-siswa', [AttendanceController::class, 'rekapBulananSiswa'])->name('rekap.bulanan.siswa');
    Route::get('/rekap-bulanan-siswa/pdf', [AttendanceController::class, 'exportPdfBulananSiswa'])->name('rekap.bulanan.siswa.pdf');

    // Route Pengaturan Shift Harian Guru
    Route::post('/shifts', [DataController::class, 'storeShift'])->name('shifts.store');
    Route::put('/shifts/{id}', [DataController::class, 'updateShift'])->name('shifts.update');
    Route::delete('/shifts/{id}', [DataController::class, 'destroyShift'])->name('shifts.destroy');

    // Route CMS Pengaturan Sekolah
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});