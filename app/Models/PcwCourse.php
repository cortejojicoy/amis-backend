<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PcwCourse extends Model
{
    use HasFactory;

    protected $table = 'pcw_courses';

    protected $fillable = [
        'pcw_id',
        'course_id',
        'course_type',
        'term_id',
        'units',
        'version',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function term()
    {
        return $this->belongsTo(StudentTerm::class, 'term_id', 'term_id');
    }
}
