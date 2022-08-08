<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CoiTxn extends Model
{
    use HasFactory;

    protected $table = 'coitxns';
    protected $primaryKey = 'coi_txn_id';

    protected $fillable = [
        'coi_id',
        'action',
        'committed_by',
        'note',
        'created_at'
    ];

    protected $hidden = [
        'created_at'
    ];

    public function coi()
    {
        return $this->belongsTo(Coi::class);
    }

    public function scopeFilter($query, $filters, $role) {
        if($role == 'students') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("c.coi_id as reference_id, co.course, co.section, CONCAT(co.days, ' ', co.times) AS schedule, coitxns.note, coitxns.action, to_char(coitxns.created_at, 'DD MON YYYY hh12:mi AM') as date_created, u.email as committed_by, to_char(c.submitted_to_sais, 'DD MON YYYY hh12:mi AM') as last_action_date"))
                    ->leftJoin('cois AS c', 'c.coi_id', '=', 'coitxns.coi_id')
                    ->leftJoin('course_offerings AS co', 'c.class_id', 'co.class_nbr')
                    ->leftJoin('users AS u', 'u.sais_id', '=', 'coitxns.committed_by')
                    ->leftJoin('students AS s', 's.sais_id', 'u.sais_id');
            }
            
            if($filters->has('sais_id')) {
                $query->where('c.sais_id', $filters->sais_id);
            }

            if($filters->has('distinct')) {
                $query->select($filters->column_name)->distinct();
            }
        } else if($role == 'faculties') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("c.coi_id as reference_id, co.term, co.course as class, co.section, s.campus_id as student_no, to_char(coitxns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, coitxns.action as trx_status, u.email as last_commit"))
                    ->join('cois AS c', 'c.coi_id', '=', 'coitxns.coi_id')
                    ->join('students AS s', 's.sais_id', '=', 'c.sais_id')
                    ->join('users AS u', 'u.sais_id', '=', 'coitxns.committed_by')
                    ->join('course_offerings AS co', 'co.class_nbr', '=', 'c.class_id')
                    ->join('faculties AS f', 'f.sais_id', '=', 'co.id');
            }

            if($filters->has('sais_id')) {
                $query->where('f.sais_id', $filters->sais_id);
            }
        }

        if($filters->has('order_type')) {
            $query->orderBy($filters->order_field, $filters->order_type);
        }
    }
}
