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
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'uuid',
        'faculty_id',
        'status',
        'actions',
        'mentor_name',
        'mentor_role',
        'created_at',
    ];

    const ENDORSED = 'Endorsed';
    const PENDING = 'Pending';
    const APPROVED = 'Approved';
    const DISAPPROVED = 'Disapproved';
    const RETURNED = 'Returned';

    public function user() 
    {
        return $this->hasMany(User::class, 'sais_id', 'student_sais_id');
    }

    public function student() 
    {
        return $this->hasOne(Student::class, 'sais_id', 'student_sais_id');
    }

    public function mentor_role() 
    {
        return $this->belongsTo(MentorRole::class, 'mentor_role', 'id');
    }

    public function mentor() 
    {
        return $this->hasMany(Mentor::class, 'uuid', 'uuid');
    }

    // BELOW WAS LINK TO UUID
    public function student_uuid() 
    {
        return $this->belongsTo(Student::class, 'uuid', 'uuid');
    }

    public function faculty() 
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    public function scopeFilter($query, $filters, $tagProcessor) {
        $query->with(['mentor_role', 'faculty.uuid']);

        // requested mentor; admin view
        if($filters->has('admins')) {
            $query->where('mas.status', '=', 'Endorsed');

            if($filters->has('uuid')) {
                $query->where('uuid', $filters->uuid);
            }
        }
        // requested mentor; nominees view
        if($filters->has('nominees')) {
            $query->where('mas.status', '=', 'Pending');

            if($filters->has('uuid')) {
                $query->where('uuid', $filters->uuid);
            }
        }

        // requested mentor; advisee view
        if($filters->has('advisee')) {
            $query->where('mas.status', '=', 'Pending');

            if($filters->has('uuid')) {
                $query->where('uuid', $filters->uuid);
            }
        }
        
        $query = $this->filterData($query, $filters);

    }

    public function filterData($query, $filters) {
        //get the active mentor of students 
        if($filters->has('active_mentor')) { 
            $query->with(['mentor', 'mentor.student_uuid.program_records', 'mentor.faculty.uuid', 'mentor.mentor_role']);

            //students uuid; display active mentor
            if($filters->has('uuid')) { 
                $query->where('uuid', $filters->uuid);
            }
        } 

        return $query;
    }
}
