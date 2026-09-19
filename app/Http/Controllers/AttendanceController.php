<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Halaman Kiosk
    public function kiosk()
    {
        return view('attendance.kiosk');
    }

    // Proses Tap Kartu
    public function store(Request $request)
{
    $uid = $request->uid;
    $todayEnglish = date('l'); // Nama hari: Monday, Tuesday, dst.
    $todayDate = Carbon::today()->toDateString();
    $currentTime = Carbon::now()->toTimeString();

    // 1. Cek apakah kartu milik Siswa atau Guru
    $student = Student::where('rfid_uid', $uid)->first();
    $teacher = Teacher::where('rfid_uid', $uid)->with(['shifts' => function($query) use ($todayEnglish) {
        $query->where('day', $todayEnglish);
    }])->first();

    if (!$student && !$teacher) {
        return response()->json(['message' => 'Kartu atau QR Code tidak dikenali!'], 404);
    }

    // 2. Jika yang absen adalah GURU
    if ($teacher) {
        $todayShift = $teacher->shifts->first();
        $shiftStart = $todayShift ? $todayShift->shift_start : '07:30:00';
        $status = ($currentTime > $shiftStart) ? 'terlambat' : 'hadir';

        // Cek apakah sudah ada absensi hari ini
        $attendance = Attendance::where('attendable_type', Teacher::class)
            ->where('attendable_id', $teacher->id)
            ->where('date', $todayDate)
            ->first();

        if (!$attendance) {
            // Belum absen -> Catat sebagai Masuk (time_in)
            Attendance::create([
                'attendable_type' => Teacher::class,
                'attendable_id' => $teacher->id,
                'date' => $todayDate,
                'time_in' => $currentTime, // Pastikan kolom database namanya time_in
                'status' => $status,
            ]);
            $msg = "Absensi Masuk berhasil ($status).";
        } else if (!$attendance->time_out) {
            // Sudah absen masuk tapi belum absen pulang -> Catat Pulang (time_out)
            $attendance->update([
                'time_out' => $currentTime
            ]);
            $msg = "Absensi Pulang berhasil dicatat.";
        } else {
            $msg = "Anda sudah melakukan absensi masuk dan pulang hari ini.";
        }

        return response()->json([
            'name' => $teacher->name,
            'role' => 'Guru',
            'message' => $msg,
            'time' => date('H:i')
        ]);
    }

    // 3. Jika yang absen adalah SISWA
    if ($student) {
        $standardShiftStart = '07:15:00';
        $status = ($currentTime > $standardShiftStart) ? 'terlambat' : 'hadir';

        $attendance = Attendance::where('attendable_type', Student::class)
            ->where('attendable_id', $student->id)
            ->where('date', $todayDate)
            ->first();

        if (!$attendance) {
            // Catat Masuk
            Attendance::create([
                'attendable_type' => Student::class,
                'attendable_id' => $student->id,
                'date' => $todayDate,
                'time_in' => $currentTime,
                'status' => $status,
            ]);
            $msg = "Absensi Masuk berhasil ($status).";
        } else if (!$attendance->time_out) {
            // Catat Pulang
            $attendance->update([
                'time_out' => $currentTime
            ]);
            $msg = "Absensi Pulang berhasil dicatat.";
        } else {
            $msg = "Siswa sudah melakukan absensi masuk dan pulang hari ini.";
        }

        return response()->json([
            'name' => $student->name,
            'role' => 'Siswa - ' . $student->class_name,
            'message' => $msg,
            'time' => date('H:i')
        ]);
    }
}
    // --- REKAP ABSENSI ---
    public function rekapIndex(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $roleFilter = $request->input('role');

        $query = Attendance::with('attendable')->where('date', $date);

        if ($roleFilter == 'Siswa') {
            $query->where('attendable_type', Student::class);
        } elseif ($roleFilter == 'Guru') {
            $query->where('attendable_type', Teacher::class);
        }

        $attendances = $query->latest()->get();

        $totalHadir = $attendances->count();
        $totalSiswa = $attendances->where('attendable_type', Student::class)->count();
        $totalGuru = $attendances->where('attendable_type', Teacher::class)->count();

        return view('admin.rekap', compact('attendances', 'totalHadir', 'totalSiswa', 'totalGuru', 'date', 'roleFilter'));
    }

    // Menghapus data rekap absensi
    public function destroyRekap($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->back()->with('success', 'Data absensi berhasil dihapus.');
    }
}