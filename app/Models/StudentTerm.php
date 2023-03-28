<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTerm extends Model
{
    use HasFactory;
    protected $primaryKey = 'term_id';
    protected $table = 'student_terms';

    public function terms()
    {
        return $this->belongsTo(StudentGrades::class);
    }

    public function scopeFilter($query, $filters)
    {
        //select fields
        if($filters->has('fields')) {
            $query->select($filters->fields);
        }

        //where clauses
        // if($filters->has('id')){
        //     $query->where('course_id', '=', $filters->id);
        // }

        //order
        if($filters->has('order_type')) {
            $query->orderBy($filters->order_field, $filters->order_type);
        }
        
        //with clauses
        // $query->with(['program', 'curriculum_courses', 'curriculum_courses.course', 'curriculum_structures']);

        //distinct
        if($filters->has('distinct')) {
            $query->select($filters->column_name)->distinct();
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

        $query = $this->filterData($query, $filters);
    }

    public function filterData($query, $filters) {
        if($filters->has('term')) {
            if($filters->term != '--') {
                $query->where('term', $filters->term);
            }
        }

        return $query;
    }
}
