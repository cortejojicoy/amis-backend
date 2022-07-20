<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorAssignmentTransaction extends Model
{
    use HasFactory;

    protected $table = 'mentor_assignment_txns';
    protected $primaryKey = 'mas_txn_id';

    protected $fillable = [
        'mas_txn_id',
        'mas_id',
        'action',
        'committed_by',
        'note',
        'created_at',
    ];

    public function scope()
    {
        # code...
    }
}
