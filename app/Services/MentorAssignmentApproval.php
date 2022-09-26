<?php
namespace App\Services;
use Carbon\Carbon;
use App\Models\Ma;
use App\Models\User;
use App\Models\MaTxn;
use App\Models\Student;
use App\Models\MaStudent;
use App\Models\Faculty;
use App\Models\Mentor;
use App\Models\MentorStatus;
use App\Models\StudentProgramRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MentorAssignmentApproval {
    function updateApproval($request, $id, $roles, $mas_id) {

        $ma = MentorStatus::find($id);

        // dd($ma->mentor_id);
        // if($request->maType == 'endorse' && $roles == 'admins') {
        //     $status = MentorStatus::ENDORSED;
        // } else if($request->maType == 'reject' && $roles == 'admins' || $roles == 'faculties') {
        //     $status = MentorStatus::DISAPPROVED;
        // } else if($request->maType == 'approved') {
        //     $status = MentorStatus::APPROVED;
        // } else {
        //     echo "test";
        // }

        if($request->maType == 'endorse') {
            $status = MentorStatus::ENDORSED;
        } else if($request->maType == 'reject') {
            $status = MentorStatus::REJECTED;
        } else if($request->maType == 'approved') {
            $status = MentorStatus::APPROVED;
        }   

        if($ma) {
            DB::beginTransaction();

            // data instance
            $student = Student::where('sais_id', $ma->student_sais_id)->first();
            $student_program_records = $student->program_records()->where('status', 'ACTIVE')->first();
            $facultyId = Faculty::where('sais_id', $ma->mentor_id)->first();
            $mentorId = Mentor::where('student_sais_id', $ma->student_sais_id)->where('faculty_id', $facultyId->id)->first();

            try {
                $ma->status = $status;
                $ma->save();

                Ma::create([
                    "mas_id" => $mas_id,
                    "student_sais_id" => $ma->student_sais_id,
                    "mentor_id" => $ma->mentor_id,
                    "status" => $status,
                    "actions" => $ma->actions,
                    "mentor_name" => $ma->mentor_name,
                    "mentor_role" => $ma->mentor_role,
                    "created_at" => Carbon::now()
                ]);
                // Create Mentors Transaction History
                MaTxn::create([
                    "mas_id" => $mas_id,
                    "action" => $status,
                    "committed_by" => Auth::user()->sais_id,
                    "note" => $request->remarks,
                    "created_at" => Carbon::now()
                ]);

                if($status == MentorStatus::ENDORSED) {
                    // MaStudent table was on1y for display purposes it will basically update the status based on request
                    MaStudent::where('sais_id', $ma->student_sais_id)->where('mentor_id', $ma->mentor_id)->update(["endorsed" => 1]);
                }

                if($status == MentorStatus::APPROVED && $status->actions == 'Add') {
                    // if the actions was add it will create an entry on mentors table
                    Mentor::create([
                        "faculty_id" => $facultyId->id,
                        "student_program_record_id" => $student_program_records->student_program_record_id,
                        "student_sais_id" => $ma->student_sais_id,
                        "mentor_role" => $ma->mentor_role,
                        "field_represented" => '',
                        "status" => 'ACTIVE',
                        "start_date" => Carbon::now(),
                        "end_date" => NULL
                    ]);

                    // MaStudent table was on1y for display purposes it will basically update the status based on request
                    MaStudent::where('sais_id', $ma->student_sais_id)->where('mentor_id', $ma->mentor_id)->update(["approved" => 1]);

                } else if($status == MentorStatus::APPROVED && $status->actions == 'Remove') {
                    // if the actions was remove data will not be deleted but rather update remove value to 1 to hide.
                    Mentor::where('student_sais_id', $ma->student_sais_id)->where('mentor_id', $mentorId->mentor_id)->update(["removed" => 1]);

                    // MaStudent table was on1y for display purposes it will basically update the status based on request
                    MaStudent::where('sais_id', $ma->student_sais_id)->where('mentor_id', $ma->mentor_id)->update(["approved" => 1]);
                }

                DB::commit();
                return response()->json(['message' => 'Successfully submitted'], 200);
            } catch(\Exception $ex) {
                DB::rollback();
                return response()->json(['message' => $ex->getMessage()], 500);
            }
        }

        

        // dd($request->data);
        // if($ma) {
        //     
        // }

    }
}
