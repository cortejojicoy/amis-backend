<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentProgramRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Program extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $program = DB::table('users As u')
        ->select(DB::raw("CONCAT(u.last_name,' ',u.first_name) AS NAME, spr.academic_program_id AS program, u.saisid, spr.status"))
        ->leftJoin('students AS s', 's.saisid', '=', 'u.saisid')
        ->leftJoin('student_program_records AS spr', 'spr.campus_id', '=', 's.campus_id')
        ->where('u.saisid', Auth::user()->saisid)
        ->first();
        return response()->json(
            [
             'program' => $program,
            ], 200
         );
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
