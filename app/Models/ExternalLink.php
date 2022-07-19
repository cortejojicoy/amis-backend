<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalLink extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'token',
        'model_type',
        'model_id',
        'action'
    ];
}
