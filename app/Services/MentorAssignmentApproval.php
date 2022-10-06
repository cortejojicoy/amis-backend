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

        $ma = Ma::find($id);
        
        if($request->maType == 'endorse') {
            $status = Ma::ENDORSED;
        } else if($request->maType == 'reject') {
            $status = Ma::REJECTED;
        } else if($request->maType == 'approved') {
            $status = Ma::APPROVED;
        }   

        if($ma) {
            DB::beginTransaction();
            
            // data instance
            $student = Student::where('sais_id', $ma->student_sais_id)->first();
            $student_program_records = $student->program_records()->where('status', 'ACTIVE')->first();
            $facultyId = Faculty::where('sais_id', $ma->mentor_id)->first();
            $mentorId = Mentor::where('student_sais_id', $ma->student_sais_id)->where('faculty_id', $facultyId->faculty_id)->first();

            try {
                $ma->status = $status;
                $ma->save();

                // create entry for transaction history
                $this->insertMaTxn($ma->mas_id, 'Endorsed', $request->remarks);

                if($status == MentorStatus::ENDORSED) {
                    // MaStudent table was on1y for display purposes it will basically update the status based on request
                    MaStudent::where('sais_id', $ma->student_sais_id)->where('mentor_id', $ma->mentor_id)->update(["endorsed" => 1]);
                }

                if($status == MentorStatus::APPROVED && $ma->actions == 'Add') {
                    // if the actions was add it will create an entry on mentors table
                    Mentor::create([
                        "faculty_id" => $facultyId->faculty_id,
                        "student_program_record_id" => $student_program_records->student_program_record_id,
                        "student_sais_id" => $ma->student_sais_id,
                        "mentor_role" => $ma->mentor_role,
                        "field_represented" => '',
                        "status" => 'ACTIVE',
                        "start_date" => Carbon::now(),
                        "end_date" => NULL
                    ]);

                    // MaStudent table was on1y for display purposes it will basically update the status based on request
                    MaStudent::where('sais_id', $ma->student_sais_id)->where('mentor_id', $ma->mentor_id)->update([
                        "approved" => 1, "adviser" => 1
                    ]);

                } else if($status == MentorStatus::APPROVED && $ma->actions == 'Remove') {
                    // if the actions was remove; data will not be deleted but rather update removed column value to 1; to hide.
                    Mentor::where('student_sais_id', $ma->student_sais_id)->where('mentor_id', $mentorId->mentor_id)->update(["removed" => 1]);

                    // MaStudent table was on1y for display purposes it will basically update the status based on request
                    MaStudent::where('sais_id', $ma->student_sais_id)->where('mentor_id', $ma->mentor_id)->update([
                        "approved" => 1, "adviser" => 0 
                    ]);
                }

                DB::commit();
                return response()->json(['message' => 'Successfully submitted'], 200);
            } catch(\Exception $ex) {
                DB::rollback();
                return response()->json(['message' => $ex->getMessage()], 500);
            }
        }

    }

    // public function insertMa($mas_id, $student_sais_id, $mentor_id, $status, $actions, $mentor_name, $mentor_role) {
    //     Ma::create([
    //         "mas_id" => $mas_id,
    //         "student_sais_id" => $student_sais_id,
    //         "mentor_id" => $mentor_id,
    //         "status" => $status,
    //         "actions" => $actions,
    //         "mentor_name" => $mentor_name,
    //         "mentor_role" => $mentor_role,
    //         "created_at" => Carbon::now()
    //     ]);
    // }

    public function insertMaTxn($mas_id, $status, $remarks) {
        MaTxn::create([
            "mas_id" => $mas_id,
            "action" => $status,
            "committed_by" => Auth::user()->sais_id,
            "note" => $remarks ? $remarks : 'None',
            "created_at" => Carbon::now()
        ]);
    }
}
