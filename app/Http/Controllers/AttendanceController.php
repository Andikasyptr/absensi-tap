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
        $todayEnglish = date('l'); 
        $todayDate = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        $student = Student::where('rfid_uid', $uid)->first();
        $teacher = Teacher::where('rfid_uid', $uid)->with(['shifts' => function($query) use ($todayEnglish) {
            $query->where('day', $todayEnglish);
        }])->first();

        if (!$student && !$teacher) {
            return response()->json(['message' => 'Kartu atau QR Code tidak dikenali!'], 404);
        }

        if ($teacher) {
            $todayShift = $teacher->shifts->first();
            $shiftStart = $todayShift ? $todayShift->shift_start : '07:30:00';
            $status = ($currentTime > $shiftStart) ? 'terlambat' : 'hadir';

            $attendance = Attendance::where('attendable_type', Teacher::class)
                ->where('attendable_id', $teacher->id)
                ->where('date', $todayDate)
                ->first();

            if (!$attendance) {
                Attendance::create([
                    'attendable_type' => Teacher::class,
                    'attendable_id' => $teacher->id,
                    'date' => $todayDate,
                    'time_in' => $currentTime,
                    'status' => $status,
                ]);
                $msg = "Absensi Masuk berhasil ($status).";
            } else if (!$attendance->time_out) {
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

        if ($student) {
            $standardShiftStart = '07:15:00';
            $status = ($currentTime > $standardShiftStart) ? 'terlambat' : 'hadir';

            $attendance = Attendance::where('attendable_type', Student::class)
                ->where('attendable_id', $student->id)
                ->where('date', $todayDate)
                ->first();

            if (!$attendance) {
                Attendance::create([
                    'attendable_type' => Student::class,
                    'attendable_id' => $student->id,
                    'date' => $todayDate,
                    'time_in' => $currentTime,
                    'status' => $status,
                ]);
                $msg = "Absensi Masuk berhasil ($status).";
            } else if (!$attendance->time_out) {
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

    // --- REKAP ABSENSI HARIAN ---
    public function rekapIndex(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $roleFilter = $request->input('role');
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

        $totalHadir = Attendance::where('date', $date)->count();
        $totalSiswa = Attendance::where('date', $date)->where('attendable_type', Student::class)->count();
        $totalGuru = Attendance::where('date', $date)->where('attendable_type', Teacher::class)->count();

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

    // --- REKAP BULANAN / PERIODE GURU & AKUMULASI JAM MENGAJAR ---
    public function rekapBulananGuru(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $teachers = Teacher::with('shifts')->get();

        $attendances = Attendance::where('attendable_type', Teacher::class)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $rekapPerGuru = [];

        foreach ($teachers as $teacher) {
            $teacherAttendances = $attendances->where('attendable_id', $teacher->id);
            
            $totalHadir = $teacherAttendances->where('status', 'hadir')->count();
            $totalTerlambat = $teacherAttendances->where('status', 'terlambat')->count();
            $totalHadirSemua = $totalHadir + $totalTerlambat;

            $totalJamMengajar = 0;

            foreach ($teacherAttendances as $att) {
                if ($teacher->shifts) {
                    $dayEnglish = Carbon::parse($att->date)->format('l');
                    $shiftHariIni = $teacher->shifts->where('day', $dayEnglish)->first();
                    if ($shiftHariIni && isset($shiftHariIni->total_hours)) {
                        $totalJamMengajar += (int) $shiftHariIni->total_hours;
                    }
                }
            }

            $rekapPerGuru[] = [
                'teacher' => $teacher,
                'total_hadir' => $totalHadirSemua,
                'tepat_waktu' => $totalHadir,
                'terlambat' => $totalTerlambat,
                'total_jp' => $totalJamMengajar,
            ];
        }

        return view('admin.rekap-bulanan-guru', compact(
            'rekapPerGuru', 
            'startDate', 
            'endDate'
        ));
    }

    // --- EXPORT LAPORAN HARIAN KE PDF ---
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

    // --- EXPORT PDF REKAP BULANAN/PERIODE GURU ---
    public function exportPdfBulananGuru(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $teachers = Teacher::with('shifts')->get();
        $attendances = Attendance::where('attendable_type', Teacher::class)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $rekapPerGuru = [];

        foreach ($teachers as $teacher) {
            $teacherAttendances = $attendances->where('attendable_id', $teacher->id);
            
            $totalHadir = $teacherAttendances->where('status', 'hadir')->count();
            $totalTerlambat = $teacherAttendances->where('status', 'terlambat')->count();
            $totalHadirSemua = $totalHadir + $totalTerlambat;

            $totalJamMengajar = 0;

            foreach ($teacherAttendances as $att) {
                if ($teacher->shifts) {
                    $dayEnglish = Carbon::parse($att->date)->format('l');
                    $shiftHariIni = $teacher->shifts->where('day', $dayEnglish)->first();
                    if ($shiftHariIni && isset($shiftHariIni->total_hours)) {
                        $totalJamMengajar += (int) $shiftHariIni->total_hours;
                    }
                }
            }

            $rekapPerGuru[] = [
                'teacher' => $teacher,
                'total_hadir' => $totalHadirSemua,
                'tepat_waktu' => $totalHadir,
                'terlambat' => $totalTerlambat,
                'total_jp' => $totalJamMengajar,
            ];
        }

        $pdf = Pdf::loadView('admin.rekap-bulanan-guru', compact(
            'rekapPerGuru', 
            'startDate', 
            'endDate'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Akumulasi-JP-Guru-' . $startDate . '_s_d_' . $endDate . '.pdf');
    }

    // Menghapus data rekap absensi
    public function destroyRekap($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->back()->with('success', 'Data absensi berhasil dihapus.');
    }
}