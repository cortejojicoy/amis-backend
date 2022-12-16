<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Mentor extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'mentor_id';
    protected $table = 'mentors';

    protected $fillable = [
        'faculty_id',
        'student_program_record_id',
        'student_sais_id',
        'mentor_role',
        'field_represented',
        'status',
        'start_date',
        'end_date'
    ];

    public function faculty() {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    public function mentor_role() {
        return $this->belongsTo(MentorRole::class, 'mentor_role', 'id');
    }

    public function mas() {
        return $this->belongsTo(Ma::class, 'student_sais_id', 'student_sais_id');
    }

    public function student() {
        return $this->hasOne(Student::class, 'sais_id', 'student_sais_id');
    }

    public function scopeFilter($query, $filters) {
        if($filters->has('mentors_information')) {
            $query->with(['faculty.uuid', 'mentor_role','student.student_user', 'student.program_records' => function($query) {
                $query->where('student_program_records.status', '=', 'ACTIVE');
            }]);
        }
    }
}
