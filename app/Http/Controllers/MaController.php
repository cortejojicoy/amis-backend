<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\MentorAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\MentorAssignmentTransaction;
use App\Http\Requests\MentorAssignmentPostRequest;

class MaController extends Controller
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
        $mentors = $request->input();
        foreach($mentors as $keys => $data) {
            // check if student already submitted mentor requests with existing mentors
            $masExists = MentorAssignment::where('mentor_id', $data['mentor_id'])
                ->where('student_sais_id', $data['sais_id'])
                ->where(function($query){
                    $query->orWhere('status', 'Pending')
                          ->orWhere('status', 'Approved');
                }) ->first();

            if(empty($masExists)) {
                //begins transaction but won't commit it to DB first
                DB::beginTransaction();
                try {
                    foreach($mentors as $keys => $data) {
                        $uniqueId[$keys] = $this->generateTxnID("TRX");
                        // Create Mentor Assignments
                        MentorAssignment::create([
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
                        MentorAssignmentTransaction::create([
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
            } else {
                return response()->json([
                    'message' => 'You already have existing mentors requests with status: ' . $masExists->status,
                    'status' => 'Error'
                ], 200);
            }
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
