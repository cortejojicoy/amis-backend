<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProgramRecord extends Model
{
    use HasFactory;
    protected $primaryKey = 'student_program_record_id';
    protected $keyType = 'string';

    protected $fillable = [
        'campus_id',
        'academic_program_id',
        'acad_group',
        'status',
    ];

    public function curriculum() {
        return $this->hasOne(curriculum::class, 'curriculum_id', 'curriculum_id');
    }
}
