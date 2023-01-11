<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    use HasFactory;

    public function workflow_steps() {
        return $this->hasMany(WorkflowStep::class, 'workflow_id', 'id');
    }
}
