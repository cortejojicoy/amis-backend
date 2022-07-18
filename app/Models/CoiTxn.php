<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoiTxn extends Model
{
    use HasFactory;

    protected $table = 'coitxns';

    public function coi()
    {
        return $this->belongsTo(Coi::class);
    }
}
