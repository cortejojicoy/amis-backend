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
    function createCoi($request, $coiID, $externalLinkToken){
        $studentTerm = StudentTerm::where('status', 'ACTIVE')->first();

        //check if student has already applied for the same class
        $existingCOI = Coi::where('class_id', $request->class_id)
            ->where('sais_id', Auth::user()->sais_id)
            ->where(function($query) {
                $query->orWhere('status', Coi::REQUESTED)
                    ->orWhere('status', Coi::APPROVED);
            })->where('term', $studentTerm->term_id)
            ->first();
        
        //if student still hasn't applied for this class, continue
        if(empty($existingCOI)) {
            //check if course_offering has faculty assigned
            $co = CourseOffering::where('class_nbr', $request->class_id)
                ->where('term', $studentTerm->term_id)
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
                    $this->insertCoi($coiID, $request->class_id, $studentTerm->term_id, Auth::user()->sais_id, Coi::REQUESTED);
                    
                    //Create COI TXN
                    $this->insertCoiTxn($coiID, Coi::REQUESTED, Auth::user()->sais_id, $request->justification);
                    
                    //create external link
                    ExternalLink::create([
                        "token" => $externalLinkToken,
                        "model_type" => 'App\Models\Coi',
                        "model_id" => $coiID
                    ]);

                    //Get user instance
                    $user = User::find(Auth::user()->sais_id);

                    //initialize mail data which will be used in the email template
                    $mailData = [
                        "status" => 'requested', 
                        "token" => $externalLinkToken,
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
            $status = Coi::APPROVED;
        } else {
            $status = Coi::DISAPPROVED;
        } 

        if($coi) {
            DB::beginTransaction();

            try {
                $coi->status = $status;
                $coi->save();

                $this->insertCoiTxn($coi->coi_id, $status, Auth::user()->sais_id, $request->justification);

                ExternalLink::where('model_id', $coi->coi_id)
                    ->where('model_type', 'App\Model\Coi')
                    ->update(['action' => $status]);

                $mailData = [
                    "status" => $status == Coi::APPROVED ? 'approved' : 'disapproved',
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

    public function insertCoi($coiID, $classID, $term, $saisID, $status) {
        Coi::create([
            "coi_id" => $coiID,
            "class_id" => $classID,
            "term" => $term,
            "sais_id" => $saisID,
            "status" => $status,
            "comment" => "",
            "created_at" => now()
        ]);
    }

    public function insertCoiTxn($coiID, $action, $saisID, $note) {
        CoiTxn::create([
            "coi_id" => $coiID,
            "action" => $action,
            "committed_by" => $saisID,
            "note" => $note ? $note : 'None',
            "created_at" => now()
        ]);
    }
}