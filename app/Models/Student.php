<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $primaryKey = 'campus_id';
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'sais_id',
        'campus_id',
    ];

    public function program_records()
    {
        return $this->hasMany(StudentProgramRecord::class, 'campus_id', 'campus_id');
    }

    public function student_grades()
    {
        return $this->hasMany(StudentGrade::class, 'campus_id', 'campus_id');
    }

    public function pcw()
    {
        return $this->hasOne(Pcw::class, 'sais_id', 'sais_id');
    }

    public function scopeProgramId($query) {
        $query->join('student_program_records', 'student_program_records.campus_id' ,'=', 'students.campus_id');
    }

    public function scopeFilter($query, $filters) {
        if($filters->has('curriculum_data')) {
            $query->with(['program_records' => function ($query) use($filters) {
                $query->where('student_program_records.status', '=', $filters->program_record_status);
            }, 'program_records.curriculum', 'program_records.curriculum.curriculum_structures', 'program_records.curriculum.curriculum_courses', 'program_records.curriculum.curriculum_courses.course']);
        }
    }
}
