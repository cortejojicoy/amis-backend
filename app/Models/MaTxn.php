<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Ma;

class MaTxn extends Model
{
    use HasFactory;

    protected $table = 'mentor_assignment_txns';
    protected $primaryKey = 'mas_txn_id';

    protected $fillable = [
        'mas_txn_id',
        'mas_id',
        'action',
        'committed_by',
        'note',
        'created_at',
    ];

    public function scopeFilter($query, $filters, $role){
        if($role == 'students') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("mentor_assignment_txns.mas_id as trx_id, to_char(mentor_assignment_txns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, action as trx_status, u.email as last_commit, ma.actions as action, note, ma.mentor_name as mentor, ma.mentor_role, ma.mentor_id"))
                ->leftJoin('mentor_assignments AS ma', 'ma.mas_id', '=', 'mentor_assignment_txns.mas_id')
                ->leftJoin('users as u', 'u.sais_id', '=', 'mentor_assignment_txns.committed_by');
            }  
            
            if($filters->has('sais_id')) {
                $query->where('ma.student_sais_id', $filters->sais_id);
            }
        } else if ($role == 'faculties') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("mentor_assignment_txns.mas_id as trx_id, to_char(mentor_assignment_txns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, action as trx_status, u.email as last_commit, ma.actions as action, note, ma.mentor_name as mentor, ma.mentor_role, ma.mentor_id"))
                ->leftJoin('mentor_assignments as ma', 'ma.mas_id', '=', 'mentor_assignment_txns.mas_id')
                ->leftJoin('users as u', 'u.sais_id', '=', 'mentor_assignment_txns.committed_by')
                ->leftJoin('mentor_assignment_students as mas', 'mas.mentor_id', '=', 'ma.mentor_id');
                
            }
            if($filters->has('sais_id')) {
                $query->where('mas.mentor_id', $filters->mentor_id);
            }
        } else if ($role == 'admins') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("mentor_assignment_txns.mas_id as trx_id, to_char(mentor_assignment_txns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, action as trx_status, u.email as last_commit, ma.actions as action, note, ma.mentor_name as mentor, ma.mentor_role, ma.mentor_id"))
                ->leftJoin('mentor_assignments as ma', 'ma.mas_id', '=', 'mentor_assignment_txns.mas_id')
                ->leftJoin('users as u', 'u.sais_id', '=', 'mentor_assignment_txns.committed_by')
                ->leftJoin('admins as a', 'a.sais_id', '=', 'u.sais_id')
                ->leftJoin('students as s', 's.sais_id' ,'=', 'ma.student_sais_id')
                ->leftJoin('student_program_records as spr', 'spr.campus_id', '=', 's.campus_id');
            }

            if($filters->admin->college != '') {
                $query->where('spr.acad_group', $filters->admin->college);
            }
        }

        if($filters->has('distinct')) {
            $query->select($filters->column_name)->distinct();
        }

        $query = $this->filterData($query, $filters);
    }

    public function filterData($query, $filters) {
        if($filters->has('transaction_id')) {
            if($filters->transaction_id != '--') {
                $query->where('ma.mas_id', $filters->transaction_id);
            }
        }

        if($filters->has('status')) {
            if($filters->status != '--') {
                $query->where('action', $filters->status);
            }
        }

        if($filters->has('mentor')) {
            if($filters->mentor != '--') {
                $query->where('ma.mentor_name', $filters->mentor);
            }
        }

        return $query;
    }
}
