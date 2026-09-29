<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolClass;

class ClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::orderBy('name')->get();
        return view('admin.classes.index', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:school_classes,name',
            'level' => 'nullable|string|max:50',
        ]);

    SchoolClass::create([
            'name' => $request->name,
            'level' => $request->level,
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $class = SchoolClass::findOrFail($id);
        $class->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Data kelas berhasil dihapus.');
    }
}