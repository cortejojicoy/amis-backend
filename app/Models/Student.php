<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $primaryKey = 'campus_id';
    protected $keyType = 'string';

    public function cois()
    {
        return $this->hasMany(Coi::class, 'student_id', 'campus_id');
    }

    public function program_records()
    {
        return $this->hasMany(StudentProgramRecord::class, 'campus_id', 'campus_id');
    }
}
