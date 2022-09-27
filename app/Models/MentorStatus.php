<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorStatus extends Model
{
    use HasFactory;

    protected $table = 'mentor_status';
    public $timestamps = false;
    protected $fillable = [
        'student_sais_id',
        'mentor_id',
        'status',
        'actions',
        'mentor_name',
        'mentor_role'
    ];

    const ENDORSED = 'Endorsed';
    const PENDING = 'Pending';
    const REJECTED = 'Rejected';
    const APPROVED = 'Approved';
    const DISAPPROVED = 'Disapproved';

    public function mentorss()
    {
        return $this->belongsTo(MaStudent::class, 'sais_id', 'student_sais_id');
    }

    public function scopeFilter($query, $filters, $roles)
    {   
        // dd($roles);
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
