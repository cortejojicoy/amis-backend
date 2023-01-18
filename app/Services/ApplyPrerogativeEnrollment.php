<?php
namespace App\Services;

use App\Models\Admin;
use App\Models\CourseOffering;
use App\Models\ExternalLink;
use App\Models\MailWorker;
use App\Models\Prerog;
use App\Models\PrerogTxn;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplyPrerogativeEnrollment{
    function createPrerog($request, $prgID, $externalLinkToken){
        $studentTerm = StudentTerm::where('status', 'ACTIVE')->first();

        $withoutApproval = ['CAS', 'CAFS', 'CEM', 'CEAT', 'GS', 'CVM', 'CDC', 'CFNR', 'CHE', 'SESAM', 'CACAS'];

        //check if student has already applied for the same class
        $toBeAppliedTo = CourseOffering::where('class_nbr', $request->class_id)
            ->where('term', $studentTerm->term_id)
            ->first();

        //get course_offerings with the same course and component
        $conflictingCourses = CourseOffering::select('class_nbr')
            ->where('course', $toBeAppliedTo->course)
            ->where('component', $toBeAppliedTo->component)
            ->where('term', $studentTerm->term_id)
            ->get()->pluck('class_nbr');

        //if the student has applied to one of those courses with statsus requested, approved_ocs, logged_ocs, then don't let the user apply.
        $existingPrerog = Prerog::whereIn('class_id', $conflictingCourses)
            ->where('sais_id', Auth::user()->sais_id)
            ->whereIn('status', [Prerog::REQUESTED, Prerog::APPROVED_OCS, Prerog::LOGGED_OCS])
            ->where('term', $studentTerm->term_id)
            ->first();
        
        $existingPrerogWithExactClassNbr = Prerog::where('class_id', $request->class_id)
            ->where('sais_id', Auth::user()->sais_id)
            ->where('status', Prerog::APPROVED_FIC)
            ->where('term', $studentTerm->term_id)
            ->first();
        
        //if the student has applied to this same course with approved status. Forbid student to apply again
        if(!empty($existingPrerogWithExactClassNbr)) {
            return response()->json(
                [
                    'message' => 'You already have an approved prerog application in this class. You do not have to apply again.',
                ], 400
            );
        }
        
        //if student still hasn't applied for this class, continue
        if(empty($existingPrerog)) {
            //check if course_offering has faculty assigned
            $co = CourseOffering::where('class_nbr', $request->class_id)
                ->where('term', $studentTerm->term_id)
                ->first()
                ->toArray();

            //if faculty assigned
            if($co['email'] != '') {
                
                //Get user instance
                $user = User::find(Auth::user()->sais_id);

                $programRecord = $user->student->program_records()->where('status', 'ACTIVE')->first();

                if(empty($programRecord)) {
                    return response()->json(
                        [
                            'message' => 'It seems that you do not have an existing/active degree program in our database, kindly contact the AMIS team to add your degree program.',
                        ], 400
                    );
                }

                //begins transaction but won't commit it to DB first
                DB::beginTransaction();

                try {

                    //if needs to be approved by OCS
                    if(!in_array($programRecord->acad_group, $withoutApproval)) {
                        //Create Prerog
                        $this->insertPrerog($prgID, $request->class_id, $studentTerm->term_id, Auth::user()->sais_id, Prerog::REQUESTED);

                        //Create COI TXN
                        $this->insertPrerogTxns($prgID, Prerog::REQUESTED, Auth::user()->sais_id, $request->justification);
                    } else {
                        //Create Prerog
                        $this->insertPrerog($prgID, $request->class_id, $studentTerm->term_id, Auth::user()->sais_id, Prerog::LOGGED_OCS);

                        //Create COI TXN
                        $this->insertPrerogTxns($prgID, Prerog::REQUESTED, Auth::user()->sais_id, $request->justification);

                        $this->insertPrerogTxns($prgID, Prerog::LOGGED_OCS, Auth::user()->sais_id, $request->justification);

                        //create external link
                        ExternalLink::create([
                            "token" => $externalLinkToken,
                            "model_type" => 'App\Models\Prerog',
                            "model_id" => $prgID
                        ]);

                        //initialize mail data which will be used in the email template
                        $mailData = [
                            "status" => Prerog::LOGGED_OCS, 
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
                            "subject" => $co['course'] . ' ' . $co['section'] . ' Prerog Application',
                            "recipient" => $co['email'],
                            "blade" => 'prg_mail',
                            "data" => json_encode($mailData),
                            "queued_at" => now()
                        ]);
                    }

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
            } else { // if there is no faculty assigned
                //return error
                return response()->json(
                    [
                        'message' => 'No Faculty-in-Charge assigned in this class. Contact the unit offering the course so they can enter the FIC in SAIS',
                    ], 400
                );
            }
        } else { //if student has already applied for this class
            //return error
            return response()->json(
                [
                    'message' => 'You have already applied for this class of the same course code with status: ' . $existingPrerog->status,
                ], 400
            );
        }
    }

    function updatePrerog($request, $id, $role, $externalLinkToken = null) {
        $prg = Prerog::find($id);

        if($request->status == 'approve' && $role == 'faculties') {
            $status = Prerog::APPROVED_FIC;
        } else if ($request->status == 'approve' && $role == 'admins') {
            $status = Prerog::APPROVED_OCS;
        } else if ($request->status == 'disapprove' && $role == 'faculties') {
            $status = Prerog::DISAPPROVED_FIC;
        } else if ($request->status == 'cancel' && $role == 'students') {
            $status = Prerog::CANCELLED;
        } else {
            $status = Prerog::DISAPPROVED_OCS;
        }

        if($prg) {
            DB::beginTransaction();

            //find the admin of the student's degree
            $student = Student::where('sais_id', $prg->sais_id)->first(); //get student
            $studentProgramRecord = $student->program_records()->where('status', 'ACTIVE')->first(); //getthe active degree of the student
            $admin = Admin::where('college', $studentProgramRecord->acad_group)->first(); //get the admin of the college of the student

            try {
                $prg->status = $status;
                $prg->save();

                $this->insertPrerogTxns($prg->prg_id, $status, Auth::user()->sais_id, $request->justification);

                //Close the previous external link
                ExternalLink::where('model_id', $prg->prg_id)
                    ->where('model_type', 'App\Models\Prerog')
                    ->where('action', null)
                    ->update(['action' => $status]);

                if($status != Prerog::CANCELLED) {
                    if($status != Prerog::APPROVED_OCS) { //if the status of the prerog application is approved, disapproved by FIC, or disapproved by OCS, send email to student
                        $mailData = [
                            "status" => $status, 
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
                    } else { // send email to faculty that the email has been approved by the OCS
                        //get the prerogtxn of student with his/her appeal
                        $prgtxn = $prg->prerog_txns()->where('action', Prerog::REQUESTED)->first();
    
                        $mailData = [
                            "status" => strtoupper($status), 
                            "class" => $prg->course_offering,
                            "token" => $externalLinkToken,
                            "student" => [
                                 'name' => $prg->user->full_name,
                                 'email' => $prg->user->email,
                                 'justification' =>  $prgtxn->note,
                                 'campus_id' => $prg->student->campus_id
                             ]
                        ];
                        
                        //Create the mailing entry
                        MailWorker::create([
                            "subject" => $prg->course_offering->course . ' ' . $prg->course_offering->section . ' Prerog Application',
                            "recipient" => $prg->course_offering->email,
                            "blade" => 'prg_mail',
                            "data" => json_encode($mailData),
                            "queued_at" => now()
                        ]);
                    }
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

    public function insertPrerog($prgID, $classID, $term, $saisID, $status) {
        Prerog::create([
            "prg_id" => $prgID,
            "class_id" => $classID,
            "term" => $term,
            "sais_id" => $saisID,
            "status" => $status,
            "comment" => "",
            "created_at" => now()
        ]);
    }

    public function insertPrerogTxns($prgID, $status, $saisID, $note) {
        PrerogTxn::create([
            "prg_id" => $prgID,
            "action" => $status,
            "committed_by" => $saisID,
            "note" => $note ? $note : 'None',
            "created_at" => now()
        ]);
    }
}