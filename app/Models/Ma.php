<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Ma;
use App\Models\User;
use App\Models\Mentor;
use App\Models\Student;

class Ma extends Model
{
    use HasFactory;
    protected $table = 'mas';
    protected $primaryKey = 'mas_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'mas_id',
        'student_sais_id',
        'mentor_id',
        'status',
        'actions',
        'mentor_name',
        'mentor_role',
        'created_at',
    ];

    const ENDORSED = 'Endorsed';
    const PENDING = 'Pending';
    const REJECTED = 'Rejected';
    const APPROVED = 'Approved';
    const DISAPPROVED = 'Disapproved';

    public function user() {
        return $this->hasMany(User::class, 'sais_id', 'student_sais_id');
    }

    public function student() {
        return $this->hasMany(Student::class, 'sais_id', 'student_sais_id');
    }

    public function mas() {
        return $this->belongsTo(Ma::class, 'mas_id', 'mas_id');
    }

    public function faculty() {
        return $this->hasMany(Faculty::class, 'sais_id', 'mentor_id');
    }

    public function scopeFilter($query, $filters, $roles) {
        $query->with(['faculty.mentor' , 'user', 'student', 'student.program_records' => function($query) {
            $query->where('student_program_records.status', '=', 'ACTIVE');
        }]);

        if($roles == 'admins') {
            if($filters->admin == 'unit') {
                $query->whereHas('student.program_records', function($query) use($filters) {
                    $query->where('student_program_records.acad_org', $filters->tags->unit)->where('status', '=', 'Pending');
                });
            } else if ($filters->admin == 'college') {
                $query->whereHas('student.program_records', function($query) use($filters) {
                    $query->where('student_program_records.acad_group', $filters->tags->unit)->where('status', '=', 'Endorsed');
                });
            }

        } else if($roles == 'faculties') {
            if($filters->faculty == 'adviser') {
                $query->where('mentor_id', $filters->sais_id)->where('status', '=', 'Approved');
                // $query->where('mentor_id', $filters->sais_id)->where('status', '=', 'Pending')->orWhere('status', '=', 'Approved');
            }
        }
    }

    public function scopeMaRequest($query, $filters, $roles)
    {   
        if($roles == 'faculties') {
            if($filters->facultyType == 'adviser' || $filters->facultyType == 'nominated') {
                $query->where('status', 'Pending')->where('student_sais_id', $filters->studentId);
            }
        }

        if($roles == 'admins') {
            if($filters->adminType == 'unit') {
                $query->where('status', 'Pending')->where('student_sais_id', $filters->studentId);
            }
            
            if($filters->adminType == 'college') {
                $query->where('status', 'Endorsed')->where('student_sais_id', $filters->studentId);
            }
        }
    }
}
