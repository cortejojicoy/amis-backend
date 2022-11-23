<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;;
use App\Models\SaveMentor;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\StudentProgramRecord;
use App\Models\MaStudent;
use App\Models\MentorRole;
use App\Services\BulkUpdateSaveMentor;
use App\Http\Requests\BulkUpdateSaveMentorRequest;

class StudentAddMentorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $student = Student::where('sais_id', $request->sais_id)->first();
        $program = StudentProgramRecord::where('campus_id', $student->campus_id)->where('status', 'ACTIVE')->first();
        $request->merge(['program' => $program]);

        $faculties = Faculty::info($request)->get();

        $mentor_role = MentorRole::get();

        $save_mentors =  SaveMentor::filter($request)->get();
        if($request->has('mentor_name')){
            $save_mentors = $save_mentors->where('mentor_name',$request->mentor_name);
        }
        return response()->json(
            [
             'save_mentors' => $save_mentors,
             'faculty_name' => $faculties,
             'm_role' => $mentor_role
            ], 200
         );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return SaveMentor::create($request->all);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function bulkUpdate(BulkUpdateSaveMentorRequest $request, BulkUpdateSaveMentor $bulkUpdateSaveMentor)
    {
        return $bulkUpdateSaveMentor->insertDeleteSaveMentor($request);
    }
}
