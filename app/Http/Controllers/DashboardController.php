<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tanggal dari parameter URL (?date=YYYY-MM-DD), jika kosong gunakan hari ini
        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        // Hitung total master data siswa & guru
        $totalSiswaCount = Student::count();
        $totalGuruCount = Teacher::count();

        // Ambil data absensi berdasarkan tanggal yang dipilih
        $attendancesOnDate = Attendance::whereDate('date', $selectedDate)->get();

        // Hitung statistik kehadiran pada tanggal tersebut
        $totalHadir = $attendancesOnDate->count();
        $siswaHadir = $attendancesOnDate->where('attendable_type', Student::class)->count();
        $guruHadir = $attendancesOnDate->where('attendable_type', Teacher::class)->count();
        $totalTerlambat = $attendancesOnDate->where('status', 'terlambat')->count();

        return view('admin.dashboard', compact(
            'totalSiswaCount',
            'totalGuruCount',
            'totalHadir',
            'siswaHadir',
            'guruHadir',
            'totalTerlambat',
            'selectedDate'
        ));
    }
}