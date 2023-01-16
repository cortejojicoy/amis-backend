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

    // public function scopeFilter($query, $filters, $role, $tagProcessor) {
    public function scopeFilter($query, $filters, $role) {
        if($role == 'students') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("c.coi_id as reference_id, c.term, co.course, co.section, CONCAT(co.days, ' ', co.times) AS schedule, coitxns.note, coitxns.action, to_char(coitxns.created_at, 'DD MON YYYY hh12:mi AM') as date_created, u.email as committed_by, c.last_action, to_char(c.last_action_date, 'DD MON YYYY hh12:mi AM') as last_action_date"))
                    ->join('cois AS c', 'c.coi_id', 'coitxns.coi_id')
                    ->leftJoin('course_offerings AS co', function($query) {
                        $query->ON('co.class_nbr','=','c.class_id')
                            ->where('co.term', '=', DB::raw('c.term'));
                    })
                    ->leftJoin('users AS u', 'u.sais_id', '=', 'coitxns.committed_by')
                    ->leftJoin('students AS s', 's.sais_id', 'u.sais_id');
            }
            
            if($filters->has('sais_id')) {
                $query->where('c.sais_id', $filters->sais_id);
            }
        } else if($role == 'faculties') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("c.coi_id as reference_id, c.term, co.course as class, co.section, s.campus_id as student_no, to_char(coitxns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, coitxns.action as trx_status, u.email as last_commit"))
                    ->join('cois AS c', 'c.coi_id', '=', 'coitxns.coi_id')
                    ->join('students AS s', 's.sais_id', '=', 'c.sais_id')
                    ->join('users AS u', 'u.sais_id', '=', 'coitxns.committed_by')
                    ->leftJoin('course_offerings AS co', function($query) {
                        $query->ON('co.class_nbr','=','c.class_id')
                            ->where('co.term', '=', DB::raw('c.term'));
                    })
                    ->join('faculties AS f', 'f.sais_id', '=', 'co.id');
            }

            if($filters->has('sais_id')) {
                $query->where('f.sais_id', $filters->sais_id);
            }
        } else if($role == 'admins') {
            if($filters->has('txn_history')) {
                $query->select(DB::raw("co.acad_group, co.acad_org, co.career, c.coi_id as reference_id, c.term, co.course as class, co.section, s.campus_id as student_no, to_char(coitxns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, coitxns.action as trx_status, u.email as last_commit, c.last_action, to_char(c.last_action_date, 'DD MON YYYY hh12:mi AM') as last_action_date"))
                ->join('cois AS c', 'coitxns.coi_id', '=', 'c.coi_id')
                ->join('students AS s', 's.sais_id', '=', 'c.sais_id')
                ->join('users AS u', 'u.sais_id', '=', 'coitxns.committed_by')
                ->join('course_offerings AS co', 'co.class_nbr', '=', 'c.class_id')
                ->join('student_program_records as spr', 's.campus_id', 'spr.campus_id')
                ->where('spr.status', 'ACTIVE');
            }

            // $query = $tagProcessor->process($query, $filters);
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

    public function filterData($query, $filters) {
        if($filters->has('course')) {
            if($filters->course != '--') {
                $query->where('co.course', $filters->course);
            }
        }

        if($filters->has('action')) {
            if($filters->action != '--') {
                $query->where('coitxns.action', $filters->action);
            }
        }

        if($filters->has('coi_id')) {
            if($filters->coi_id != '--') {
                $query->where('c.coi_id', $filters->coi_id);
            }
        }

        if($filters->has('campus_id')) {
            if($filters->campus_id != '--') {
                $query->where('s.campus_id', $filters->campus_id);
            }
        }

        return $query;
    }
}
