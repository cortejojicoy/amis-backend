<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacultyAppointment extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'faculty_appointments';

    public function student_program() {
        return $this->belongsTo(Program::class, 'homeunit', 'unit');
    }

    public function faculty() {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    public function scopeFilter($query, $filters) {
        if($filters->has('mentors_information')) {
            $query->with(['faculty.user']);

            if($filters->program->academic_program_id != '') {
                $query->where('homeunit', $filters->program->academic_program_id);
            }
        }
    }
}
