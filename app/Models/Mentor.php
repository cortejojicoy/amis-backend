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

    public function faculty() {
        return $this->hasMany(Faculty::class, 'faculty_id', 'faculty_id');
    }

    public function mentor_role() {
        return $this->belongsTo(MentorRole::class, 'mentor_role', 'id');
    }

    public function mas() {
        return $this->belongsTo(Ma::class, 'student_sais_id', 'student_sais_id');
    }

    public function scopeActiveMentor($query, $request) {
        $query->with(['mentor_role', 'faculty', 'faculty.uuid', 'faculty.mentor' => function($query) {
            $query->where('status', '=', 'ACTIVE');
        }]);

        if($request->has('sais_id')) {
            $query->where('student_sais_id', $request->sais_id);
        }
    }

    public function scopeMentorRole($query) {
        $query->with('faculty', function($query) {
            $query->distinct()->where('faculties.sais_id', Auth::user()->sais_id);
        });
    }
}
