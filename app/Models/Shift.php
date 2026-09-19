<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    // TAMBAHKAN BARIS INI AGAR KOLOM BISA DI-ISI (MASS ASSIGNABLE)
    protected $guarded = ['id'];

    // Atau bisa juga menggunakan $fillable:
    // protected $fillable = ['teacher_id', 'day', 'shift_start', 'shift_end'];
}