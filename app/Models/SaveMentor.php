<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaveMentor extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'mentor_id';
}
