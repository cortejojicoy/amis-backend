<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Mentor extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'mentor_id';
    protected $table = 'mentors';

    public function scopeActiveMentor($query)
    {
        $query->distinct()
              ->leftJoin('faculties', 'faculties.id', '=', 'mentors.faculty_id')
              ->leftJoin('users', 'users.sais_id', '=', 'faculties.sais_id')
              ->where('mentors.student_sais_id', Auth::user()->sais_id);
    }
}
