<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Shift; // Pastikan Model Shift di-import

class DataController extends Controller
{
    // Tampilkan daftar siswa & guru serta form tambah
    public function index()
    {
        $students = Student::all();
        // PERBAIKAN: Menggunakan with('shifts') agar data jam masuk harian guru ikut terpanggil
        $teachers = Teacher::with('shifts')->get(); 
        
        return view('admin.data-index', compact('students', 'teachers'));
    }

    // Simpan data siswa baru
    public function storeStudent(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:students,nis',
            'name' => 'required|string',
            'rfid_uid' => 'required|unique:students,rfid_uid', // Kode dari Barcode/RFID/Kartu
            'class_name' => 'required|string',
        ]);

        Student::create($request->all());

        return redirect()->route('admin.data.index')->with('success', 'Siswa berhasil ditambahkan!');
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

    // TAMBAHAN BARU: Simpan shift jam masuk harian berbeda untuk guru
    public function storeShift(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'day' => 'required|string',
            'shift_start' => 'required',
        ]);

        // Cek apakah guru sudah punya jadwal di hari yang sama, jika ada update, jika belum buat baru
        Shift::updateOrCreate(
            [
                'teacher_id' => $request->teacher_id,
                'day' => $request->day,
            ],
            [
                'shift_start' => $request->shift_start,
                'shift_end' => $request->shift_end ?? null,
            ]
        );

        return redirect()->route('admin.data.index')->with('success', 'Shift harian guru berhasil diatur!');
    }

    // Hapus data siswa
    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('admin.data.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    // Hapus data guru (otomatis menghapus relasi shift jika onDelete cascade)
    public function destroyTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->shifts()->delete(); // Hapus shift terkait terlebih dahulu
        $teacher->delete();

        return redirect()->route('admin.data.index')->with('success', 'Data guru berhasil dihapus.');
    }
}