<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Student extends Model {
    protected $guarded = ['id'];

    public function attendances(): MorphMany {
        return $this->morphMany(Attendance::class, 'attendable');
    }
}
