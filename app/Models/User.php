<?php

namespace App\Models;

// use App\Traits\Uuids;

use Attribute;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,  HasRoles;
    protected $primaryKey = 'sais_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function student() {
        return $this->hasOne(Student::class, 'sais_id', 'sais_id');
    }

    public function faculty() {
        return $this->hasOne(Faculty::class, 'sais_id', 'sais_id');
    }

    public function admin() {
        return $this->hasOne(Admin::class, 'sais_id', 'sais_id');
    }

    public function getFullNameAttribute() {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }

    public function scopeFilter($query, $filters) {
        $query->with(['student', 'faculty', 'admin', 'student.student_grades', 'student.program_records' => function ($query) use($filters) {
            $query->where('student_program_records.status', '=', $filters->program_record_status);
        }]);
    }

    public function scopeStudentDetails($query, $request) {
        $query->leftJoin('students', 'students.sais_id', '=', 'users.sais_id')
        ->leftJoin('student_program_records', 'student_program_records.campus_id', '=', 'students.campus_id')
        ->where('users.sais_id', $request->sais_id);
    }

    public function scopeStudentById($query, $request) {
        // $query->with(['student', 'student.program_records', ''])

        $query->leftJoin('students', 'students.sais_id', '=', 'users.sais_id')
        ->leftJoin('student_program_records', 'student_program_records.campus_id', '=', 'students.campus_id')
        ->where('users.sais_id', $request->id);
    }

    public function mas() {
        return $this->hasMany(MaStudent::class, 'sais_id', 'sais_id');
    }
}
