<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorStatus extends Model
{
    use HasFactory;

    protected $table = 'mentor_status';
    public $timestamps = false;
    protected $fillable = [
        'student_sais_id',
        'mentor_id',
        'status',
        'actions',
        'mentor_name',
        'mentor_role'
    ];

    public function mentorss()
    {
        return $this->belongsTo(MaStudent::class, 'sais_id', 'student_sais_id');
    }

}
