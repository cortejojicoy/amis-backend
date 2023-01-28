<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PcwTxn extends Model
{
    use HasFactory;

    protected $table = 'pcw_txns';
    protected $primaryKey = 'pcw_txn_id';
    protected $keyType = 'string';

    protected $fillable = [
        'pcwtxnable_id',
        'pcwtxnable_type',
        'action',
        'committed_by',
        'note',
        'created_at',
    ];

    public function pcwtxnable()
    {
        return $this->morphTo(__FUNCTION__, 'pcwtxnable_type', 'pcwtxnable_id');
    }
}
