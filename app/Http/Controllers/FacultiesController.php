<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentProgramRecord;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Student;

class FacultiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $student = Student::where('sais_id', $request->sais_id)->first();
        // $student_program = StudentProgramRecord::where('campus_id', $student->campus_id)->first();
        // $request->merge(['program' => $student_program]);

        $ma = Faculty::filter($request)->get();
        return response()->json(
            [
             'ma' => $ma,
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
        //
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
