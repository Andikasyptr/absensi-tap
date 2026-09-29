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

    // --- REKAP ABSENSI HARIAN (SISWA & GURU TERJADWAL) ---
    public function rekapIndex(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $roleFilter = $request->input('role', 'siswa');
        $statusFilter = $request->input('status');
        $kelasFilter = $request->input('class_name');

        $dayEnglish = Carbon::parse($date)->format('l');

        // 1. Ambil daftar kelas secara unik (tanpa duplikasi nama kelas) untuk dropdown filter
        $classList = Student::select('class_name')
            ->whereNotNull('class_name')
            ->distinct()
            ->orderBy('class_name')
            ->pluck('class_name')
            ->unique()
            ->values();

        // 2. Logika Rekap Siswa (Semua Siswa Terdaftar)
        $studentRecaps = [];
        $studentQuery = Student::query();
        if ($kelasFilter) {
            $studentQuery->where('class_name', $kelasFilter);
        }
        $allStudents = $studentQuery->orderBy('class_name')->orderBy('name')->get();

        $attendancesToday = Attendance::where('attendable_type', Student::class)
            ->where('date', $date)
            ->get()
            ->keyBy('attendable_id');

        foreach ($allStudents as $student) {
            $att = $attendancesToday->get($student->id);
            $status = 'Belum Absen';

            if ($att) {
                $status = $att->status; 
            }

            if ($statusFilter) {
                if ($statusFilter == 'belum_absen' && $status != 'Belum Absen') continue;
                if ($statusFilter != 'belum_absen' && $status != $statusFilter) continue;
            }

            $studentRecaps[] = [
                'student' => $student,
                'attendance' => $att,
                'status' => $status
            ];
        }

        // 3. Logika Rekap Guru (Hanya Guru yang memiliki Shift di hari tersebut)
        $teachersWithShift = Teacher::whereHas('shifts', function($q) use ($dayEnglish) {
            $q->where('day', $dayEnglish);
        })->with(['shifts' => function($q) use ($dayEnglish) {
            $q->where('day', $dayEnglish);
        }])->orderBy('name')->get();

        $teacherAttendancesToday = Attendance::where('attendable_type', Teacher::class)
            ->where('date', $date)
            ->get()
            ->keyBy('attendable_id');

        $teacherRecaps = [];
        $totalJamGuruAll = 0;

        foreach ($teachersWithShift as $teacher) {
            $att = $teacherAttendancesToday->get($teacher->id);
            $status = 'Belum Absen';

            if ($att) {
                $status = $att->status; 
            }

            if ($statusFilter) {
                if ($statusFilter == 'belum_absen' && $status != 'Belum Absen') continue;
                if ($statusFilter != 'belum_absen' && $status != $statusFilter) continue;
            }

            $shiftHariIni = $teacher->shifts->first();
            $jpHariIni = $shiftHariIni ? (int) $shiftHariIni->total_hours : 0;

            if ($att) {
                $totalJamGuruAll += $jpHariIni;
            }

            $teacherRecaps[] = [
                'teacher' => $teacher,
                'attendance' => $att,
                'status' => $status,
                'total_jp' => $jpHariIni
            ];
        }

        // Statistik
        $totalHadir = Attendance::where('date', $date)->count();
        $totalSiswa = Attendance::where('date', $date)->where('attendable_type', Student::class)->count();
        $totalSiswaTerdaftar = Student::count();
        $totalGuruHadir = Attendance::where('date', $date)->where('attendable_type', Teacher::class)->count();
        $totalGuruJadwalHariIni = count($teachersWithShift);

        return view('admin.rekap', compact(
            'studentRecaps',
            'teacherRecaps',
            'classList',
            'totalHadir', 
            'totalSiswa', 
            'totalSiswaTerdaftar',
            'totalGuruHadir',
            'totalGuruJadwalHariIni',
            'date', 
            'roleFilter', 
            'statusFilter',
            'kelasFilter',
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

    // --- EXPORT LAPORAN HARIAN KE PDF (MENDUKUNG SEMUA SISWA & GURU TERJADWAL) ---
    public function exportPdf(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $roleFilter = $request->input('role', 'Siswa');
        $statusFilter = $request->input('status');
        $kelasFilter = $request->input('class_name');

        $dayEnglish = Carbon::parse($date)->format('l');
        $studentRecaps = [];
        $teacherRecaps = [];
        $totalJamGuruAll = 0;

        if ($roleFilter == 'Siswa') {
            $studentQuery = Student::query();
            if ($kelasFilter) {
                $studentQuery->where('class_name', $kelasFilter);
            }
            $allStudents = $studentQuery->orderBy('class_name')->orderBy('name')->get();

            $attendancesToday = Attendance::where('attendable_type', Student::class)
                ->where('date', $date)
                ->get()
                ->keyBy('attendable_id');

            foreach ($allStudents as $student) {
                $att = $attendancesToday->get($student->id);
                $status = 'Belum Absen';

                if ($att) {
                    $status = $att->status; 
                }

                if ($statusFilter) {
                    if ($statusFilter == 'belum_absen' && $status != 'Belum Absen') continue;
                    if ($statusFilter != 'belum_absen' && $status != $statusFilter) continue;
                }

                $studentRecaps[] = [
                    'student' => $student,
                    'attendance' => $att,
                    'status' => $status
                ];
            }
        } elseif ($roleFilter == 'Guru') {
            $teachersWithShift = Teacher::whereHas('shifts', function($q) use ($dayEnglish) {
                $q->where('day', $dayEnglish);
            })->with(['shifts' => function($q) use ($dayEnglish) {
                $q->where('day', $dayEnglish);
            }])->orderBy('name')->get();

            $teacherAttendancesToday = Attendance::where('attendable_type', Teacher::class)
                ->where('date', $date)
                ->get()
                ->keyBy('attendable_id');

            foreach ($teachersWithShift as $teacher) {
                $att = $teacherAttendancesToday->get($teacher->id);
                $status = 'Belum Absen';

                if ($att) {
                    $status = $att->status; 
                }

                if ($statusFilter) {
                    if ($statusFilter == 'belum_absen' && $status != 'Belum Absen') continue;
                    if ($statusFilter != 'belum_absen' && $status != $statusFilter) continue;
                }

                $shiftHariIni = $teacher->shifts->first();
                $jpHariIni = $shiftHariIni ? (int) $shiftHariIni->total_hours : 0;

                if ($att) {
                    $totalJamGuruAll += $jpHariIni;
                }

                $teacherRecaps[] = [
                    'teacher' => $teacher,
                    'attendance' => $att,
                    'status' => $status,
                    'total_jp' => $jpHariIni
                ];
            }
        }

        $pdf = Pdf::loadView('admin.rekap-pdf', compact(
            'studentRecaps',
            'teacherRecaps',
            'date', 
            'roleFilter', 
            'statusFilter',
            'kelasFilter',
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

        $pdf = Pdf::loadView('admin.rekap-bulanan-guru-pdf', compact(
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