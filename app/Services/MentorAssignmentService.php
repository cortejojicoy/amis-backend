<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\Mentor;
use App\Models\Ma;
use App\Models\MaTxn;
use App\Models\MentorRole;
use App\Models\SaveMentor;
use App\Models\StudentProgramRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MentorAssignmentService {
    function insertDeleteSaveMentor($request){
        DB::beginTransaction();
        try {
            SaveMentor::where('uuid',$request->uuid)->delete();
            SaveMentor::insert($request->input('data'));
            DB::commit();
            return response()->json(['message' => 'Successfully updated.'], 200);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['message' => $ex->getMessage()], 500);
        }
    }


    function submitRequestedMentor($request, $mas_id) {
        // validate if mentors was already submitted   
        $faculty = Faculty::where('faculty_id', $request['faculty_id'])->first();
        $existRequest = Ma::where('faculty_id', $faculty->faculty_id)->where('uuid', Auth::user()->uuid)->first();
        // instance for mentor table
        $checkMentorExist = Mentor::where('uuid', Auth::user()->uuid)->first();
        // request mentors should active
        $requestMentors = Mentor::where('uuid', Auth::user()->uuid)->where('faculty_id', $request['faculty_id'])->first();

        // count maximum number of major adviser request
        $countMax = Ma::where('uuid', Auth::user()->uuid)->where('faculty_id', $request['faculty_id'])->get()->count();

        // 2 = Major Adviser; 3 = Member
        
        if($existRequest != null) {
            if($existRequest->status === 'Pending') {
                return response()->json([
                    'message' => 'You have already requested '. $request['mentor_name']
                ], 400);
            }

            if($request['mentor_role'] == 2) { //major adviser
                if($countMax > 0) {
                    return response()->json([
                        'message' => 'You have reached maximum requirement for requesting Major Adviser'
                    ], 400);
                }
            }

            if($request['mentor_role'] == 3) {
                if($countMax > 1) {
                    return response()->json([
                        'message' => 'You have reached maximum requirement for requesting Member'
                    ], 400);
                }
            }
        }

        if($requestMentors != null) {
            if($requestMentors->status != 'ACTIVE') {
                return response()->json([
                    'message' => 'You have requested INACTIVE faculty'
                ], 400);
            }
        }

        if($checkMentorExist != '') {
            DB::beginTransaction();
            try {

                // update status; from saved to submitted
                SaveMentor::where('uuid', $request['uuid'])->update(['actions_status' => 'submitted']);
                                                            
                // transaction_id; uuid; faculty_id; status; actions; mentor_name; mentor_role; created_at
                $this->insertMa($mas_id, $request['uuid'], $request['faculty_id'], 'Pending', $request['actions'], $request['mentor_name'], $request['mentor_role']);

                // transasction_id; status, remarks
                $this->insertMaTxn($mas_id, 'Pending', 'None');

                
                DB::commit();
                return response()->json([
                    'message' => 'Successfully submitted',
                    'status' => 'Ok'
                ], 200);
            } catch(\Exception $ex) {
                DB::rollback();
                return response()->json(['message' => $ex->getMessage()], 500);
            }
        } else {
            // if mentors is empty they cannot request
            return response()->json([
                'message' => 'You cannot request a mentor without atleast a temporary adviser'
            ], 400);
        }
    }   


    function updateApproval($request, $id) {

        $ma = Ma::find($id);

        if($request->type == 'endorse') {
            $status = Ma::ENDORSED;
        } else if($request->type == 'returned') {
            $status = Ma::RETURNED;
        } else if($request->type == 'disapproved') {
            $status = Ma::DISAPPROVED;
        } else if($request->type == 'pending') {
            $status = Ma::PENDING;
        } else if($request->type == 'approved') {
            $status = Ma::APPROVED;
        }
        // else if($request->type == 'disapproved') {
        //     $status = Ma::DISAPPROVED;
        // } else if($request->type == 'approved') {
        //     $status = Ma::APPROVED;
        // }   

        if($ma) {

            $student = Student::where('uuid', $ma->uuid)->first();
            $student_program_records = $student->program_records()->where('status', 'ACTIVE')->first();
            $studentMentor = Mentor::where('uuid', $ma->uuid)->where('mentor_role', '=', 1)->first();
            //check mentor id; update status to inactive
            $mentorId = Mentor::find($studentMentor->mentor_id);
            $returnedMentor = SaveMentor::find($ma->mas_id);

            DB::beginTransaction();

            try {
                $ma->status = $status;
                $ma->save();

                if($request->roles === "students") {
                    if($status == Ma::PENDING) {
                        // SaveMentor::where('uuid', $ma->uuid)->update(["actions_status" => "submitted"]);
                        // $ma->mentor_name = $returnedMentor->mentor_name,
                    }

                    if($returnedMentor) {
                        // $returnedMentor->mentor_name = 
                        $returnedMentor->actions_status = 'submitted';
                        $returnedMentor->save();
                    }
                } 
                
                if($request->roles == 'faculties') {
                    if($status == Ma::RETURNED) {
                        SaveMentor::create([
                            "mas_id" => $ma->id,
                            "actions" => $ma->actions,
                            "mentor_name" => $ma->mentor_name, 
                            "mentor_role" => $ma->mentor_role,
                            "uuid" => $ma->uuid,
                            "faculty_id" => $ma->faculty_id,
                            "actions_status" => "saved",
                            "status" => "returned"
                        ]);
                    }
                }


                if($request->roles == 'admins') {
                    if($status == Ma::APPROVED) {
                        Mentor::create([
                            "faculty_id" => $ma->faculty_id,
                            "student_program_record_id" => $student_program_records->student_program_record_id,
                            "uuid" => $ma->uuid,
                            "mentor_role" => $ma->mentor_role,
                            "field_represented" => '',
                            "status" => 'ACTIVE',
                            "start_date" => Carbon::now()
                        ]);
                    }
                    //temporay adviser will be set status to inactive
                    if($mentorId) {
                        $mentorId->status = 'INACTIVE';
                        $mentorId->end_date = Carbon::now();
                        $mentorId->save();
                    }
                }

                // create entry for transaction history
                $this->insertMaTxn($ma->id, $status, $request->remarks);

                DB::commit();
                return response()->json(['message' => 'Successfully committed'], 200);
            } catch(\Exception $ex) {
                DB::rollback();
                return response()->json(['message' => $ex->getMessage()], 500);
            }
        }
    }


    public function insertMa($mas_id, $uuid, $facultyId, $status, $actions, $mentorName, $mentorRole) {
        Ma::create([
            "id" => $mas_id,
            "uuid" => $uuid,
            "faculty_id" => $facultyId,
            "status" => $status,
            "actions" => $actions,
            "mentor_name" => $mentorName,
            "mentor_role" => $mentorRole,
            "created_at" => Carbon::now()
        ]);
    }
    

    public function insertMaTxn($mas_id, $status, $remarks) {
        MaTxn::create([
            "mas_id" => $mas_id,
            "action" => $status,
            "committed_by" => Auth::user()->uuid,
            "note" => $remarks ? $remarks : 'None',
            "created_at" => Carbon::now()
        ]);
    }

}