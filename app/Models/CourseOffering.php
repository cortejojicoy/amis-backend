<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CourseOffering extends Model
{
    use HasFactory;

    protected $primaryKey = 'course_offerings_id';
    protected $keyType = 'integer';

    protected $fillable = [
        'institution',
        'career',
        'term',
        'course_id',
        'acad_org',
        'acad_group',
        'course',
        'subject',
        'catalog',
        'class_nbr',
        'descr',
        'component',
        'section',
        'times',
        'days',
        'activity',
        'facil_id',
        'tot_enrl',
        'cap_enrl',
        'id',
        'name',
        'email',
        'consent',
        'prerog',
        'offer_nbr',
        'topic_id'
    ];

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

    public function term()
    {
        return $this->belongsTo(StudentTerm::class, 'term', 'term_id');
    }

    public function scopeFilter($query, $filters)
    {
        $query->whereRelation('term', 'status', 'ACTIVE');

        //select fields
        if($filters->has('fields')) {
            $query->select($filters->fields);
        }

        //add restriction here that consent = "I" should only be the ones displayed
        if($filters->has('consent')) {
            $query->where('consent', '=', $filters->consent);
        }

        if($filters->has('prerog')) {
            $query->where('prerog', '=', TRUE);
        }

        //order
        if($filters->has('order_type')) {
            $query->orderBy($filters->order_field, $filters->order_type);
        }

        //distinct
        if($filters->has('distinct')) {
            $query->select($filters->column_name)->distinct();
        }

        if($filters->has('with_faculties')) {
            $query->with(['faculty', 'faculty.user']);
        }

        $query = $this->filterData($query, $filters);

        //with clauses
        if($filters->has('with_cois')) {
            $query->with(['cois' => function ($query) use($filters) {
                $query->where('cois.status', '=', $filters->coi_status)
                    ->where('cois.term', '=', DB::raw("(select term_id from student_terms where status = 'ACTIVE')"));
            }, 'cois.user', 'cois.student', 'cois.coitxns' => function ($query) use($filters) {
                $query->where('coitxns.action', '=', $filters->coi_txn_status);
            }]);
        }

        //with clauses
        if($filters->has('with_prg')) {
            $query->with(['prerogs' => function ($query) use($filters) {
                $query->whereIn('prerogs.status', $filters->prg_status);
                
                if($filters->has('prg_term')) {
                    $query->where('prerogs.term', $filters->prg_term);
                }
            }, 'prerogs.user', 'prerogs.student', 'prerogs.student.program_records' => function ($query) {
                $query->where('student_program_records.status', '=', 'ACTIVE');
            },'prerogs.prerog_txns' => function ($query) use($filters) {
                $query->where('prerog_txns.action', '=', $filters->prg_txn_status);
            }]);
        }

    }

    public function filterData($query, $filters) {
        if($filters->has('course')) {
            if($filters->course != '--') {
                $query->where('course', $filters->course);
            }
        }

        if($filters->has('class_nbr')) {
            if($filters->class_nbr != '--') {
                $query->where('class_nbr', $filters->class_nbr);
            }
        }

        if($filters->has('component')) {
            if($filters->component != '--') {
                $query->where('component', $filters->component);
            }
        }

        if($filters->has('id')) {
            if($filters->id != '--') {
                $query->where('id', $filters->id);
            }
        }

        return $query;
    }
}
