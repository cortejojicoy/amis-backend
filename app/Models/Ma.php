<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Ma;
use App\Models\User;
use App\Models\Mentor;
use App\Models\Student;

class Ma extends Model
{
    use HasFactory;
    protected $table = 'mas';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'uuid',
        'faculty_id',
        'status',
        'actions',
        'mentor_name',
        'mentor_role',
        'created_at',
    ];

    const ENDORSED = 'Endorsed';
    const PENDING = 'Pending';
    const APPROVED = 'Approved';
    const DISAPPROVED = 'Disapproved';
    const RETURNED = 'Returned';

    public function user() {
        return $this->hasMany(User::class, 'sais_id', 'student_sais_id');
    }

    public function student() {
        return $this->hasOne(Student::class, 'sais_id', 'student_sais_id');
    }

    public function mentor_role() {
        return $this->belongsTo(MentorRole::class, 'mentor_role', 'id');
    }

    public function mentor() {
        return $this->hasMany(Mentor::class, 'uuid', 'uuid');
    }

    // public function faculty() {
    //     return $this->hasMany(Faculty::class, 'sais_id', 'mentor_id');
    // }

    // BELOW WAS LINK TO UUID
    public function student_uuid() {
        return $this->belongsTo(Student::class, 'uuid', 'uuid');
    }

    public function faculty() {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }

    public function scopeFilter($query, $filters, $tagProcessor) {
        if($filters->has('nominees')) {
            $query->with(['student_uuid.student_user', 'student_uuid.program_records', 'faculty.uuid', 'mentor_role']);

            
            $query->whereHas('student_uuid.program_records', function($query) use($tagProcessor, $filters) {
                $query = $tagProcessor->process($query, $filters, 'tags');
            });
        }


        // if($filters->has('nominees')) {
        //     $query->with(['student_uuid.student_user', 'student_uuid.program_records' => function($query) {
        //         $query->where('student_program_records.status', '=' , 'ACTIVE');
        //     }]);

            // dd($filters);

            // if($filters->mentor->faculty_id != '') {
            //     $query->where('faculty_id', $filters->mentor->faculty_id);
            // }
            // $query->with(['faculty.uuid' => function($query) use($filters) {
            //     $query->where('uuid', $filters->uuid);
            // }]); 
        // }

        if($filters->has('is_adviser')) {
            if($filters->has('request_mentor')) {
                $query->with(['mentor', 'faculty.uuid', 'mentor_role']);

                // if($filters->mentor != null) {
                    
                // }
                // if($filters->mentor->faculty_id != '') {
                //     $query->where('faculty_id', $filters->mentor->faculty_id);
                // }
            }
        }

        if($filters->has('is_admin')) {
            if($filters->has('table_data')) { //diplay on tables
                $query->with(['student_uuid.student_user', 'student_uuid.program_records', 'faculty.uuid', 'mentor_role']);

                if($filters->mentor != null) {
                    if($filters->mentor->faculty_id != '') {
                        $query->where('faculty_id', $filters->mentor->faculty_id);
                    }
                }

                if($filters->has('uuid')) {
                    $query->where('uuid', $filters->uuid);
                }
            }

            $query->whereHas('student_uuid.program_records', function($query) use($tagProcessor, $filters) {
                $query = $tagProcessor->process($query, $filters, 'tags');
            });
        }

        if($filters->has('with_fullname')) {
            if($filters->with_fullname == true) {
                $query->addSelect(DB::RAW("CONCAT(u.last_name,' ',u.first_name) AS name"))
                       ->leftJoin('users as u', 'u.uuid', '=', 'mas.uuid');
            }
        }

        if($filters->has('with_program')) {
            if($filters->with_program == true) {
                $query->addSelect(DB::RAW("spr.academic_program_id as program, spr.status as student_status"))
                      ->leftJoin('users as usr', 'usr.uuid', '=', 'mas.uuid')
                      ->leftJoin('students as s', 's.uuid', '=', 'usr.uuid')
                      ->leftJoin('student_program_records as spr', 'spr.campus_id', '=', 's.campus_id');
            }
        }

        if($filters->has('with_mentor_name')) {
            if($filters->with_mentor_name == true) {
                $query->addSelect(DB::RAW("CONCAT(usrs.last_name,' ',usrs.first_name) as mentor_name, mr.titles as mentor_role"))
                      ->leftJoin('faculties as f', 'f.faculty_id', '=', 'mas.faculty_id')
                      ->leftJoin('users as usrs', 'usrs.uuid', '=', 'f.uuid')
                      ->leftJoin('mentor_roles as mr', 'mr.id', '=', 'mas.mentor_role');
            }
        }
        

        // select fields
        if($filters->has('fields')) {
            $query->select($filters->fields);
        }

        //  distinct
        // if($filters->has('distinct')) {
        //     $query->select($filters->column_name)->distinct();
        // }
        
        $query = $this->filterData($query, $filters);

    }

    public function filterData($query, $filters) {
        

        //get the active mentor of students 
        if($filters->has('active_mentor')) { 
            $query->with(['mentor', 'mentor.student_uuid.program_records', 'mentor.faculty.uuid', 'mentor.mentor_role']);

            //students uuid; display active mentor
            if($filters->has('uuid')) { 
                $query->where('uuid', $filters->uuid);
            }
        } 

        //display on mentor requests
        if($filters->has('request_mentor')) {
            $query->with(['mentor_role']);
        }


        if($filters->has('name')) {
            if($filters->name != '--') {
                $query->where("uuid", $filters->name);
            }
        }

        if($filters->has('program')) {
            if($filters->program != '--') {
                $query->where("student_program_records.academic_program_id", $filters->program);
            }
        }

        if($filters->has('student_status')) {
            if($filters->student_status != '--') {
                $query->where('student_program_records.status', $filters->student_status);
            }
        }

        if($filters->has('mentor_name')) {
            if($filters->mentor_name != '--') {
                $query->where('mentor_name', $filters->mentor_name);
            }
        }

        if($filters->has('mentor_role')) {
            if($filters->mentor_role != '--') {
                $query->where('mentor_role', $filters->mentor_role);
            }
        }

        if($filters->has('mentor_status')) {
            if($filters->mentor_status != '--') {
                $query->where('mentor_status', $filters->mentor_status);
            }
        }

        return $query;
    }
}
