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
        'uuid',
        'mentor_role',
        'field_represented',
        'status',
        'start_date',
        'end_date'
    ];

    public function mentor_role()
    {
        return $this->belongsTo(MentorRole::class, 'mentor_role', 'id');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'sais_id', 'student_sais_id');
    }

    // BELOW WAS LINK TO UUID 
    public function student_uuid()
    {
        return $this->hasOne(Student::class, 'uuid', 'uuid');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    public function mentor()
    {
        return $this->hasMany(Ma::class, 'faculty_id', 'faculty_id');
    }

    // public function mentor_assignment()
    // {
    //     return $this->belongsTo(MentorAssignment::class, 'faculty_id', 'mentor_faculty_id');
    // }

    public function scopeFilter($query, $filters) {
        if($filters->has('mentors_information')) {
            $query->with(['faculty.uuid', 'mentor_role','student_uuid.student_user', 'student_uuid.program_records' => function($query) {
                $query->where('student_program_records.status', '=', 'ACTIVE');
            }]);

            if($filters->has('is_student')) {
                if($filters->has('uuid')) {
                    $query->where('uuid', $filters->uuid)->where('status', '=', 'ACTIVE');
                }
            }
        }

        if($filters->has('admin')) {
            $query->distinct('uuid')->with(['faculty.uuid', 'mentor_role', 'student_uuid.student_user', 'student_uuid.program_records' => function($query) {
                $query->where('student_program_records.status', '=', 'ACTIVE');
            }]);
        }
        
        $query = $this->filterData($query, $filters);
    }

    public function filterData($query, $filters)
    {
        // dd($filters->mentor->faculty_id);

        if($filters->has('is_adviser')) {
            if($filters->mentor != null) {
                if($filters->mentor->faculty_id != '') {
                    $query->where('faculty_id', $filters->mentor->faculty_id);
                }
            }

            if($filters->has('active_mentor')) {
                $query->with(['faculty.uuid', 'mentor_role']);
            }

            if($filters->has('request_mentor')) {
                $query->with(['faculty.uuid', 'mentor_role']);
            }
        }

        if($filters->has('is_admin')) {
            if($filters->has('uuid')) {
                $query->where('uuid', $filters->uuid);
            }

            if($filters->has('active_mentor')) {
                $query->with(['faculty.uuid', 'mentor_role']);
            }
        }

        
    }

    public function scopeMentorRoles($query, $filters) {
        if($filters->has('mentors_information')) {
            $query->with(['mentor_role']);
        }
    }
}
