<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prerog extends Model
{
    use HasFactory;
    
    protected $table = 'prerogs';
    protected $primaryKey = 'prg_id';
    protected $keyType = 'string'; 
    
    public $incrementing = false;

    protected $fillable = [
        'prg_id',
        'class_id',
        'status',
        'sais_id',
        'comment',
        'submitted_to_sais',
        'created_at',
    ];

    public function prerog_txns()
    {
        return $this->hasMany(PrerogTxn::class, 'prg_id', 'prg_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'sais_id', 'sais_id');
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, Student::class, 'sais_id', 'sais_id', 'sais_id', 'sais_id');
    }

    public function course_offering()
    {
        return $this->belongsTo(CourseOffering::class, 'class_id', 'class_nbr');
    }

    public function scopeFilter($query, $filters, $role) {
        if($filters->has('class_nbr')) {
            $query->where('prerogs.class_id', $filters->class_nbr);
        }

        if($role == 'faculties') {
            if($filters->has('sais_id')) {
                $query->where('co.id', $filters->sais_id);
            }

            if($filters->has('status')) {
                $query->whereIn('prerogs.status', $filters->status);
            }

            if($filters->has('with_students')) {
                $query->with(['user', 'student', 'prerog_txns' => function ($query) use($filters) {
                    $query->where('prerog_txns.action', '=', $filters->prg_txn_status);
                }]);
            }
        }

        if($role == 'admins') {
            if($filters->admin->university == 0) {
                // if the access is for college level
                if($filters->admin->college != '') {
                    $query->whereRelation('course_offering', 'acad_group', $filters->admin->college);
                } else if ($filters->admin->unit != '') { //if the access is for unit level
                    $query->whereRelation('course_offering', 'acad_org', $filters->admin->unit);
                }
            } else { //if university level
                if($filters->admin->graduate == 1 && $filters->admin->undergrad == 0) {
                    $query->whereRelation('course_offering', 'career', 'GRAD');
                } else if ($filters->admin->graduate == 0 && $filters->admin->undergrad == 1) {
                    $query->whereRelation('course_offering', 'career', 'UGRD');
                }
            }

            if($filters->has('prg_status')) {
                $query->whereIn('prerogs.status', $filters->prg_status);
            }

            if($filters->has('with_students')) {
                $query->with(['user', 'student', 'course_offering', 'prerog_txns' => function($query) use ($filters) {
                    $query->where('prerog_txns.action', '=', $filters->prg_txn_status);
                }]);
            }
        }
    }
}
