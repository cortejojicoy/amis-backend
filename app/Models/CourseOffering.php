<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CourseOffering extends Model
{
    use HasFactory;

    public function cois()
    {
        return $this->hasMany(Coi::class, 'class_id', 'class_nbr');
    }

    public function prerogs()
    {
        return $this->hasMany(Prerog::class, 'class_id', 'class_nbr');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'id', 'sais_id');
    }

    public function scopeFilter($query, $filters)
    {
        //select fields
        if($filters->has('fields')) {
            $query->select($filters->fields);
        }

        //where clauses
        if($filters->has('id')){
            $query->where('id', '=', $filters->id);
        }

        if($filters->has('class_nbr')) {
            $query->where('class_nbr', '=', $filters->class_nbr);
        }

        if($filters->has('course')) {
            $query->where('course', '=', $filters->course);
        }

        //order
        if($filters->has('order_type')) {
            $query->orderBy($filters->order_field, $filters->order_type);
        }

        //distinct
        if($filters->has('distinct')) {
            $query->distinct();
        }

        //with clauses
        if($filters->has('with_cois')) {
            $query->with(['cois' => function ($query) use($filters) {
                $query->where('cois.status', '=', $filters->coi_status);
            }, 'cois.user', 'cois.student', 'cois.coitxns' => function ($query) use($filters) {
                $query->where('coitxns.action', '=', $filters->coi_txn_status);
            }]);
        }

        //with clauses
        if($filters->has('with_prg')) {
            $query->with(['prerogs' => function ($query) use($filters) {
                $query->whereIn('prerogs.status', $filters->prg_status);
            }, 'prerogs.user', 'prerogs.student', 'prerogs.student.program_records' ,'prerogs.prerog_txns' => function ($query) use($filters) {
                $query->where('prerog_txns.action', '=', $filters->prg_txn_status);
            }]);
        }
    }
}
