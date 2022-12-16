<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    public function degree()
    {
        return $this->belongsTo(Degree::class, 'degree_id', 'degree_id');
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
        $query->with(['degree']);

        //distinct
        if($filters->has('distinct')) {
            $query->select($filters->column_name)->distinct();
        }

        $query = $this->filterData($query, $filters);
    }

    public function filterData($query, $filters) {
        if($filters->has('acronym')) {
            if($filters->acronym != '--') {
                $query->where('acronym', $filters->acronym);
            }
        }

        if($filters->has('college')) {
            if($filters->college != '--') {
                $query->where('college', $filters->college);
            }
        }

        return $query;
    }
}
