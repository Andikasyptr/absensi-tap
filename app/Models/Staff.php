<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';
    protected $fillable = ['name', 'rfid_uid', 'position'];

    // Jika staf TU juga ingin menggunakan sistem shift harian yang sama seperti guru:
    public function shifts()
    {
        return $this->morphMany(TeacherShift::class, 'attendable'); 
        // Catatan: Anda bisa menggunakan morphMany ke tabel shifts yang sama, atau buat tabel shift khusus staff jika jam kerjanya berbeda.
    }

    public function attendances()
    {
        return $this->morphMany(Attendance::class, 'attendable');
    }
}