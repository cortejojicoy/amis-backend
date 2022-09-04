<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prerog extends Model
{
    use HasFactory;
    
    protected $table = 'prerogs';
    protected $primaryKey = 'prg_id';
    protected $keyType = 'string'; 
    
    public $incrementing = false;

    protected $fillable = [
        'prg_id',
        'term',
        'class_id',
        'status',
        'sais_id',
        'comment',
        'submitted_to_sais',
        'created_at',
    ];

    const REQUESTED = 'Requested';
    const LOGGED_OCS = 'Logged by OCS';
    const CANCELLED = 'Cancelled';
    const APPROVED_FIC = 'Approved by FIC';
    const APPROVED_OCS = 'Approved by OCS';
    const DISAPPROVED_FIC = 'Disapproved by FIC';
    const DISAPPROVED_OCS = 'Disapproved by OCS';

    public function prerog_txns()
    {
        return $this->hasMany(PrerogTxn::class, 'prg_id', 'prg_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'sais_id', 'sais_id');
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, Student::class, 'sais_id', 'sais_id', 'sais_id', 'sais_id');
    }

    public function course_offering()
    {
        return $this->belongsTo(CourseOffering::class, 'class_id', 'class_nbr');
    }

    public function scopeFilter($query, $filters, $role) {
        if($filters->has('class_nbr')) {
            $query->where('prerogs.class_id', $filters->class_nbr);
        }

        if($filters->has('prg_term')) {
            $query->where('prerogs.term', $filters->prg_term);
        } 

        if($role == 'students') {
            if($filters->has('sais_id')) {
                $query->where('prerogs.sais_id', $filters->sais_id);
            }

            if($filters->has('with_course_offerings')) {
                $query->with(['course_offering']);
            }
        }

        if($role == 'faculties') {
            if($filters->has('sais_id')) {
                $query->where('co.id', $filters->sais_id);
            }

            if($filters->has('status')) {
                $query->whereIn('prerogs.status', $filters->status);
            }

            if($filters->has('with_students')) {
                $query->with(['user', 'student', 'student.program_records' => function ($query) {
                    $query->where('student_program_records.status', '=', 'ACTIVE');
                }, 'prerog_txns' => function ($query) use($filters) {
                    $query->where('prerog_txns.action', '=', $filters->prg_txn_status);
                }]);
            }
        }

        if($role == 'admins') {
            if($filters->admin->college != '') {
                $query->whereHas('student.program_records', function ($query) use($filters) {
                    $query->where('student_program_records.acad_group', $filters->admin->college)
                        ->where('student_program_records.status', 'ACTIVE');
                });
            }

            if($filters->has('prg_status')) {
                $query->whereIn('prerogs.status', $filters->prg_status);
            }

            if($filters->has('with_students')) {
                $query->with(['user', 'student', 'student.program_records' => function ($query) {
                    $query->where('student_program_records.status', '=', 'ACTIVE');
                }, 'course_offering', 'prerog_txns' => function($query) use ($filters) {
                    $query->where('prerog_txns.action', '=', $filters->prg_txn_status);
                }]);
            }
        }
    }
}
