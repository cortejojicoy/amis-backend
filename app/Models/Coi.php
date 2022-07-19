<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coi extends Model
{
    use HasFactory;

    protected $primaryKey = 'coi_id';

    protected $fillable = [
        'coi_id',
        'class_id',
        'status',
        'student_id',
        'comment',
        'submitted_to_sais',
        'created_at',
    ];

    public function coitxns()
    {
        return $this->hasMany(CoiTxn::class);
    }

    public function post()
    {
        return $this->belongsTo(Student::class, 'student_id', 'campus_id');
    }
}
