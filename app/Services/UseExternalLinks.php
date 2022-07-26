<?php
namespace App\Services;

use App\Models\Coi;
use App\Models\CoiTxn;
use App\Models\MailWorker;
use Illuminate\Support\Facades\DB;

class UseExternalLinks {
    function updateCoi($action, $ex_link){
        $coi = Coi::find($ex_link->model_id);

        if($action == 'approve') {
            DB::beginTransaction();

            $coi->status = 'Approved';
            $coi->save();

            // //create coi transaction entry
            CoiTxn::create([
                "coi_id" => $ex_link->model_id,
                "action" => "Approved",
                "committed_by" => $coi->course_offering->id,
                "note" => "None",
                "created_at" => now()
            ]);

            $ex_link->action = "approve";
            $ex_link->save();

            $mailData = [
                "status" => 'approved', 
                "class" => $coi->course_offering,
            ];
            
            //Create the mailing entry
            MailWorker::create([
                "subject" => $coi->course_offering->course . ' ' . $coi->course_offering->section . ' COI Application',
                "recipient" => $coi->user->email,
                "blade" => 'coi_mail',
                "data" => json_encode($mailData),
                "queued_at" => now()
            ]);

            DB::commit();

            return view('external-link', [
                "message" => "COI Successfully Approved!",
                "subMessage" => "You may now exit this tab. Thank you!"
            ]);
        } else if ($action == 'disapprove') {
            return view('external-link', [
                "message" => "Action not recognized!",
                "subMessage" => "If you want to disapprove a COI, kindly login to AMIS, Select Faculty Portal > Consent of Instructor in the left menu. Thank you!"
            ]);
        }
    }
}