<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $primaryKey = 'faculty_id';
    
    protected $fillable = [
        'uuid',
        'sais_id'
    ];

    public function courseOfferings() 
    {
        return $this->hasMany(CourseOffering::class, 'id', 'sais_id');
    }

    public function user() 
    {
        return $this->belongsTo(User::class,'sais_id','sais_id');
    }

    // BELOW WAS LINK TO UUID

    public function appointment() 
    {
        return $this->belongsTo(FacultyAppointment::class, 'faculty_id', 'faculty_id');
    }

    public function uuid() 
    {
        return $this->belongsTo(User::class, 'uuid', 'uuid');
    }

    public function mentor() 
    {
        return $this->hasMany(Mentor::class, 'faculty_id', 'faculty_id');
    }

    public function scopeFilter($query, $filters) {
        if($filters->has('advisees')) {
            $query->with(['user', 'mentor.faculty', 'mentor.mentor_role','mentor.student.student_user', 'mentor.student.program_records' => function($query) {
                $query->where('student_program_records.status', '=', 'ACTIVE');
            }]);
        }

        if($filters->has('faculty_list')) {
            $query->with(['uuid', 'appointment']);
        }

        //select fields
        if($filters->has('fields')) {
            $query->select($filters->fields);
        }

        if($filters->has('uuid')) {
            $query->where('uuid', $filters->uuid);
        }
        
        $query = $this->filterData($query, $filters);
    }

    public function filterData($query, $filters) {
        if($filters->has('course_code')) {
            if($filters->course_code != '--') {
                $query->where('course_code', $filters->course_code);
            }
        }

        return $query;
    }
}
