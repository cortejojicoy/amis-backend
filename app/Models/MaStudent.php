<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaStudent extends Model
{
    use HasFactory;

    protected $table = 'mentor_assignment_students';
    protected $primaryKey = 'sais_id';
    public $timestamps = false;
    protected $fillable = [
        'sais_id',
        'name',
        'program',
        'status',
        'acad_group',
        'adviser',
        'mentor_id',
        'mentor_name',
        'mentor_role',
        'mentor_status',
        'actions',
        'pending',
        'endorsed',
        'approved',
    ];

    public function scopeFaculty($query) {
        $query->where('mentor_id', Auth::user()->sais_id)->where('endorsed', 0);
    }

    public function scopeUnit($query) {
        $query->where('endorsed', 0);
    }

    public function scopeCollege($query) {
        $query->where('endorsed', 1);
    }

    public function user()
    {
        return $this->hasMany(User::class, 'sais_id', 'sais_id');
    }

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'student_sais_id', 'sais_id');
    }

    public function scopeFilter($query, $filters, $roles) {
        if($roles == 'faculties') {
            if($filters->faculty == 'adviser') {
                $query->where('approved', 1)->where('mentor_id', Auth::user()->sais_id);
            }
            
            if($filters->faculty == 'nominated') {
                $query->where('endorsed', 0)->where('mentor_id', Auth::user()->sais_id);
            }
            
        }  
        
        if($roles == 'admins') {
            if($filters->admin == 'unit') {
                // students will not display to unit if they have existing adviser
                $query->where('endorsed', 0)->where('adviser', 0);
            }

            if($filters->admin == 'college') {
                // students will display to college based on admins college
                $query->where('endorsed', 1)->where('acad_group', $filters->tags->college);
            }
        }

        if($filters->has('distinct')) {
            $query->select($filters->column_name)->distinct();
        }
        $query = $this->filterData($query, $filters);
    }

    public function filterData($query, $filters) {
        if($filters->has('name')) {
            if($filters->name != '--') {
                $query->where('name', $filters->name);
            }
        }

        if($filters->has('program')) {
            if($filters->program != '--') {
                $query->where('program', $filters->program);
            }
        }

        if($filters->has('status')) {
            if($filters->status != '--') {
                $query->where('status', $filters->status);
            }
        }

        if($filters->has('mentor_name')) {
            if($filters->mentor_name != '--') {
                $query->where('mentor_name', $filters->mentor_name);
            }
        }

        if($filters->has('mentor_role')) {
            if($filters->mentor_role != '--') {
                $query->where('mentor_role', $filters->mentor_role);
            }
        }

        if($filters->has('mentor_status')) {
            if($filters->mentor_status != '--') {
                $query->where('mentor_status', $filters->mentor_status);
            }
        }
        
        return $query;
    }
}