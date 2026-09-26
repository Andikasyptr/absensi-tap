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
        // Ambil tanggal dari filter, default hari ini
        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        // Total siswa & guru terdaftar
        $totalSiswaCount = Student::count();
        $totalGuruCount = Teacher::count();

        // Data absensi pada tanggal tersebut
        $attendances = Attendance::where('date', $selectedDate)->get();

        // Hitung total kehadiran
        $totalHadir = $attendances->count();
        $siswaHadir = $attendances->where('attendable_type', Student::class)->count();
        $guruHadir = $attendances->where('attendable_type', Teacher::class)->count();

        // 🔍 HITUNG KETERLAMBATAN TERPISAH
        $siswaTerlambat = Attendance::where('date', $selectedDate)
            ->where('attendable_type', Student::class)
            ->where('status', 'terlambat')
            ->count();

        $guruTerlambat = Attendance::where('date', $selectedDate)
            ->where('attendable_type', Teacher::class)
            ->where('status', 'terlambat')
            ->count();

        $totalTerlambat = $siswaTerlambat + $guruTerlambat;

        return view('admin.dashboard', compact(
            'selectedDate',
            'totalHadir',
            'siswaHadir',
            'guruHadir',
            'totalSiswaCount',
            'totalGuruCount',
            'siswaTerlambat',
            'guruTerlambat',
            'totalTerlambat'
        ));
    }
}
