<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
                // ->leftJoin('mentor_status as ms', 'ms.student_sais_id', '=', 'ma.student_sais_id');
                // ->leftJoin('mentor_assignment_students as mas', 'mas.sais_id', '=', 'ma.student_sais_id');
                        // select mtxn.mas_id as trx_id, to_char(mtxn.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, mtxn.action as trx_status, u.email as last_commit, ma.actions as action, mtxn.note, ma.mentor_name as mentor, ma.mentor_role, ma.mentor_id from mentor_assignment_txns AS mtxn
                        // left join mentor_assignments AS ma on ma.mas_id = mtxn.mas_id
                        // left join users as u on u.sais_id = mtxn.committed_by
                        // left join mentor_assignment_students as mas on mas.sais_id = ma.student_sais_id
                    
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
                ->leftJoin('admins as a', 'a.sais_id', '=', 'u.sais_id');
            }

            if($filters->has('sais_id')) {
                $query->where('a.sais_id', $filters->sais_id);
            }
        }
    }
}
