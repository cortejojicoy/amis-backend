<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $primaryKey = 'campus_id';
    protected $keyType = 'string';

    public function program_records() {
        return $this->hasMany(StudentProgramRecord::class, 'campus_id', 'campus_id');
    }

    public function student_grades() {
        return $this->hasMany(StudentGrade::class, 'campus_id', 'campus_id');
    }

    public function student_user() {
        return $this->belongsTo(User::class, 'uuid', 'uuid');
    }

    public function scopeProgramId($query) {
        $query->join('student_program_records', 'student_program_records.campus_id' ,'=', 'students.campus_id');
    }
}
