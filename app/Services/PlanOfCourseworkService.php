<?php
namespace App\Services;

use App\Models\Pcw;
use App\Models\PcwCourse;
use App\Models\PcwTxn;
use App\Models\StudentTerm;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Exists;

/* NOTE THAT THIS IS JUST A PSEUDOCODE TO BE FOLLOWED FOR ALL SERVICES THAT WILL USE WORKFLOWS */
class PlanOfCourseworkService extends GenericService {
    function createPCW($request) {
        DB::beginTransaction();
        try {
            $studentTerm = StudentTerm::where('status', 'ACTIVE')->first();
            $existing_pcw = Pcw::where('sais_id', Auth::user()->sais_id)
                ->where('pcw_type', $request->type)
                ->first();
            
            if($existing_pcw) {
                $pcw_id = $existing_pcw->pcw_id;
            } else {
                $pcw_id = $this->generateTxnID('PCW');
            }
            
            if($request->action == Pcw::DRAFT) {
                if($existing_pcw && $existing_pcw->status == Pcw::DRAFT) {
                    //update PCW updated_at to now
                    $existing_pcw->updated_at = now();
                    $existing_pcw->save();

                    //delete PCW courses
                    PcwCourse::where('pcw_id', $existing_pcw->pcw_id)->delete();

                    //create the new pcw courses
                    $this->createPcwCourses($request->courseworks, $pcw_id, $request->type, 'Original');
                } else { //if there is no existing existing pcw
                    //insert pcw and create pcw courses
                    $this->insertPCW($pcw_id, $request->type, $studentTerm->term_id, Auth::user()->sais_id, Pcw::DRAFT);
                    $this->createPcwCourses($request->courseworks, $pcw_id, $request->type, 'Original');
                }
            } else if($request->action == Pcw::SUBMITTED) {
                if($existing_pcw && $existing_pcw->status == Pcw::DRAFT) {
                    $existing_pcw->status = Pcw::SUBMITTED;
                    $existing_pcw->updated_at = now();
                    $existing_pcw->save();

                    PcwCourse::where('pcw_id', $existing_pcw->pcw_id)->delete();

                    //create the new pcw courses
                    $this->createPcwCourses($request->courseworks, $pcw_id, $request->type, 'Original');

                    //insert pcwtxns
                    $this->insertPCWTxns($pcw_id, 'App\Models\Pcw', Pcw::SUBMITTED, Auth::user()->sais_id, '');
                } else {
                    $this->insertPCW($pcw_id, $request->type, $studentTerm->term_id, Auth::user()->sais_id, Pcw::SUBMITTED);
                    $this->createPcwCourses($request->courseworks, $pcw_id, $request->type, 'Original');
                    
                    //insert pcwtxns
                    $this->insertPCWTxns($pcw_id, 'App\Models\Pcw', Pcw::SUBMITTED, Auth::user()->sais_id, '');
                }
            }

            DB::commit();
                    
            //return ok
            return response()->json(
                [
                    'message' => 'Plan of Coursework Saved',
                    'status' => 'Ok',
                    ''
                ], 200
            );
        } catch (\Exception $ex) {
            //if there is an error, rollback to previous state of db before beginTransaction
            DB::rollback();

            //return error
            return response()->json(
                [
                    'message' => $ex->getMessage()
                ], 500
            );
        }
        //get the pre-determined workflow for this user's POS.

        //assign that workflow to the user's POS entry
        //$this->insertPOS($pos_id, $studentTerm->term_id, Auth::user()->sais_id, POS::APPLIED); 

        //$this->insertPOSTxns($pos_id, $action, Auth::user()->sais_id, $request->justification);
    }

    function updatePOS($request, $id, $role, $permission, $workflowService) {
        /*
            get the workflow and current_step being followed by this POS
        */
        //$pos = POS::find($id);

        // if($request->action == 'approve' && $role == 'faculties') {
        //     $action = POS::APPROVED_FIC;
        // } else if ($request->action == 'approve' && $role == 'admins') {
        //     $action = POS::APPROVED_OCS;
        // } else if ($request->action == 'disapprove' && $role == 'faculties') {
        //     $action = POS::DISAPPROVED_FIC;
        // } else if ($request->action == 'cancel' && $role == 'students') {
        //     $action = POS::CANCELLED;
        // } else {
        //     $action = POS::DISAPPROVED_OCS;
        // }

        //$hasAccess = $workflowService->hasAccess($role, $permission, $pos->current_step, $request->action, $pos->workflow_id);

        //if($hasAccess) {
        //  $this->insertPOSTxns($pos->pos_id, $action, Auth::user()->sais_id, $request->justification);
        //}
        
        //$current_steps = $workflowService->getCurrentSteps($pos->current_step, $request->action, $pos->workflow_id);

        /*
            check if the number of returned steps matches that of the one recorded in the plan of study transaction history
        */
        //$isSame = $this->checkSteps($current_steps, $id);

        //if($isSame) {
        //  $pos->status = $request->action;
        //  $pos->save();
        //}
    }

    function checkSteps($current_steps, $id) {
        /*
            get txn history of the pos
        */

        // $txn_history = POSTXN::where('pos_id', $id)
        //    ->get();

        /* 
            - check if txn_history contains the status of each step
        */

        // foreach($current_steps as $step) {
        //  if(!$txn_history->contains('action', $step->resulting_status)) {
        //      return false
        //  }
        // }

        //return true
    }

    function hasAccess($request, $pcw_id, $tagProcessor) {
        $pcws = Pcw::filter($request, $tagProcessor)->get();

        $exists = $pcws->contains('pcw_id', $pcw_id);

        if($exists) {
            return true;
        } else {
            return false;
        }
    }

    public function insertPCWTxns($pcwID, $pcwType, $action, $saisID, $note) {
        PcwTxn::create([
            "pcwtxnable_id" => $pcwID,
            "pcwtxnable_type" => $pcwType,
            "action" => $action,
            "committed_by" => $saisID,
            "note" => $note ? $note : 'None',
            "created_at" => now()
        ]);
    }

    public function insertPCW($pcwID, $type, $term_id, $saisID, $status) {
        Pcw::create([
            "pcw_id" => $pcwID,
            "pcw_type" => $type,
            "term_id" => $term_id,
            "sais_id" => $saisID,
            "status" => $status,
            "comment" => "",
            "created_at" => now()
        ]);
    }

    public function insertPcwCourses($pcw_id, $course, $type, $term_id, $version) {
        PcwCourse::create([
            "pcw_id" => $pcw_id,
            "course_id" => $course['course_code'],
            "course_type" => $type,
            "units" => $course['units'],
            "term_id" => $term_id,
            "version" => $version
        ]);
    }

    public function createPcwCourses($courses, $pcw_id, $type, $version, ) {
        foreach($courses as $course) {
            $term_id = $this->getTerm($course['year'], $course['sem']);

            $this->insertPcwCourses($pcw_id, $course, $type, $term_id, $version);
        }
    }
}