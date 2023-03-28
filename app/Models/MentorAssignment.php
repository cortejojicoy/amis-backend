<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorAssignment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'mentor_assignments';

    protected $fillable = [
        'uuid',
        'mentor_faculty_id',
        'mas_id',
        'faculty_id',
        'acad_group',
        'acadg_org',
        'name',
        'program',
        'student_status',
        'mentor',
        'role',
        'status',
    ];

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'mentor_faculty_id', 'faculty_id');
    }

    public function student_uuid()
    {
        return $this->belongsTo(Student::class, 'uuid', 'uuid');
    }

    public function scopeFilter($query, $filters, $tagProcessor)
    {
        // $query->with(['mentor']);

        if($filters->has('advisee')) {
            if($filters->mentor != null) {
                if($filters->mentor->faculty_id != '') {
                    $query->distinct('name')->where('mentor_faculty_id', $filters->mentor->faculty_id);
                }
            }
        }

        if($filters->has('nominees')) {
            if($filters->mentor != null) {
                if($filters->mentor->faculty_id != '') {
                    $query->distinct('name')->where('faculty_id', $filters->mentor->faculty_id);
                }
            }
        }

        if($filters->has('admins')) {
            $query->distinct('name')->whereHas('student_uuid.program_records', function($query) use($tagProcessor, $filters) {
                $query = $tagProcessor->process($query, $filters);
            });
        }

        // // select fields
        // if($filters->has('fields')) {
        //     $query->select($filters->fields);
        // }

        //  distinct
        if($filters->has('distinct')) {
            $query->select($filters->column_name)->distinct();
        }
        
        if($filters->has('table_filters')) {
            $query = $this->filterData($query, $filters);
        }

        // if($filters->has('order_type')) {
        //     $query->orderBy($filters->order_field, $filters->order_type);
        // }
    }

    public function filterData($query, $filters) {

        if($filters->has('name')) {
            if($filters->name != '--') {
                $query->where("name", $filters->name);
            }
        }

        if($filters->has('program')) {
            if($filters->program != '--') {
                $query->where("program", $filters->program);
            }
        }

        if($filters->has('student_status')) {
            if($filters->student_status != '--') {
                $query->where('student_status', $filters->student_status);
            }
        }

        // if($filters->has('mentor')) {
        //     if($filters->mentor != '--') {
        //         $query->where('mentor', $filters->mentor);
        //     }
        // }

        // if($filters->has('role')) {
        //     if($filters->role != '--') {
        //         $query->where('role', $filters->role);
        //     }
        // }

        // if($filters->has('status')) {
        //     if($filters->status != '--') {
        //         $query->where('status', $filters->status);
        //     }
        // }

        return $query;
    }
}
