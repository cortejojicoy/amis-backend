<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaveMentor extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'mas_id',
        'faculty_id',
        'uuid',
        'actions',
        'mentor_name',
        'mentor_role',
        'actions_status',
        'filed_represented',
        'effectivity_start',
        'effectivity_end'
    ];


    const SUBMITTED = 'submitted';
    const PENDING = 'Pending';
    const RETURNED = 'Returned by adviser';
    // const APPROVED = 'Approved';
    // const DISAPPROVED = 'Disapproved';

    public function faculty(){
        return $this->belongsTo(Faculty::class, 'faculty_id', 'mentor_id');
    }

    public function student() {
        return $this->hasOne(Student::class, 'uuid', 'uuid');
    }

    public function mentor() {
        return $this->hasMany(Mentor::class, 'uuid', 'uuid');
    }

    public function scopeFilter($query, $filters) {
        if($filters->has('student_add_mentor')) {
            $query->with(['student.student_user', 'mentor.mentor_role', 'mentor.faculty.uuid', 'student.program_records' => function($query) {
                $query->where('student_program_records.status', '=', 'ACTIVE');
            }]);

            if($filters->has('uuid')) {
                $query->where('uuid', $filters->uuid);
            }

            if($filters->has('mentor_name')) {
                $query->where('mentor_name', $filters->mentor_name);
            }

            // if($filters->program->acronym != '') {
            //     $query->with(['faculty'])
            // }
        }
    }
}
