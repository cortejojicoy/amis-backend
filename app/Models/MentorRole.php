<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorRole extends Model
{
    use HasFactory;

    
    public $timestamps = false;
    protected $table = 'mentor_roles';
}
