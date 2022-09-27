<?php
namespace App\Services;
use Carbon\Carbon;
use App\Models\SaveMentor;
use App\Models\Faculty;
use App\Models\Mentor;
use App\Models\Student;
use App\Models\MentorStatus;
use App\Models\Ma;
use App\Models\MaTxn;
use App\Models\MaStudent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class SubmitRequestedMentor extends Controller {
    function submitRequestedMentor($request) {
        $mentors = $request->input();
        // var_dump($mentors);
        foreach($mentors as $keys => $data) {
            $student = Student::where('sais_id', Auth::user()->sais_id)->first();
            $student_program_records = $student->program_records()->where('status', 'ACTIVE')->first();

            $programId = Student::programId()->where('students.sais_id', )->first();
            $facultyId = Faculty::where('sais_id', $data['mentor_id'])->first();
            $activeMentor = Mentor::where('student_sais_id', $data['sais_id'])->where('faculty_id', $facultyId->id)->first();
            $mentorExists = Ma::where('mentor_id', $data['mentor_id'])->where('student_sais_id', Auth::user()->sais_id)->first();
            
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

            DB::beginTransaction();
            try {
                foreach($mentors as $keys => $data) {
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

                    MentorStatus::create([
                        "student_sais_id" => $data['sais_id'],
                        "mentor_id" => $data['mentor_id'],
                        "mentor_name" => $data['mentor_name'],
                        "mentor_role" => $data['mentor_role'],
                        "status" => 'Pending',
                        "actions" => $data['actions'],
                    ]);

                    MaStudent::create([
                        "sais_id" => Auth::user()->sais_id,
                        "name" => Auth::user()->last_name . ' ' . Auth::user()->first_name,
                        "program" => $student_program_records->academic_program_id,
                        "acad_group" => $student_program_records->acad_group,
                        "adviser" => $activeMentor ? 1 : 0,
                        "status" => $student_program_records->status,
                        "mentor_id" => $data['mentor_id'],
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
}