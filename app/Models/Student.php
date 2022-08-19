<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $primaryKey = 'campus_id';
    protected $keyType = 'string';

    public function program_records()
    {
        return $this->hasMany(StudentProgramRecord::class, 'campus_id', 'campus_id');
    }

    public function student_grades()
    {
        return $this->hasMany(StudentGrade::class, 'campus_id', 'campus_id');
    }
}
