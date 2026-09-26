<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    // Tampilkan halaman form CMS Pengaturan Sekolah
    public function index()
    {
        return view('admin.settings');
    }

    // Proses Simpan Perubahan Pengaturan
    public function update(Request $request)
    {
        $data = $request->except('_token', '_method');

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan kop surat & profil sekolah berhasil diperbarui!');
    }
}