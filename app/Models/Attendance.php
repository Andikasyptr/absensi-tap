<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'attendable_type', 'attendable_id', 'date', 'time_in', 'time_out', 'status'
    ];

    // Relasi Polymorphic
    public function attendable()
    {
        return $this->morphTo();
    }
}