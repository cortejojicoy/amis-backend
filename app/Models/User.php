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

    public function save_mentor() {
        return $this->hasMany(SaveMentor::class, 'sais_id', 'sais_id');
    }

    public function admin() {
        return $this->hasOne(Admin::class, 'sais_id', 'sais_id');
    }

    public function user_permission_tags() {
        return $this->hasMany(UserPermissionTag::class, 'model_id', 'sais_id');
    }

    public function mentor() {
        return $this->hasMany(Mentor::class, 'student_sais_id', 'sais_id');
    }

    public function getFullNameAttribute() {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }

    // BELOW WAS LINK TO UUID 
    public function student_uuid() {
        return $this->hasOne(Student::class, 'uuid', 'uuid');
    }

    public function faculty_uuid() {
        // return $this->
    }


    public function scopeFilter($query, $filters) {
        if($filters->has('personal_information')) {
            $query->with(['student', 'faculty', 'admin', 'student.student_grades', 'student.program_records' => function ($query) use($filters) {
                $query->where('student_program_records.status', '=', $filters->program_record_status);
            }]);
        }

        if($filters->has('student_information')) {
            $query->with(['student_uuid', 'student_uuid.program_records']);

            if($filters->has('uuid')) {
                $query->where('uuid', $filters->uuid);
            }
        }

        if($filters->has('user_module')) {
            $query->with(['roles', 'permissions', 'user_permission_tags']);
        }

        if($filters->has('student_add_mentor')) {
            $query->with(['student', 'save_mentor','mentor.mentor_role', 'mentor.faculty.uuid', 'student.program_records' => function($query) {
                $query->where('student_program_records.status', '=', 'ACTIVE');
            }]);

            // $query->whereHas('mentor.faculty.faculty_appoint', function($query) {

            // });
            // $query->whereHas('mentor.faculty.faculty_appoint.student_program', function($query) use($filters) {
            //     $query->where('programs.acronym', $filters->program);
            // });

            // $query->whereHas('mentor', function($query) {
            //     $query->where('mentors.status', '=', 'ACTIVE');
            // });

            // $query->with('faculty_add_mentor');

            if($filters->has('sais_id')) {
                $query->where('sais_id', $filters->sais_id);
            }
        }

        //select fields
        // if($filters->has('fields')) {
        //     $query->select($filters->fields);
        // }

        //order
        // if($filters->has('order_type')) {
        //     $query->orderBy($filters->order_field, $filters->order_type);
        // }


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
}