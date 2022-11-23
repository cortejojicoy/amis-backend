<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $primaryKey = 'faculty_id';
    // /**
    //  * Get the post that owns the comment.
    // */


    public function courseOfferings() {
        return $this->hasMany(CourseOffering::class, 'id', 'sais_id');
    }

    public function user() {
        return $this->belongsTo(User::class,'sais_id','sais_id');
    }

    public function uuid() {
        return $this->belongsTo(User::class, 'uuid', 'uuid');
    }

    public function mentor() {
        return $this->hasMany(Mentor::class, 'faculty_id', 'faculty_id');
    }

        /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeInfo($query, $filters) {
        if($filters->program->academic_program_id != '') {
            $query->join('users', 'users.uuid', '=', 'faculties.uuid')
                  ->where('program', $filters->program->academic_program_id);
            
        }
    }
}
