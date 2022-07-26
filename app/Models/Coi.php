<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Coi extends Model
{
    use HasFactory;

    protected $primaryKey = 'coi_id';
    public $incrementing = false; // add this to custom primary keys
    protected $keyType = 'string'; // also this

    protected $fillable = [
        'coi_id',
        'class_id',
        'status',
        'sais_id',
        'comment',
        'submitted_to_sais',
        'created_at',
    ];

    public function coitxns()
    {
        return $this->hasMany(CoiTxn::class, 'coi_id', 'coi_id');
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
            $query->where('cois.class_id', $filters->class_nbr);
        }

        if($role == 'faculties') {
            if($filters->has('sais_id')) {
                $query->where('co.id', $filters->sais_id);
            }

            if($filters->has('status')) {
                $query->where('cois.status', $filters->status);
            }

            if($filters->has('ctxn_action')) {
                $query->where('ctxn.action', $filters->ctxn_action);
            }

            if($filters->has('with_students')) {
                $query->with(['user', 'coitxns' => function ($query) use($filters) {
                    $query->where('coitxns.action', '=', $filters->coi_txn_status);
                }]);
            }
        }
    }
}
