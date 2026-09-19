<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Perbaikan: Gunakan HasMany yang benar dari Eloquent
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class, 'teacher_id');
    }
}