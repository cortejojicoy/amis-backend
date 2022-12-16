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
        'mas_txn_id',
        'mas_id',
        'action',
        'committed_by',
        'note',
        'created_at',
    ];

    // protected $hidden = [
    //     'created_at'
    // ];

    

    public function scopeFilter($query, $filters, $role){
        if($role == 'students') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("matxns.mas_id as trx_id, to_char(matxns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, action as trx_status, u.email as last_commit, ma.actions as action, note, ma.mentor_name as mentor, ma.mentor_role, ma.mentor_id"))
                ->join('mas AS ma', 'ma.mas_id', '=', 'matxns.mas_id')
                ->join('users as u', 'u.sais_id', '=', 'matxns.committed_by');
            }  
            
            if($filters->has('sais_id')) {
                $query->where('ma.student_sais_id', $filters->sais_id);
            }
        } else if ($role == 'faculties') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("matxns.mas_id as trx_id, to_char(matxns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, action as trx_status, u.email as last_commit, ma.actions as action, note, ma.mentor_name as mentor, ma.mentor_role, ma.mentor_id"))
                        ->join('mas as ma', 'ma.mas_id', '=', 'matxns.mas_id')
                        ->join('users as u', 'u.sais_id', '=', 'matxns.committed_by');
                // if($filters->mentor->faculty_id != NULL) {
                //     if($filters->mentor->faculty_id != '') {
                        
                //     }
                // }
            }
            
            if($filters->has('sais_id')) {
                $query->where('ma.mentor_id', $filters->sais_id);
            }
            
        } else if ($role == 'admins') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("matxns.mas_id as trx_id, to_char(matxns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, action as trx_status, u.email as last_commit, ma.actions as action, note, ma.mentor_name as mentor, ma.mentor_role, ma.mentor_id"))
                ->join('mas as ma', 'ma.mas_id', '=', 'matxns.mas_id')
                ->join('students as s', 's.sais_id' ,'=', 'ma.student_sais_id')
                ->join('users as u', 'u.sais_id', '=', 'matxns.committed_by')
                ->join('student_program_records as spr', 'spr.campus_id', '=', 's.campus_id');
            }

            if($filters->admin->college != '') {
                $query->where('spr.acad_group', $filters->admin->college);
            }

            if($filters->admin->unit != '') {
                $query->where('spr.acad_org', $filters->admin->unit);
            }
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
        if($filters->has('mas_id')) {
            if($filters->mas_id != '--') {
                $query->where('ma.mas_id', $filters->mas_id);
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
