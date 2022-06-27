<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTerm extends Model
{
    use HasFactory;
    protected $primaryKey = 'term_id';
    protected $table = 'student_ay_terms';

    public function terms()
    {
        return $this->belongsTo(StudentGrades::class);
    }
}
