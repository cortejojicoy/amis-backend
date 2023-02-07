<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Ma;

class MaTxn extends Model
{
    use HasFactory;

    protected $table = 'matxns';
    protected $primaryKey = 'mas_txn_id';

    protected $fillable = [
        'mas_id',
        'action',
        'committed_by',
        'note',
        'created_at',
    ];

    // protected $hidden = [
    //     'created_at'
    // ];

    public function scopeFilter($query, $filters, $role, $tagProcessor){
        if($role == 'students') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("matxns.mas_id as trx_id, to_char(matxns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, action as trx_status, u.email as last_commit, ma.actions as action, note, ma.mentor_name as mentor, mr.titles as mentor_role, ma.faculty_id"))
                ->join('mas AS ma', 'ma.id', '=', 'matxns.mas_id')
                ->join('users as u', 'u.uuid', '=', 'matxns.committed_by')
                ->join('mentor_roles as mr', 'mr.id', '=', 'ma.mentor_role');
            }  
            
            if($filters->has('uuid')) {
                $query->where('ma.uuid', $filters->uuid);
            }
        } else if ($role == 'faculties') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("matxns.mas_id as trx_id, to_char(matxns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, action as trx_status, u.email as last_commit, ma.actions as action, note, ma.mentor_name as mentor, mr.titles as mentor_role, ma.faculty_id"))
                        ->join('mas as ma', 'ma.id', '=', 'matxns.mas_id')
                        ->join('users as u', 'u.uuid', '=', 'matxns.committed_by')
                        ->join('mentor_roles as mr', 'mr.id', '=', 'ma.mentor_role');
            }
            
            // if($filters->mentor->faculty_id != '') {
            //     $query->where('ma.faculty_id', $filters->mentor->faculty_id);
            // }

        } else if ($role == 'admins') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("matxns.mas_id as trx_id, to_char(matxns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, action as trx_status, u.email as last_commit, ma.actions as action, note, ma.mentor_name as mentor, mr.titles as mentor_role, ma.faculty_id"))
                ->join('mas as ma', 'ma.id', '=', 'matxns.mas_id')
                ->join('mentor_roles as mr', 'mr.id', '=', 'ma.mentor_role')
                ->join('students as s', 's.uuid' ,'=', 'ma.uuid')
                ->join('users as u', 'u.uuid', '=', 'matxns.committed_by')
                ->join('student_program_records', 'student_program_records.campus_id', '=', 's.campus_id');
            }

            $query = $tagProcessor->process($query, $filters);
        }

        if($filters->has('distinct')) {
            $query->select($filters->column_name)->distinct();
        }

        $query = $this->filterData($query, $filters);

        if($filters->has('order_type')) {
            $query->orderBy($filters->order_field, $filters->order_type);
        }
    }

    public function filterData($query, $filters) {
        if($filters->has('id')) {
            if($filters->id != '--') {
                $query->where('mas.id', $filters->id);
            }
        }

        if($filters->has('action')) {
            if($filters->action != '--') {
                $query->where('action', $filters->action);
            }
        }

        if($filters->has('mentor_name')) {
            if($filters->mentor_name != '--') {
                $query->where('ma.mentor_name', $filters->mentor_name);
            }
        }

        return $query;
    }
}
