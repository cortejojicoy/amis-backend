<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasFactory;

    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'id', 'workflow_id');
    }
}
