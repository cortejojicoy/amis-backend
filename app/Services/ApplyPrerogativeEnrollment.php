<?php
namespace App\Services;

use App\Models\CourseOffering;
use App\Models\ExternalLink;
use App\Models\MailWorker;
use App\Models\Prerog;
use App\Models\PrerogTxn;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplyPrerogativeEnrollment{
    function createPrerog($request, $prg_id, $external_link_token){
        //check if student has already applied for the same class
        $existingPrerog = Prerog::where('class_id', $request->class_id)
            ->where('sais_id', Auth::user()->sais_id)
            ->where(function($query) {
                $query->orWhere('status', 'Requested')
                    ->orWhere('status', 'Accepted')
                    ->orWhere('status', 'Approved');
            })
            ->first();
        
        //if student still hasn't applied for this class, continue
        if(empty($existingPrerog)) {
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

                $status = "Requested";
                try {
                    //Create Prerog
                    Prerog::create([
                        "prg_id" => $prg_id,
                        "class_id" => $request->class_id,
                        "sais_id" => Auth::user()->sais_id,
                        "status" => $status,
                        "comment" => "",
                        "created_at" => now()
                    ]);
                    
                    //Create COI TXN
                    PrerogTxn::create([
                        "prg_id" => $prg_id,
                        "action" => $status,
                        "committed_by" => Auth::user()->sais_id,
                        "note" => $request->justification ? $request->justification : 'None',
                        "created_at" => now()
                    ]);
                    
                    //create external link
                    ExternalLink::create([
                        "token" => $external_link_token,
                        "model_type" => 'App\Models\Prerog',
                        "model_id" => $prg_id
                    ]);

                    //Get user instance
                    $user = User::find(Auth::user()->sais_id);

                    //initialize mail data which will be used in the email template
                    $mailData = [
                        "status" => strtoupper($status), 
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
                        "subject" => $co['course'] . ' ' . $co['section'] . ' Prerog Application',
                        "recipient" => $co['email'],
                        "blade" => 'prg_mail',
                        "data" => json_encode($mailData),
                        "queued_at" => now()
                    ]);

                    //Commit the changes to db if there is no error
                    DB::commit();
                    
                    //return ok
                    return response()->json(
                        [
                            'message' => 'Prerog successfully requested',
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
                    'message' => 'You have already applied for a Prerog to this class with status: ' . $existingPrerog->status,
                ], 400
            );
        }
    }

    function updatePrerog($request, $id, $role, $external_link_token = null) {
        $prg = Prerog::find($id);
        if($request->status == 'approve') {
            $status = 'Approved';
        } else if ($request->status == 'accept') {
            $status = 'Accepted';
        } else if ($request->status == 'disapprove' && $role == 'faculties') {
            $status = 'Disapproved by FIC';
        } else {
            $status = 'Disapproved by OCS';
        }

        if($prg) {
            DB::beginTransaction();

            try {
                $prg->status = $status;
                $prg->save();

                PrerogTxn::create([
                    "prg_id" => $prg->prg_id,
                    "action" => $status,
                    "committed_by" => Auth::user()->sais_id,
                    "note" => $request->justification ? $request->justification : "None",
                    "created_at" => now()
                ]);

                //Close the previous external link
                ExternalLink::where('model_id', $prg->prg_id)
                    ->where('model_type', 'App\Models\Prerog')
                    ->where('action', null)
                    ->update(['action' => $status]);

                //If the action of the user is just accept, create another external link
                if($status == 'accept') {
                    //create external link
                    ExternalLink::create([
                        "token" => $external_link_token,
                        "model_type" => 'App\Models\Prerog',
                        "model_id" => $prg->prg_id
                    ]);
                }

                if($status != 'Accepted') { //if the status of the prerog application is approved, disapproved by FIC, or disapproved by OCS, send email to student
                    $mailData = [
                        "status" => strtoupper($status), 
                        "reason" => $request->justification,
                        "class" => $prg->course_offering,
                        "role" => $role
                    ];
                    
                    //Create the mailing entry
                    MailWorker::create([
                        "subject" => $prg->course_offering->course . ' ' . $prg->course_offering->section . ' Prerog Application',
                        "recipient" => $prg->user->email,
                        "blade" => 'prg_mail',
                        "data" => json_encode($mailData),
                        "queued_at" => now()
                    ]);
                } else {
                    // send email to OCS that the email has been accepted by the faculty

                    // $mailData = [
                    //     "status" => strtoupper($status), 
                    //     "class" => $prg->course_offering,
                    //     "token" => $external_link_token,
                    //     "student" => [
                    //          'name' => $prg->user->full_name,
                    //          'email' => $prg->user->email,
                    //          'justification' =>  $request->justification,
                    //          'campus_id' => $prg->student->campus_id
                    //      ]
                    // ];
                    
                    // //Create the mailing entry
                    // MailWorker::create([
                    //     "subject" => $prg->course_offering->course . ' ' . $prg->course_offering->section . ' Prerog Application',
                    //     "recipient" => $prg->user->email,
                    //     "blade" => 'prg_mail',
                    //     "data" => json_encode($mailData),
                    //     "queued_at" => now()
                    // ]);
                }

                //add another for the OCS

                DB::commit();

                return response()->json(
                    [
                        'message' => 'Prerog Successfully ' . $status,
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