<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
                    'time_in' => $currentTime,
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

    // --- REKAP ABSENSI (AMAN DARI ERROR SHIFTS STUDENT) ---
    public function rekapIndex(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $roleFilter = $request->input('role');
        $statusFilter = $request->input('status'); // Filter status (hadir / terlambat)

        // Base query hanya meload attendable umum tanpa .shifts global
        $query = Attendance::with('attendable')->where('date', $date);

        if ($roleFilter == 'Siswa') {
            $query->where('attendable_type', Student::class);
        } elseif ($roleFilter == 'Guru') {
            // Load shifts khusus jika yang difilter adalah guru
            $query->with('attendable.shifts');
            $query->where('attendable_type', Teacher::class);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $attendances = $query->latest()->get();

        $totalHadir = Attendance::where('date', $date)->count();
        $totalSiswa = Attendance::where('date', $date)->where('attendable_type', Student::class)->count();
        $totalGuru = Attendance::where('date', $date)->where('attendable_type', Teacher::class)->count();

        // Hitung akumulasi total jam mengajar guru dengan pengamanan ketat
        $totalJamGuruAll = 0;
        $guruAttendances = Attendance::with('attendable.shifts')
            ->where('attendable_type', Teacher::class)
            ->where('date', $date)
            ->get();

        foreach($guruAttendances as $gat) {
            if ($gat->attendable && method_exists($gat->attendable, 'shifts')) {
                $dayName = Carbon::parse($gat->date)->format('l');
                $shiftHariIni = $gat->attendable->shifts->where('day', $dayName)->first();
                if ($shiftHariIni && isset($shiftHariIni->total_hours)) {
                    $totalJamGuruAll += (int) $shiftHariIni->total_hours;
                }
            }
        }

        return view('admin.rekap', compact(
            'attendances', 
            'totalHadir', 
            'totalSiswa', 
            'totalGuru', 
            'date', 
            'roleFilter', 
            'statusFilter',
            'totalJamGuruAll'
        ));
    }

    // --- EXPORT LAPORAN KE PDF ---
    public function exportPdf(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $roleFilter = $request->input('role', 'Siswa');
        $statusFilter = $request->input('status');

        $query = Attendance::with('attendable')->where('date', $date);

        if ($roleFilter == 'Siswa') {
            $query->where('attendable_type', Student::class);
        } elseif ($roleFilter == 'Guru') {
            $query->with('attendable.shifts');
            $query->where('attendable_type', Teacher::class);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $attendances = $query->latest()->get();

        // Akumulasi total jam mengajar guru khusus untuk cetak PDF dengan pengaman ketat
        $totalJamGuruAll = 0;
        if ($roleFilter == 'Guru') {
            foreach($attendances as $gat) {
                if ($gat->attendable && method_exists($gat->attendable, 'shifts')) {
                    $dayName = Carbon::parse($gat->date)->format('l');
                    $shiftHariIni = $gat->attendable->shifts->where('day', $dayName)->first();
                    if ($shiftHariIni && isset($shiftHariIni->total_hours)) {
                        $totalJamGuruAll += (int) $shiftHariIni->total_hours;
                    }
                }
            }
        }

        $pdf = Pdf::loadView('admin.rekap-pdf', compact(
            'attendances', 
            'date', 
            'roleFilter', 
            'statusFilter',
            'totalJamGuruAll'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Rekap-Absen-' . $roleFilter . '-' . $date . '.pdf');
    }

    // Menghapus data rekap absensi
    public function destroyRekap($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->back()->with('success', 'Data absensi berhasil dihapus.');
    }
}