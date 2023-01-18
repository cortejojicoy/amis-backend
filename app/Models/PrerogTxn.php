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
                $query->select(DB::raw("p.prg_id as reference_id, p.term, co.course, co.section, CONCAT(co.days, ' ', co.times) AS schedule, prerog_txns.note, prerog_txns.action, to_char(prerog_txns.created_at, 'DD MON YYYY hh12:mi AM') as date_created, u.email as committed_by, to_char(p.submitted_to_sais, 'DD MON YYYY hh12:mi AM') as last_action_date"))
                    ->leftJoin('prerogs AS p', 'p.prg_id', '=', 'prerog_txns.prg_id')
                    ->leftJoin('course_offerings AS co', function($query) {
                        $query->ON('co.class_nbr','=','p.class_id')
                            ->where('co.term', '=', DB::raw('p.term'));
                    })
                    ->leftJoin('users AS u', 'u.sais_id', '=', 'prerog_txns.committed_by')
                    ->leftJoin('students AS s', 's.sais_id', 'u.sais_id');
            }

            if($filters->has('sais_id')) {
                $query->where('p.sais_id', $filters->sais_id);
            }
        } else if($role == 'faculties') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("p.prg_id as reference_id, p.term, co.course, co.section, s.campus_id as student_no, prerog_txns.action, to_char(prerog_txns.created_at, 'DD MON YYYY hh12:mi AM') as date_created, u.email as committed_by, to_char(p.submitted_to_sais, 'DD MON YYYY hh12:mi AM') as last_action_date"))
                    ->join('prerogs AS p', 'p.prg_id', '=', 'prerog_txns.prg_id')
                    ->join('students AS s', 's.sais_id', '=', 'p.sais_id')
                    ->join('users AS u', 'u.sais_id', '=', 'prerog_txns.committed_by')
                    ->join('course_offerings AS co', 'co.class_nbr', '=', 'p.class_id')
                    ->leftJoin('course_offerings AS co', function($query) {
                        $query->ON('co.class_nbr','=','p.class_id')
                            ->where('co.term', '=', DB::raw('p.term'));
                    })
                    ->join('faculties AS f', 'f.sais_id', '=', 'co.id');
            }

            if($filters->has('sais_id')) {
                $query->where('f.sais_id', $filters->sais_id);
            }
        } else if($role == 'admins') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("p.prg_id as reference_id, p.term, co.course, co.section, s.campus_id as student_no, spr.academic_program_id as degree, prerog_txns.action, to_char(prerog_txns.created_at, 'DD MON YYYY hh12:mi AM') as date_created, u.email as committed_by, to_char(p.submitted_to_sais, 'DD MON YYYY hh12:mi AM') as last_action_date"))
                    ->join('prerogs AS p', 'p.prg_id', '=', 'prerog_txns.prg_id')
                    ->join('students AS s', 's.sais_id', '=', 'p.sais_id')
                    ->join('users AS u', 'u.sais_id', '=', 'prerog_txns.committed_by')
                    ->leftJoin('course_offerings AS co', function($query) {
                        $query->ON('co.class_nbr','=','p.class_id')
                            ->where('co.term', '=', DB::raw('p.term'));
                    })
                    ->join('student_program_records as spr', 's.campus_id', 'spr.campus_id')
                    ->where('spr.status', 'ACTIVE');
            }

            if($filters->admin->college != '') {
                $query->where('spr.acad_group', $filters->admin->college);
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

    //populate this function in every model with filters needed for this model.
    public function filterData($query, $filters) {
        if($filters->has('course')) {
            if($filters->course != '--') {
                $query->where('co.course', $filters->course);
            }
        }

        if($filters->has('action')) {
            if($filters->action != '--') {
                $query->where('prerog_txns.action', $filters->action);
            }
        }

        if($filters->has('campus_id')) {
            if($filters->campus_id != '--') {
                $query->where('s.campus_id', $filters->campus_id);
            }
        }

        if($filters->has('prg_id')) {
            if($filters->prg_id != '--') {
                $query->where('p.prg_id', $filters->prg_id);
            }
        }

        return $query;
    }
}