<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorAssignment extends Model
{
    use HasFactory;
    protected $table = 'mentor_assignments';
    protected $primaryKey = 'mas_id';

    protected $fillable = [
        'mas_id',
        'student_sais_id',
        'mentor_id',
        'status',
        'actions',
        'mentor_name',
        'mentor_role',
        'created_at',
    ];

    public function scopeAssignments($query)
    {
        # code...
    }
}
