<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaAdmin extends Model
{
    use HasFactory;

    protected $table = 'mentor_admins';
    public $timestamps = false;

}
