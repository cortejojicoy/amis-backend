<?php
namespace App\Services;

use App\Models\Coi;
use App\Models\CoiTxn;
use App\Models\CourseOffering;
use App\Models\ExternalLink;
use App\Models\MailWorker;
use App\Models\StudentTerm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplyConsentOfInstructor{
    function createCoi($request, $coi_id, $external_link_token){
        $student_term = StudentTerm::where('status', 'ACTIVE')->first();

        //check if student has already applied for the same class
        $existingCOI = Coi::where('class_id', $request->class_id)
            ->where('sais_id', Auth::user()->sais_id)
            ->where(function($query) {
                $query->orWhere('status', 'Requested')
                    ->orWhere('status', 'Approved');
            })->where('term', $student_term->term_id)
            ->first();
        
        //if student still hasn't applied for this class, continue
        if(empty($existingCOI)) {
            //check if course_offering has faculty assigned
            $co = CourseOffering::where('class_nbr', $request->class_id)
                ->first()
                ->toArray();

            //if no faculty assigned
            if($co['email'] == '') {
                //return error
                return response()->json(
                    [
                        'message' => 'No Faculty-in-Charge assigned in this class. Contact the unit offering the course so they can enter the FIC in SAIS',
                    ], 400
                );
            } else { // if there is faculty assigned
                //begins transaction but won't commit it to DB first
                DB::beginTransaction();
                try {
                    //Create COI
                    Coi::create([
                        "coi_id" => $coi_id,
                        "class_id" => $request->class_id,
                        "term" => $student_term->term_id,
                        "sais_id" => Auth::user()->sais_id,
                        "status" => "Requested",
                        "comment" => "",
                        "created_at" => now()
                    ]);
                    
                    //Create COI TXN
                    CoiTxn::create([
                        "coi_id" => $coi_id,
                        "action" => "Requested",
                        "committed_by" => Auth::user()->sais_id,
                        "note" => $request->justification ? $request->justification : 'None',
                        "created_at" => now()
                    ]);
                    
                    //create external link
                    ExternalLink::create([
                        "token" => $external_link_token,
                        "model_type" => 'App\Models\Coi',
                        "model_id" => $coi_id
                    ]);

                    //Get user instance
                    $user = User::find(Auth::user()->sais_id);

                    //initialize mail data which will be used in the email template
                    $mailData = [
                        "status" => 'requested', 
                        "token" => $external_link_token,
                        "class" => $co,
                        "student" => [
                            'name' => $user->full_name,
                            'email' => Auth::user()->email,
                            'justification' =>  $request->justification,
                            'campus_id' => $user->student->campus_id
                        ]
                    ];
                    
                    //Create the mailing entry
                    MailWorker::create([
                        "subject" => $co['course'] . ' ' . $co['section'] . ' COI Application',
                        "recipient" => $co['email'],
                        "blade" => 'coi_mail',
                        "data" => json_encode($mailData),
                        "queued_at" => now()
                    ]);

                    //Commit the changes to db if there is no error
                    DB::commit();
                    
                    //return ok
                    return response()->json(
                        [
                            'message' => 'COI successfully requested',
                            'status' => 'Ok'
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
            }
        } else { //if student has already applied for this class
            //return error
            return response()->json(
                [
                    'message' => 'You have already applied COI to this class with status: ' . $existingCOI->status,
                ], 400
            );
        }
    }

    function updateCoi($request, $id) { 
        $coi = Coi::find($id);
        if($request->status == 'approve') {
            $status = 'Approved';
        } else {
            $status = 'Disapproved';
        } 

        if($coi) {
            DB::beginTransaction();

            try {
                $coi->status = $status;
                $coi->save();

                CoiTxn::create([
                    "coi_id" => $coi->coi_id,
                    "action" => $status,
                    "committed_by" => Auth::user()->sais_id,
                    "note" => $request->justification ? $request->justification : "None",
                    "created_at" => now()
                ]);

                ExternalLink::where('model_id', $coi->coi_id)
                    ->where('model_type', 'App\Model\Coi')
                    ->update(['action' => $status]);

                $mailData = [
                    "status" => $status == 'Approved' ? 'approved' : 'disapproved',
                    "reason" => $request->justification,
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

                return response()->json(
                    [
                        'message' => 'COI Successfully ' . $status,
                        'status' => 'Ok'
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
        }
    }
}