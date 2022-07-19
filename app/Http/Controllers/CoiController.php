<?php

namespace App\Http\Controllers;

use App\Models\Coi;
use App\Models\CoiTxn;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoiController extends Controller
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
        $uniqueId = $this->generateTxnID("COI");

        $student = Student::where('sais_id', Auth::user()->sais_id)->first();
        
        DB::beginTransaction();
        try {
            Coi::create([
                "coi_id" => $uniqueId,
                "class_id" => $request->class_id,
                "student_id" => $student->campus_id,
                "status" => "Requested",
                "comment" => "",
                "created_at" => Carbon::now()
            ]);

            CoiTxn::create([
                "coi_id" => $uniqueId,
                "action" => "Requested",
                "committed_by" => Auth::user()->sais_id,
                "note" => $request->justification,
                "created_at" => Carbon::now()
            ]);

            // update coi txn in student view
            // create function for random alpha num generation

            $randomAlphaNum = $this->generateRandomAlphaNum(50, 1);

            // add external links
            // add emailing
            DB::commit();

            return response()->json(
                [
                    'message' => 'COI successfully requested'
                ], 200
            );

        } catch (\Exception $ex) {
            DB::rollback();

            return response()->json(
                [
                    'message' => $ex->getMessage()
                ], 500
            );
        }
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
