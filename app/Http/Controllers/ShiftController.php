<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;

class ShiftController extends Controller
{
    // Tampilkan daftar shift
    public function index()
    {
        $shifts = Shift::all();
        return view('admin.shifts.index', compact('shifts'));
    }

    // Simpan atau update shift
    public function store(Request $request)
    {
        $request->validate([
            'day' => 'required|string',
            'start_time' => 'required',
        ]);

        Shift::updateOrCreate(
            ['day' => $request->day],
            [
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]
        );

        return redirect()->route('admin.shifts.index')->with('success', 'Jam shift berhasil disimpan!');
    }
}