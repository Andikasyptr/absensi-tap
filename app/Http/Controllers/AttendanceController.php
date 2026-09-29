<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\SchoolClass;
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
        $staff = Staff::where('rfid_uid', $uid)->first();

        if (!$student && !$teacher && !$staff) {
            return response()->json(['message' => 'maaf, anda belum terdaftar, silahkan hubungi operator absensi'], 404);
        }

        // --- GURU ---
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
                return response()->json([
                    'message' => 'Anda sudah melakukan absensi masuk dan pulang hari ini.'
                ], 422);
            }

            return response()->json([
                'name' => $teacher->name,
                'role' => 'Guru',
                'message' => $msg,
                'time' => date('H:i')
            ]);
        }

        // --- STAF TU / TENAGA KEPENDIDIKAN ---
        if ($staff) {
            $standardShiftStart = '07:30:00';
            $status = ($currentTime > $standardShiftStart) ? 'terlambat' : 'hadir';

            $attendance = Attendance::where('attendable_type', Staff::class)
                ->where('attendable_id', $staff->id)
                ->where('date', $todayDate)
                ->first();

            if (!$attendance) {
                Attendance::create([
                    'attendable_type' => Staff::class,
                    'attendable_id' => $staff->id,
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
                return response()->json([
                    'message' => 'Staf TU sudah melakukan absensi masuk dan pulang hari ini.'
                ], 422);
            }

            return response()->json([
                'name' => $staff->name,
                'role' => 'Staf TU / Tenaga Kependidikan',
                'message' => $msg,
                'time' => date('H:i')
            ]);
        }

        // --- SISWA ---
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
                return response()->json([
                    'message' => 'Siswa sudah melakukan absensi masuk dan pulang hari ini.'
                ], 422);
            }

            return response()->json([
                'name' => $student->name,
                'role' => 'Siswa - ' . $student->class_name,
                'message' => $msg,
                'time' => date('H:i')
            ]);
        }
    }

    // --- REKAP ABSENSI HARIAN (SISWA, GURU, & STAF TU) ---
    public function rekapIndex(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $roleFilter = $request->input('role', 'siswa');
        $statusFilter = $request->input('status');
        $kelasFilter = $request->input('class_name');

        $dayEnglish = Carbon::parse($date)->format('l');

        // 1. Ambil daftar kelas secara unik dan diurutkan
        $classList = Student::whereNotNull('class_name')
            ->orderBy('class_name')
            ->pluck('class_name')
            ->unique()
            ->values();

        // 2. Logika Rekap Siswa
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

        // 3. Logika Rekap Guru & Staf TU
        $teachersWithShift = Teacher::whereHas('shifts', function($q) use ($dayEnglish) {
            $q->where('day', $dayEnglish);
        })->with(['shifts' => function($q) use ($dayEnglish) {
            $q->where('day', $dayEnglish);
        }])->orderBy('name')->get();

        $allStaff = Staff::orderBy('name')->get();

        $teacherAttendancesToday = Attendance::where('attendable_type', Teacher::class)
            ->where('date', $date)
            ->get()
            ->keyBy('attendable_id');

        $staffAttendancesToday = Attendance::where('attendable_type', Staff::class)
            ->where('date', $date)
            ->get()
            ->keyBy('attendable_id');

        $teacherRecaps = [];
        $totalJamGuruAll = 0;

        // Masukkan Guru Terjadwal
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
                'person' => $teacher,
                'role_type' => 'Guru',
                'attendance' => $att,
                'status' => $status,
                'total_jp' => $jpHariIni
            ];
        }

        // Masukkan Staf TU (JP otomatis 0)
        foreach ($allStaff as $staff) {
            $att = $staffAttendancesToday->get($staff->id);
            $status = 'Belum Absen';

            if ($att) {
                $status = $att->status; 
            }

            if ($statusFilter) {
                if ($statusFilter == 'belum_absen' && $status != 'Belum Absen') continue;
                if ($statusFilter != 'belum_absen' && $status != $statusFilter) continue;
            }

            $teacherRecaps[] = [
                'person' => $staff,
                'role_type' => 'Staf TU',
                'attendance' => $att,
                'status' => $status,
                'total_jp' => 0
            ];
        }

        // Statistik
        $totalHadir = Attendance::where('date', $date)->count();
        $totalSiswa = Attendance::where('date', $date)->where('attendable_type', Student::class)->count();
        $totalSiswaTerdaftar = Student::count();
        $totalGuruHadir = Attendance::where('date', $date)->whereIn('attendable_type', [Teacher::class, Staff::class])->count();
        $totalGuruJadwalHariIni = count($teachersWithShift) + count($allStaff);

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

    // --- REKAP BULANAN / PERIODE GURU & STAF TU ---
    public function rekapBulananGuru(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $teachers = Teacher::with('shifts')->get();
        $staffList = Staff::all();

        $teacherAttendances = Attendance::where('attendable_type', Teacher::class)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $staffAttendances = Attendance::where('attendable_type', Staff::class)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $rekapPerGuru = [];

        // Rekap Guru
        foreach ($teachers as $teacher) {
            $tAttendances = $teacherAttendances->where('attendable_id', $teacher->id);
            
            $totalHadir = $tAttendances->where('status', 'hadir')->count();
            $totalTerlambat = $tAttendances->where('status', 'terlambat')->count();
            $totalHadirSemua = $totalHadir + $totalTerlambat;

            $totalJamMengajar = 0;

            foreach ($tAttendances as $att) {
                if ($teacher->shifts) {
                    $dayEnglish = Carbon::parse($att->date)->format('l');
                    $shiftHariIni = $teacher->shifts->where('day', $dayEnglish)->first();
                    if ($shiftHariIni && isset($shiftHariIni->total_hours)) {
                        $totalJamMengajar += (int) $shiftHariIni->total_hours;
                    }
                }
            }

            $rekapPerGuru[] = [
                'person' => $teacher,
                'role_type' => 'Guru',
                'total_hadir' => $totalHadirSemua,
                'tepat_waktu' => $totalHadir,
                'terlambat' => $totalTerlambat,
                'total_jp' => $totalJamMengajar,
            ];
        }

        // Rekap Staf TU (JP = 0)
        foreach ($staffList as $staff) {
            $sAttendances = $staffAttendances->where('attendable_id', $staff->id);
            
            $totalHadir = $sAttendances->where('status', 'hadir')->count();
            $totalTerlambat = $sAttendances->where('status', 'terlambat')->count();
            $totalHadirSemua = $totalHadir + $totalTerlambat;

            $rekapPerGuru[] = [
                'person' => $staff,
                'role_type' => 'Staf TU',
                'total_hadir' => $totalHadirSemua,
                'tepat_waktu' => $totalHadir,
                'terlambat' => $totalTerlambat,
                'total_jp' => 0,
            ];
        }

        return view('admin.rekap-bulanan-guru', compact(
            'rekapPerGuru', 
            'startDate', 
            'endDate'
        ));
    }

    // --- REKAP BULANAN / PERIODE SISWA ---
    public function rekapBulananSiswa(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $kelasFilter = $request->input('class_name');

        $studentQuery = Student::query();
        if ($kelasFilter) {
            $studentQuery->where('class_name', $kelasFilter);
        }
        $students = $studentQuery->orderBy('class_name')->orderBy('name')->get();

        $attendances = Attendance::where('attendable_type', Student::class)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $rekapPerSiswa = [];

        foreach ($students as $student) {
            $studentAttendances = $attendances->where('attendable_id', $student->id);
            
            $totalHadir = $studentAttendances->where('status', 'hadir')->count();
            $totalTerlambat = $studentAttendances->where('status', 'terlambat')->count();
            $totalMasuk = $totalHadir + $totalTerlambat;

            $rekapPerSiswa[] = [
                'student' => $student,
                'tepat_waktu' => $totalHadir,
                'terlambat' => $totalTerlambat,
                'total_hadir' => $totalMasuk,
            ];
        }

        $schoolClasses = SchoolClass::orderBy('name')->get();

        return view('admin.rekap-bulanan-siswa', compact(
            'rekapPerSiswa', 
            'startDate', 
            'endDate',
            'kelasFilter',
            'schoolClasses'
        ));
    }

    // --- EXPORT LAPORAN HARIAN KE PDF ---
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

            $allStaff = Staff::orderBy('name')->get();

            $teacherAttendancesToday = Attendance::where('attendable_type', Teacher::class)
                ->where('date', $date)
                ->get()
                ->keyBy('attendable_id');

            $staffAttendancesToday = Attendance::where('attendable_type', Staff::class)
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
                    'person' => $teacher,
                    'role_type' => 'Guru',
                    'attendance' => $att,
                    'status' => $status,
                    'total_jp' => $jpHariIni
                ];
            }

            foreach ($allStaff as $staff) {
                $att = $staffAttendancesToday->get($staff->id);
                $status = 'Belum Absen';

                if ($att) {
                    $status = $att->status; 
                }

                if ($statusFilter) {
                    if ($statusFilter == 'belum_absen' && $status != 'Belum Absen') continue;
                    if ($statusFilter != 'belum_absen' && $status != $statusFilter) continue;
                }

                $teacherRecaps[] = [
                    'person' => $staff,
                    'role_type' => 'Staf TU',
                    'attendance' => $att,
                    'status' => $status,
                    'total_jp' => 0
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

    // --- EXPORT PDF REKAP BULANAN GURU & STAF TU ---
    public function exportPdfBulananGuru(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $teachers = Teacher::with('shifts')->get();
        $staffList = Staff::all();

        $teacherAttendances = Attendance::where('attendable_type', Teacher::class)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $staffAttendances = Attendance::where('attendable_type', Staff::class)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $rekapPerGuru = [];

        foreach ($teachers as $teacher) {
            $tAttendances = $teacherAttendances->where('attendable_id', $teacher->id);
            
            $totalHadir = $tAttendances->where('status', 'hadir')->count();
            $totalTerlambat = $tAttendances->where('status', 'terlambat')->count();
            $totalHadirSemua = $totalHadir + $totalTerlambat;

            $totalJamMengajar = 0;

            foreach ($tAttendances as $att) {
                if ($teacher->shifts) {
                    $dayEnglish = Carbon::parse($att->date)->format('l');
                    $shiftHariIni = $teacher->shifts->where('day', $dayEnglish)->first();
                    if ($shiftHariIni && isset($shiftHariIni->total_hours)) {
                        $totalJamMengajar += (int) $shiftHariIni->total_hours;
                    }
                }
            }

            $rekapPerGuru[] = [
                'person' => $teacher,
                'role_type' => 'Guru',
                'total_hadir' => $totalHadirSemua,
                'tepat_waktu' => $totalHadir,
                'terlambat' => $totalTerlambat,
                'total_jp' => $totalJamMengajar,
            ];
        }

        foreach ($staffList as $staff) {
            $sAttendances = $staffAttendances->where('attendable_id', $staff->id);
            
            $totalHadir = $sAttendances->where('status', 'hadir')->count();
            $totalTerlambat = $sAttendances->where('status', 'terlambat')->count();
            $totalHadirSemua = $totalHadir + $totalTerlambat;

            $rekapPerGuru[] = [
                'person' => $staff,
                'role_type' => 'Staf TU',
                'total_hadir' => $totalHadirSemua,
                'tepat_waktu' => $totalHadir,
                'terlambat' => $totalTerlambat,
                'total_jp' => 0,
            ];
        }

        $pdf = Pdf::loadView('admin.rekap-bulanan-guru-pdf', compact(
            'rekapPerGuru', 
            'startDate', 
            'endDate'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Akumulasi-JP-Guru-dan-TU-' . $startDate . '_s_d_' . $endDate . '.pdf');
    }

    // --- EXPORT PDF REKAP BULANAN SISWA ---
    public function exportPdfBulananSiswa(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $kelasFilter = $request->input('class_name');

        $studentQuery = Student::query();
        if ($kelasFilter) {
            $studentQuery->where('class_name', $kelasFilter);
        }
        $students = $studentQuery->orderBy('class_name')->orderBy('name')->get();

        $attendances = Attendance::where('attendable_type', Student::class)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $rekapPerSiswa = [];

        foreach ($students as $student) {
            $studentAttendances = $attendances->where('attendable_id', $student->id);
            
            $totalHadir = $studentAttendances->where('status', 'hadir')->count();
            $totalTerlambat = $studentAttendances->where('status', 'terlambat')->count();
            $totalMasuk = $totalHadir + $totalTerlambat;

            $rekapPerSiswa[] = [
                'student' => $student,
                'tepat_waktu' => $totalHadir,
                'terlambat' => $totalTerlambat,
                'total_hadir' => $totalMasuk,
            ];
        }

        $pdf = Pdf::loadView('admin.rekap-bulanan-siswa-pdf', compact(
            'rekapPerSiswa', 
            'startDate', 
            'endDate',
            'kelasFilter'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Rekap-Bulanan-Siswa-' . $startDate . '_s_d_' . $endDate . '.pdf');
    }

    // Menghapus data rekap absensi
    public function destroyRekap($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->back()->with('success', 'Data absensi berhasil dihapus.');
    }
}