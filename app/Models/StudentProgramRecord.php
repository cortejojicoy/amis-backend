<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProgramRecord extends Model
{
    use HasFactory;

    public function curriculum() {
        return $this->hasOne(curriculum::class, 'curriculum_id', 'curriculum_id');
    }
}
