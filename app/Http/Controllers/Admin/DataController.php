<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Shift; 
use App\Models\SchoolClass;

class DataController extends Controller
{
    // Tampilkan daftar siswa, guru, staf TU, security, serta daftar kelas untuk dropdown
    public function index()
    {
        $students = Student::all();
        $teachers = Teacher::with('shifts')->get(); 
        
        // Mengambil data Staf TU (selain Security/Satpam)
        $staffList = Staff::where(function ($query) {
            $query->where('position', 'not like', '%Security%')
                  ->where('position', 'not like', '%Satpam%')
                  ->orWhereNull('position');
        })->orderBy('name')->get();

        // Mengambil data Security / Satpam
        $securityList = Staff::where('position', 'like', '%Security%')
            ->orWhere('position', 'like', '%Satpam%')
            ->orderBy('name')->get();

        $schoolClasses = SchoolClass::orderBy('name')->get(); 
        
        return view('admin.data-index', compact('students', 'teachers', 'staffList', 'securityList', 'schoolClasses'));
    }
        // Perbarui data siswa (EDIT)
    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'nis' => 'required|unique:students,nis,' . $student->id,
            'name' => 'required|string',
            'rfid_uid' => 'required|unique:students,rfid_uid,' . $student->id,
            'class_name' => 'required|string',
        ]);

        $student->update($request->all());

        return redirect()->route('admin.data.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    // Hapus data siswa
    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('admin.data.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    // Simpan data guru baru
    public function storeTeacher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rfid_uid' => 'required|string|unique:teachers,rfid_uid',
        ]);

        Teacher::create([
            'name' => $request->name,
            'rfid_uid' => $request->rfid_uid,
        ]);

        return redirect()->route('admin.data.index')->with('success', 'Data guru berhasil disimpan.');
    }

    // Perbarui data guru (EDIT)
    public function updateTeacher(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'rfid_uid' => 'required|string|unique:teachers,rfid_uid,' . $teacher->id,
        ]);

        $teacher->update([
            'name' => $request->name,
            'rfid_uid' => $request->rfid_uid,
        ]);

        return redirect()->route('admin.data.index')->with('success', 'Data guru berhasil diperbarui!');
    }

    // Hapus data guru
    public function destroyTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->shifts()->delete(); 
        $teacher->delete();

        return redirect()->route('admin.data.index')->with('success', 'Data guru berhasil dihapus.');
    }

    // --- MANAJEMEN STAF TU & TENAGA KEPENDIDIKAN ---

    // Simpan data Staf TU baru
    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'rfid_uid' => 'required|string|unique:staff,rfid_uid',
        ]);

        Staff::create([
            'name' => $request->name,
            'position' => $request->position ?? 'Staf Tata Usaha',
            'rfid_uid' => $request->rfid_uid,
        ]);

        return redirect()->route('admin.data.index')->with('success', 'Data Staf TU berhasil ditambahkan!');
    }

    // Perbarui data Staf TU (EDIT)
    public function updateStaff(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'rfid_uid' => 'required|string|unique:staff,rfid_uid,' . $staff->id,
        ]);

        $staff->update([
            'name' => $request->name,
            'position' => $request->position,
            'rfid_uid' => $request->rfid_uid,
        ]);

        return redirect()->route('admin.data.index')->with('success', 'Data Staf TU berhasil diperbarui!');
    }

    // Hapus data Staf TU
    public function destroyStaff($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();

        return redirect()->route('admin.data.index')->with('success', 'Data Staf TU berhasil dihapus.');
    }
    // ================= MANAJEMEN SECURITY / SATPAM =================

    public function storeSecurity(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'rfid_uid' => 'required|unique:staff,rfid_uid',
            'position' => 'nullable|string',
        ]);

        // Pastikan posisi memiliki label 'Security' agar terfilter otomatis di tab Security
        $position = $request->position;
        if (empty($position)) {
            $position = 'Security / Satpam';
        } elseif (!str_contains(strtolower($position), 'security') && !str_contains(strtolower($position), 'satpam')) {
            $position = 'Security - ' . $position;
        }

        Staff::create([
            'name'     => $request->name,
            'position' => $position,
            'rfid_uid' => $request->rfid_uid,
        ]);

        return redirect()->route('admin.data.index')->with('success', 'Petugas Security berhasil ditambahkan!');
    }

    public function updateSecurity(Request $request, $id)
    {
        $security = Staff::findOrFail($id);

        $request->validate([
            'name'     => 'required|string',
            'rfid_uid' => 'required|unique:staff,rfid_uid,' . $id,
            'position' => 'nullable|string',
        ]);

        $position = $request->position;
        if (empty($position)) {
            $position = 'Security / Satpam';
        } elseif (!str_contains(strtolower($position), 'security') && !str_contains(strtolower($position), 'satpam')) {
            $position = 'Security - ' . $position;
        }

        $security->update([
            'name'     => $request->name,
            'position' => $position,
            'rfid_uid' => $request->rfid_uid,
        ]);

        return redirect()->route('admin.data.index')->with('success', 'Data petugas security berhasil diperbarui!');
    }

    public function destroySecurity($id)
    {
        Staff::findOrFail($id)->delete();

        return redirect()->route('admin.data.index')->with('success', 'Data petugas security berhasil dihapus!');
    }
    // --- MANAJEMEN SHIFT GURU ---

    // Simpan shift jam masuk harian & jumlah jam mengajar untuk guru
    public function storeShift(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'day' => 'required|string',
            'shift_start' => 'required',
            'total_hours' => 'nullable|integer|min:1|max:12', 
        ]);

        Shift::updateOrCreate(
            [
                'teacher_id' => $request->teacher_id,
                'day' => $request->day,
            ],
            [
                'shift_start' => $request->shift_start,
                'shift_end' => $request->shift_end ?? null,
                'total_hours' => $request->total_hours ?? null, 
            ]
        );

        return redirect()->route('admin.data.index')->with('success', 'Shift harian dan jumlah jam guru berhasil diatur!');
    }

    // Perbarui shift satuan (EDIT)
    public function updateShift(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $request->validate([
            'shift_start' => 'required',
            'shift_end' => 'nullable',
            'total_hours' => 'nullable|integer|min:1|max:12',
        ]);

        $shift->update([
            'shift_start' => $request->shift_start,
            'shift_end' => $request->shift_end,
            'total_hours' => $request->total_hours,
        ]);

        return redirect()->route('admin.data.index')->with('success', 'Jadwal shift berhasil diperbarui!');
    }

    // Hapus shift satuan
    public function destroyShift($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return redirect()->route('admin.data.index')->with('success', 'Jadwal shift berhasil dihapus!');
    }
}