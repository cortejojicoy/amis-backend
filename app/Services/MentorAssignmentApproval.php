<?php
namespace App\Services;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Ma;
use App\Models\User;
use App\Models\MaTxn;
use App\Models\Student;
use App\Models\MaStudent;
use App\Models\Faculty;
use App\Models\Mentor;
use App\Models\MentorStatus;
use App\Models\MentorRole;
use App\Models\StudentProgramRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MentorAssignmentApproval extends Controller {
    function submitRequestedMentor($request) {
        $student = Student::where('sais_id', Auth::user()->sais_id)->first();
        $student_program_records = $student->program_records()->where('status', 'ACTIVE')->first();
        // // $programId = Student::programId()->where('students.sais_id', )->first();
        // $activeMentor = $facultyId->mentor()->where('student_sais_id', $data['sais_id'])->first();
        
        // $facultyId->mentor()->where('student_sais_id', $data['sais_id'])->first();
        // $mentorExists = Ma::where('mentor_id', $data['mentor_id'])->where('student_sais_id', Auth::user()->sais_id)->first();
        // $arr = false;
        // if(!empty($mentorExists)) {
        //     return response()->json([
        //         'message' => 'You already have existing request',
        //         'status' => 'Error'
        //     ], 200);
        //     $arr = true;
        // }
        
        // if($activeMentor != NULL) {
        //     return response()->json([
        //         'message' => 'You cannot request your active mentors',
        //         'status' => 'Error'
        //     ], 200);
        //     $arr = true;
        // }
        
        $mentors = $request->input();

        DB::beginTransaction();
        try {
            foreach($mentors as $keys => $data) {
                $facultyId = Faculty::where('sais_id', $data['mentor_id'])->first();
                $activeMentor = Mentor::where('student_sais_id', $data['sais_id'])->first();

                $uniqueId[$keys] = $this->generateTxnID("MAS");
                // Create Mentor Assignments
                Ma::create([
                    "mas_id" => $uniqueId[$keys],
                    "student_sais_id" => $data['sais_id'],
                    "mentor_id" => $data['mentor_id'],
                    "status" => 'Pending',
                    "actions" => $data['actions'],
                    "mentor_name" => $data['mentor_name'],
                    "mentor_role" => $data['mentor_role'],
                    "created_at" => Carbon::now()
                ]);
                // Create Mentors Transaction History
                MaTxn::create([
                    "mas_id" => $uniqueId[$keys],
                    "action" => 'Pending',
                    "committed_by" => Auth::user()->sais_id,
                    "note" => '',
                    "created_at" => Carbon::now()
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Successfully submitted'], 200);
        } catch(\Exception $ex) {
            DB::rollback();
            return response()->json(['message' => $ex->getMessage()], 500);
        }
    }

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
                $this->insertMaTxn($ma->mas_id, $status, $request->remarks);

                if($status == Ma::ENDORSED) {
                    // MaStudent table was on1y for display purposes it will basically update the status based on request
                    MaStudent::where('sais_id', $ma->student_sais_id)->where('mentor_id', $ma->mentor_id)->update(["endorsed" => 1]);
                }
                
                if($status == Ma::REJECTED && $ma->actions == 'Add') {
                    MaStudent::where('sais_id', $ma->student_sais_id)->where('mentor_id', $ma->mentor_id)->update(["endorsed" => 1]);
                } else if($status == Ma::REJECTED && $ma->actions == 'Add') {
                    MaStudent::where('sais_id', $ma->student_sais_id)->where('mentor_id', $ma->mentor_id)->update(["approved" => 1, "adviser" => 0 ]);
                }

                if($status == Ma::APPROVED && $ma->actions == 'Add') {
                    // if the actions was add it will create an entry on mentors table
                    $mentor_role = MentorRole::where('titles', $ma->mentor_role)->first();
                    Mentor::create([
                        "faculty_id" => $facultyId->faculty_id,
                        "student_program_record_id" => $student_program_records->student_program_record_id,
                        "student_sais_id" => $ma->student_sais_id,
                        "mentor_role" => $mentor_role->id,
                        "field_represented" => '',
                        "status" => 'ACTIVE',
                        "start_date" => Carbon::now(),
                        "end_date" => NULL
                    ]);

                    // MaStudent table was on1y for display purposes it will basically update the status based on request
                    MaStudent::where('sais_id', $ma->student_sais_id)->where('mentor_id', $ma->mentor_id)->update([
                        "approved" => 1, "adviser" => 1
                    ]);

                } else if($status == Ma::APPROVED && $ma->actions == 'Remove') {
                    // if the actions was remove; data will not be deleted but rather update removed column value to 1; to hide.
                    
                    Mentor::where('student_sais_id', $ma->student_sais_id)->where('mentor_id', $mentorId->mentor_id)->delete();

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
