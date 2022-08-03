<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PrerogTxn extends Model
{
    use HasFactory;

    protected $table = 'prerog_txns';
    protected $primaryKey = 'prg_txn_id';

    protected $fillable = [
        'prg_id',
        'action',
        'committed_by',
        'note',
        'created_at'
    ];

    public function prerog()
    {
        return $this->belongsTo(Prerog::class);
    }

    public function scopeFilter($query, $filters, $role) {
        if($role == 'students') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("p.prg_id as reference_id, co.course, co.section, CONCAT(co.days, ' ', co.times) AS schedule, prerog_txns.note, prerog_txns.action, to_char(prerog_txns.created_at, 'DD MON YYYY hh12:mi AM') as date_created, u.email as committed_by, to_char(p.submitted_to_sais, 'DD MON YYYY hh12:mi AM') as last_action_date"))
                    ->leftJoin('prerogs AS p', 'p.prg_id', '=', 'prerog_txns.prg_id')
                    ->leftJoin('course_offerings AS co', 'p.class_id', 'co.class_nbr')
                    ->leftJoin('users AS u', 'u.sais_id', '=', 'prerog_txns.committed_by')
                    ->leftJoin('students AS s', 's.sais_id', 'u.sais_id');
            }

            if($filters->has('sais_id')) {
                $query->where('p.sais_id', $filters->sais_id);
            }
        } else if($role == 'faculties') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("p.prg_id as reference_id, co.term, co.course, co.section, s.campus_id as student_no, prerog_txns.action, to_char(prerog_txns.created_at, 'DD MON YYYY hh12:mi AM') as date_created, u.email as committed_by, to_char(p.submitted_to_sais, 'DD MON YYYY hh12:mi AM') as last_action_date"))
                    ->join('prerogs AS p', 'p.prg_id', '=', 'prerog_txns.prg_id')
                    ->join('students AS s', 's.sais_id', '=', 'p.sais_id')
                    ->join('users AS u', 'u.sais_id', '=', 'prerog_txns.committed_by')
                    ->join('course_offerings AS co', 'co.class_nbr', '=', 'p.class_id')
                    ->join('faculties AS f', 'f.sais_id', '=', 'co.id');
            }

            if($filters->has('sais_id')) {
                $query->where('f.sais_id', $filters->sais_id);
            }
        } else if($role == 'admins') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("p.prg_id as reference_id, co.term, co.course, co.section, s.campus_id as student_no, spr.academic_program_id as degree, prerog_txns.action, to_char(prerog_txns.created_at, 'DD MON YYYY hh12:mi AM') as date_created, u.email as committed_by, to_char(p.submitted_to_sais, 'DD MON YYYY hh12:mi AM') as last_action_date"))
                    ->join('prerogs AS p', 'p.prg_id', '=', 'prerog_txns.prg_id')
                    ->join('students AS s', 's.sais_id', '=', 'p.sais_id')
                    ->join('users AS u', 'u.sais_id', '=', 'prerog_txns.committed_by')
                    ->join('course_offerings AS co', 'co.class_nbr', '=', 'p.class_id')
                    ->join('student_program_records as spr', 's.campus_id', 'spr.campus_id');
            }

            // if($filters->admin->university == 0) {
            //     // if the access is for college level
            //     if($filters->admin->college != '') {
            //         $query->where('co.acad_group', $filters->admin->college);
            //     } else if ($filters->admin->unit != '') { //if the access is for unit level
            //         $query->where('co.acad_org', $filters->admin->unit);
            //     }
            // } else { //if university level
            //     if($filters->admin->graduate == 1 && $filters->admin->undergrad == 0) {
            //         $query->where('co.career', 'GRAD');
            //     } else if ($filters->admin->graduate == 0 && $filters->admin->undergrad == 1) {
            //         $query->where('co.career', 'UGRD');
            //     }
            // }
            if($filters->admin->college != '') {
                $query->where('spr.acad_group', $filters->admin->college);
            }
        }

        if($filters->has('order_type')) {
            $query->orderBy($filters->order_field, $filters->order_type);
        }
    }
}