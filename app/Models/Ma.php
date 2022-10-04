<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ma extends Model
{
    use HasFactory;
    protected $table = 'mas';
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
}
