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
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    public function scopeActiveMentor($query) {
        $query->with(['faculty', 'faculty.user', 'faculty.mentor' => function($query) {
            $query->where('student_sais_id', Auth::user()->sais_id);
        }]);
    }

    public function scopeMentorRole($query) {
        $query->with('faculty', function($query) {
            $query->distinct()->where('faculties.sais_id', Auth::user()->sais_id);
        });
    }
}
