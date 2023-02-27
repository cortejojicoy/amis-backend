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
use App\Models\FacultyAppointment;
use App\Models\MentorAssignment;
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


    function submitRequestedMentor($request, $index, $mas_id, $facultyId) {

        $faculty = Faculty::where('faculty_id', $request['faculty_id'])->first();
        //if the user is not existed on mentors table; it will return atleast they have temporary adviser
        $checkMentorExist = Mentor::where('uuid', Auth::user()->uuid)
                                    ->where('status', '=', 'ACTIVE')
                                    ->first();
        //if the user request a mentor; mentor should active
        // $requestMentors = Mentor::where('uuid', Auth::user()->uuid)
        //                         ->where('faculty_id', $faculty->faculty_id)
        //                         ->where('status', '!=', 'ACTIVE')
        //                         ->first();
        //this will get student details
        $studentUser = User::where('uuid', Auth::user()->uuid)->first();

        $student = Student::where('uuid', Auth::user()->uuid)->first();
        $studentProgramRecords = $student->program_records()
                                            ->where('status', '=', 'ACTIVE')
                                            ->first();
        
        $facultyMentor = MentorRole::find($request['mentor_role']);
        
        $errMsg = [];
        if($facultyMentor->id == 2) { //requesting for a mjor adviser
            $role[$index] = $facultyMentor->id;
            $countMax = Ma::where('uuid', Auth::user()->uuid)
                            ->where('mentor_role', $facultyMentor->id)
                            ->get()->count();

            if($countMax > 0 || $countMax + count($role)) {
                $errMsg[] = "You have reached maximum requirement for requesting a Major Adviser";
            }

        } else if($facultyMentor->id == 3) { //requesting for a member
            $role[$index] = $facultyMentor->id;
            $countMax = Ma::where('uuid', Auth::user()->uuid)
                            ->where('mentor_role', $facultyMentor->id)
                            ->get()->count();
            if($countMax > 2 || $countMax + count($role)) {
                $errMsg[] = "You have reached maximum requirement for requesting a Member";
            }
        } 

        //return existing mentor request
        $existRequest = Ma::whereIn('faculty_id', $facultyId)->where('uuid', Auth::user()->uuid)->where('status', '=', 'Requested')->get();
        foreach($existRequest as $name) {
            $errMsg[] = "You have already requested " . $name->mentor_name;
        }

        // if($requestMentors != '') {
        //     $errMsg[] = "You have requested INACTIVE faculty";
        // }

        if($errMsg != '') {
            return response()->json([
                'errors' => array($errMsg)
            ], 422);
        } else {
            if($checkMentorExist != '') {
                DB::beginTransaction();
                try {
                    // update status; from saved to submitted
                    SaveMentor::where('uuid', $request['uuid'])->update(['actions_status' => 'submitted']);

                    //insert to mentors dump data
                    $this->insertMentorAssignment(
                        Auth::user()->uuid,
                        $checkMentorExist->faculty_id, //active mentor faculty_id
                        $checkMentorExist ? '' : $mas_id,
                        $faculty->faculty_id, // request mentor faculty_id
                        $studentProgramRecords->acad_group,
                        "",
                        // student informations
                        $studentUser->last_name ." ". $studentUser->first_name,
                        $studentProgramRecords->academic_program_id,
                        $studentProgramRecords->status,

                        //mentors informations
                        "UNASSIGNED",
                        "UNASSIGNED",
                        "UNASSIGNED"
                    );
                                                                
                    // transaction_id; uuid; faculty_id; status; actions; mentor_name; mentor_role; created_at
                    $this->insertMa($mas_id, $request['uuid'], $request['faculty_id'], 'Requested', $request['actions'], $request['mentor_name'], $request['mentor_role']);

                    // transasction_id; status, remarks
                    $this->insertMaTxn($mas_id, 'Requested', $request['remarks']);

                    
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
        } else if($request->type == 'requested') {
            $status = Ma::REQUESTED;
        }
        // else if($request->type == 'disapproved') {
        //     $status = Ma::DISAPPROVED;
        // } else if($request->type == 'approved') {
        //     $status = Ma::APPROVED;
        // }   

        if($ma) {

            $student = Student::where('uuid', $ma->uuid)->first();
            $student_program_records = $student->program_records()->where('status', 'ACTIVE')->first();
            
            $faculty = Faculty::where('faculty_id', $ma->faculty_id)->first();
            $user = User::where('uuid', $faculty->uuid)->first();
            
            $mentorAssignment = MentorAssignment::where('uuid', $ma->uuid)->first();
            
            //check mentor id; update status to inactive
            $studentMentor = Mentor::where('uuid', $ma->uuid)->where('mentor_role', '=', 1)->first();
            // $mentorId = Mentor::find($studentMentor->mentor_id);

            $mentorAssignmentDumpData = MentorAssignment::find($mentorAssignment->id);
            $saveMentor = SaveMentor::find($ma->mas_id);

            DB::beginTransaction();

            try {
                $ma->status = $status;
                $ma->save();

                if($request->roles === "students") {
                    if($status == Ma::PENDING) {
                        // SaveMentor::where('uuid', $ma->uuid)->update(["actions_status" => "submitted"]);
                        // $ma->mentor_name = $returnedMentor->mentor_name,
                    }

                    if($saveMentor) {
                        // $returnedMentor->mentor_name = 
                        $saveMentor->actions_status = 'submitted';
                        $saveMentor->save();
                    }
                } 
                
                if($request->roles === 'faculties') {
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

                if($request->roles === 'admins') {
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
                    
                    Mentor::where('mentor_id', $studentMentor->mentor_id)->update([
                        'status' => 'INACTIVE',
                        'end_date' => Carbon::now(),
                    ]);

                    // update mentor-assignment dump tables
                    if($mentorAssignmentDumpData) {
                        $mentorAssignmentDumpData->mentor_faculty_id = $ma->faculty_id;
                        $mentorAssignmentDumpData->mentor = $user->last_name. " ".$user->first_name;
                        $mentorAssignmentDumpData->role = $ma->mentor_role;
                        // $mentorAssignmentDumpData->status = $ma->faculty_id;
                        $mentorAssignmentDumpData->save();
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
            "note" => $remarks ? $remarks : "None",
            "created_at" => Carbon::now()
        ]);
    }


    public function insertMentorAssignment($uuid, $mentorId, $masId, $facultyId, $acadGroup, $acadOrg, $name, $program, $studentStatus, $mentor, $role, $status) {
        MentorAssignment::create([
            "uuid" => $uuid,
            "mentor_faculty_id" => $mentorId ? $mentorId : '',
            "mas_id" => $masId,
            "faculty_id" => $facultyId,
            "acad_group" => $acadGroup,
            "acad_org" => $acadOrg,
            "name" => $name,
            "program" => $program,
            "student_status" => $studentStatus,
            "mentor" => $mentor,
            "role" => $role,
            "status" => $status,
            "created_at" => Carbon::now()
        ]);
    }
}