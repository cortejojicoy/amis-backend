<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailWorker extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'subject',
        'recipient',
        'blade',
        'data',
        'queued_at',
        'sent_at'
    ];
}
