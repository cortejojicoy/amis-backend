<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Mentor extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'mentor_id';
    protected $table = 'mentors';

    protected $fillable = [
        'faculty_id',
        'student_program_record_id',
        'student_sais_id',
        'mentor_role',
        'field_represented',
        'status',
        'start_date',
        'end_date'
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'id', 'faculty_id');
    }

    public function mentor()
    {
        return $this->belongsTo(MaStudent::class, 'student_sais_id', 'mentor_id');
    }

    public function scopeActiveMentor($query) {
        $query->distinct()
              ->leftJoin('faculties', 'faculties.id', '=', 'mentors.faculty_id')
              ->leftJoin('users', 'users.sais_id', '=', 'faculties.sais_id')
              ->where('mentors.student_sais_id', Auth::user()->sais_id)
              ->where('removed', 0);
    }

    public function scopeMentorRole($query) {
        $query->with('faculty', function($query) {
            $query->distinct()->where('faculties.sais_id', Auth::user()->sais_id);
        });
    }
}
