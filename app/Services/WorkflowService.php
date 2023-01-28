<?php
namespace App\Services;

use App\Models\Tag;
use App\Models\User;
use App\Models\UserPermissionTag;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class WorkflowService {
    function getCurrentSteps($current_step_id, $action, $workflow_id){
        $current_workflow_steps = WorkflowStep::where('workflow_id', $workflow_id)
            ->where('step', $current_step_id)
            ->where('action', $action)
            ->get();

        return $current_workflow_steps;
    }

    function getNextStep($current_step_id, $action, $permission, $workflow_id) {
        $current_workflow = WorkflowStep::where('workflow_id', $workflow_id)
            ->where('step', $current_step_id)
            ->where('action', $action)
            ->where('permission', $permission)
            ->first();

        $next_workflow = WorkflowStep::where('workflow_id', $workflow_id)
            ->where('step', $current_workflow->next_step)
            ->get();

        return $next_workflow;
    }

    function hasAccess() {
        
    }
}