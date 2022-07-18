<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdviserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $advisees = DB::table('users As u')
        ->select(DB::raw("CONCAT(u.last_name,' ',u.first_name) AS NAME, spr.academic_program_id AS program, u.sais_id, spr.status"))
        ->distinct()
        ->leftJoin('students AS s', 's.sais_id', '=', 'u.sais_id')
        ->leftJoin('student_program_records AS spr', 'spr.campus_id', '=', 's.campus_id')
        ->leftJoin('mentors AS m', 'm.student_program_record_id', '=', 'spr.student_program_record_id')
        ->leftJoin('faculties AS f', 'f.id', '=', 'm.faculty_id')
        ->where('f.saisid', Auth::user()->saisid)
        ->get();
        return response()->json(
            [
             'advisees' => $advisees,
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
