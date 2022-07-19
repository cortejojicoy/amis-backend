<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $primaryKey = 'campus_id';

    public function cois()
    {
        return $this->hasMany(Coi::class, 'student_id', 'campus_id');
    }
}
