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
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,  HasRoles, HasPermissions;
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

    public function user_permission_tags() {
        return $this->hasMany(UserPermissionTag::class, 'model_id', 'sais_id');
    }

    public function getFullNameAttribute() {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }

    public function scopeFilter($query, $filters) {
        if($filters->has('personal_information')) {
            $query->with(['student', 'faculty', 'admin', 'student.student_grades', 'student.program_records' => function ($query) use($filters) {
                $query->where('student_program_records.status', '=', $filters->program_record_status);
            }]);
        }

        if($filters->has('user_module')) {
            $query->with(['roles', 'permissions', 'user_permission_tags']);
        }

        $query = $this->filterData($query, $filters);
    }

    public function filterData($query, $filters) {
        if($filters->has('sais_id')) {
            if($filters->sais_id != '--') {
                $query->where('sais_id', $filters->sais_id);
            }
        }

        return $query;
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

    
    public function scopeKeywords($query, $search) {
        // $query->select(DB::raw("faculties.sais_id AS faculty, CONCAT(users.first_name,' ',users.middle_name,' ',users.last_name) AS NAME, student_program_records.academic_program_id AS program, users.sais_id, student_program_records.status"))
        // ->distinct()
        // ->leftJoin('students', 'students.sais_id', '=', 'users.sais_id')
        // ->leftJoin('student_program_records', 'student_program_records.campus_id', '=', 'students.campus_id')
        // ->leftJoin('mentors', 'mentors.student_program_record_id', '=', 'student_program_records.student_program_record_id')
        // ->leftJoin('faculties', 'faculties.id', '=', 'mentors.faculty_id')
        // ->where('users.first_name', 'LIKE', "$search")
        // ->orWhere('users.middle_name', 'LIKE', "$search")
        // ->orWhere('users.last_name', 'LIKE', "$search");

        $query->select(DB::raw("CONCAT(users.first_name,' ',users.middle_name,' ',users.last_name) AS NAME"))
        ->where('users.first_name', 'LIKE', "$search")
        ->orWhere('users.middle_name', 'LIKE', "$search")
        ->orWhere('users.last_name', 'LIKE', "$search");
    }
}
