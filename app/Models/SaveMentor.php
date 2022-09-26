<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaveMentor extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'mentor_id';

    public function faculty()
    {
        # code...
    }

    public function scopeFilter($query, $filters)
    {
        $query->where('sais_id', $filters->sais_id)->where('actions_status', 'saved');
    }
}
