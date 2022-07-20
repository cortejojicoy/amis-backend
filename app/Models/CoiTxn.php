<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoiTxn extends Model
{
    use HasFactory;

    protected $table = 'coitxns';
    protected $primaryKey = 'coi_txn_id';

    protected $fillable = [
        'coi_id',
        'action',
        'committed_by',
        'note',
        'created_at'
    ];

    protected $hidden = [
        'created_at'
    ];

    public function coi()
    {
        return $this->belongsTo(Coi::class);
    }
}
