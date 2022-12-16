<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pcw extends Model
{
    use HasFactory;

    protected $table = 'pcws';
    protected $primaryKey = 'pcw_id';
    protected $keyType = 'string';

    const DRAFT = 'Draft';
    const SUBMITTED = 'Submitted';
    const APPROVED = 'Approved';

    protected $fillable = [
        'pcw_id',
        'pcw_type',
        'term_id',
        'status',
        'sais_id',
        'comment',
        'created_at',
    ];

    public function pcwtxns()
    {
        return $this->morphMany(PcwTxn::class, 'pcwtxnable');
    }

    public function pcw_courses()
    {
        return $this->hasMany(PcwCourse::class, 'pcw_id', 'pcw_id');
    }

    public function scopeFilter($query, $filters, $role, $tagProcessor = null)
    {
        //select fields
        if($filters->has('fields')) {
            $query->select($filters->fields);
        }

        //where clauses
        if($filters->has('sais_id')){
            $query->where('pcws.sais_id', '=', $filters->sais_id);
        }
        if($filters->has('status')){
            $query->whereIn('pcws.status', $filters->status);
        }
        if($filters->has('type')){
            $query->where('pcws.pcw_type', '=', $filters->type);
        }

        //order
        if($filters->has('order_type')) {
            $query->orderBy($filters->order_field, $filters->order_type);
        }

        //distinct
        if($filters->has('distinct')) {
            $query->select($filters->column_name)->distinct();
        }

        if($role == 'student') {
            $query->with(['pcw_courses', 'pcw_courses.course', 'pcw_courses.term', 'pcwtxns']);
        } else if($role == 'faculty') {
            $query->with(['pcwtxns']);
        } else if($role == 'admin') {
            $query = $tagProcessor->process($query, $filters);
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
    }
}
