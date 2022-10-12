<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaveMentor extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'mentor_id';

    public function Faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'mentor_id');
    }

    public function scopeFilter($query, $filters)
    {
        $query->where('sais_id', $filters->sais_id)->where('actions_status', 'saved');

        // if($filters->has('sais_id')) {
        //     $query->with('faculty', function($query) use($filters) {
        //         $query->where()
        //     })
        // }
    }
}
