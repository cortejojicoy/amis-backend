<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $primaryKey = 'tag_id';

    use HasFactory;

    protected $fillable = [
        'tag_name',
        'reference_model',
        'reference_field',
        'reference_value',
        'reference_type',
        'reference_operation'
    ];

    public function scopeFilter($query, $filters)
    {
        //select fields
        if($filters->has('fields')) {
            $query->select($filters->fields);
        }

        //where clauses
        if($filters->has('id')){
            $query->where('tag_id', '=', $filters->id);
        }

        //order
        if($filters->has('order_type')) {
            $query->orderBy($filters->order_field, $filters->order_type);
        }
        
        //with clauses
        // $query->with(['teachingModel']);

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
        if($filters->has('tag_name')) {
            if($filters->tag_name != '--') {
                $query->where('tag_name', $filters->tag_name);
            }
        }

        return $query;
    }
}
