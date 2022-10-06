<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function scopeFilter($query, $filters, $roles)
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
