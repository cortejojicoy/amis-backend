<?php
namespace App\Services;

use App\Models\Admin;
use App\Models\Coi;
use App\Models\CoiTxn;
use App\Models\ExternalLink;
use App\Models\MailWorker;
use App\Models\Prerog;
use App\Models\PrerogTxn;
use App\Models\Student;
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

    function updatePrerog($action, $ex_link, $external_link_token = null) {
        $prg = Prerog::find($ex_link->model_id);

        if($action == 'approved_by_fic') {
            $status = Prerog::APPROVED_FIC;
        } else {
            return view('external-link', [
                "message" => "Action not recognized!",
                "subMessage" => "If you want to disapprove a Prerog, kindly login to AMIS, Select Faculty/Admin Portal > Prerogative Enrollment in the left menu. Thank you!"
            ]);
        }

        if($prg) {
            DB::beginTransaction();

            $student = Student::where('sais_id', $prg->sais_id)->first();
            $student_program_records = $student->program_records()->where('status', 'ACTIVE')->first();
            $admin = Admin::where('college', $student_program_records->acad_group)->first();

            try {
                $prg->status = $status;
                $prg->save();

                PrerogTxn::create([
                    "prg_id" => $prg->prg_id,
                    "action" => $status,
                    "committed_by" => $prg->course_offering->id,
                    "note" => "None",
                    "created_at" => now()
                ]);

                //Close the previous external link
                $ex_link->action = $status;
                $ex_link->save();

                //send email to user that his/her prerog application has been approved
                $mailData = [
                    "status" => $status, 
                    "reason" => "None",
                    "class" => $prg->course_offering,
                    "role" => "faculties"
                ];
                
                //Create the mailing entry
                MailWorker::create([
                    "subject" => $prg->course_offering->course . ' ' . $prg->course_offering->section . ' Prerog Application',
                    "recipient" => $prg->user->email,
                    "blade" => 'prg_mail',
                    "data" => json_encode($mailData),
                    "queued_at" => now()
                ]);

                DB::commit();

                return view('external-link', [
                    "message" => "Prerog Successfully Approved!",
                    "subMessage" => "You may now exit this tab. Thank you!"
                ]);
            } catch (\Exception $ex) {
                //if there is an error, rollback to previous state of db before beginTransaction
                DB::rollback();
    
                //return error
                return view('external-link', [
                    "message" => "Prerog Action Error!",
                    "subMessage" => "Please contact the administrator of the system. Thank you!"
                ]);
            }
        }
    }
}