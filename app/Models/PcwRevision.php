<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PcwRevision extends Model
{
    use HasFactory;

    public function pcwtxns() {
        return $this->morphMany(PcwTxn::class, 'pcwtxnable');
    }
}
