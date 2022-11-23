<?php

namespace App\Http\Controllers\Student;

use App\Services\MentorAssignmentApproval;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mentor;
use App\Models\User;

class StudentMaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $studentActiveMentor = Mentor::activeMentor($request)->get();
        $studentInfo = User::studentDetails($request)->get();
        return response()->json(
            [
            'stud_info' => $studentInfo,
            'stud_active_mentor' => $studentActiveMentor
            ],200
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, MentorAssignmentApproval $submitRequestedMentor)
    {
        return $submitRequestedMentor->submitRequestedMentor($request);
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
}
