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

    public function courseOfferings() {
        return $this->hasMany(CourseOffering::class, 'id', 'sais_id');
    }

    public function user() {
        return $this->belongsTo(User::class,'sais_id','sais_id');
    }

    public function appointment() {
        return $this->belongsTo(FacultyAppointment::class, 'faculty_id', 'faculty_id');
    }

    public function uuid() {
        return $this->belongsTo(User::class, 'uuid', 'uuid');
    }

    public function mentor() {
        return $this->hasMany(Mentor::class, 'faculty_id', 'faculty_id');
    }

    public function scopeFilter($query, $filters) {
        if($filters->has('advisees')) {
            $query->with(['user', 'mentor.faculty', 'mentor.mentor_role','mentor.student.student_user', 'mentor.student.program_records' => function($query) {
                $query->where('student_program_records.status', '=', 'ACTIVE');
            }]);
        }

        if($filters->has('faculty_list')) {
            $query->with(['user', 'appointment']);
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
        /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeInfo($query, $filters) {
        $query->join('users', 'users.uuid', '=', 'faculties.uuid');
    }

    public function scopeFilter($query, $filters) {
        if($filters->has('fields')) {
            $query->select($filters->fields);
        }

        //where clauses
        if($filters->has('sais_id')){
            $query->where('sais_id', '=', $filters->sais_id);
        }

        //order
        if($filters->has('order_type')) {
            $query->orderBy($filters->order_field, $filters->order_type);
        }

        //distinct
        if($filters->has('distinct')) {
            $query->select($filters->column_name)->distinct();
        }

        if($filters->has('with_user')) {
            $query->with(['user']);
        }

        //with clauses
        // if($filters->has('with_prg')) {
        //     $query->with(['prerogs' => function ($query) use($filters) {
        //         $query->whereIn('prerogs.status', $filters->prg_status);
                
        //         if($filters->has('prg_term')) {
        //             $query->where('prerogs.term', $filters->prg_term);
        //         }
        //     }, 'prerogs.user', 'prerogs.student', 'prerogs.student.program_records' => function ($query) {
        //         $query->where('student_program_records.status', '=', 'ACTIVE');
        //     },'prerogs.prerog_txns' => function ($query) use($filters) {
        //         $query->where('prerog_txns.action', '=', $filters->prg_txn_status);
        //     }]);
        // }

        // $query = $this->filterData($query, $filters);
    }
}
