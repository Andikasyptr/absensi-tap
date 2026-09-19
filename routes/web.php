<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\DataController;

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
    
    // Dashboard Admin (Menggunakan DashboardController yang terhubung ke database)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Data Siswa & Guru (Pendaftaran Kartu/Barcode)
    Route::get('/data', [DataController::class, 'index'])->name('data.index');
    Route::post('/student', [DataController::class, 'storeStudent'])->name('student.store');
    Route::post('/teacher', [DataController::class, 'storeTeacher'])->name('teacher.store');

    // Route untuk Hapus Data Siswa & Guru
    Route::delete('/student/{id}', [DataController::class, 'destroyStudent'])->name('student.destroy');
    Route::delete('/teacher/{id}', [DataController::class, 'destroyTeacher'])->name('teacher.destroy');

    // Rekap Absensi
    Route::get('/rekap', [AttendanceController::class, 'rekapIndex'])->name('rekap');
    
    // Route untuk Hapus Data Rekap Absensi
    Route::delete('/rekap/{id}', [AttendanceController::class, 'destroyRekap'])->name('rekap.destroy');

    // Route Pengaturan Shift Harian Guru (Di-handle di DataController)
    Route::post('/shifts', [DataController::class, 'storeShift'])->name('shifts.store');
});