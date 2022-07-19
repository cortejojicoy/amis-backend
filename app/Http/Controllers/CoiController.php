<?php

namespace App\Http\Controllers;

use App\Models\Coi;
use App\Models\CoiTxn;
use App\Models\CourseOffering;
use App\Models\ExternalLink;
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
        //generate transaction ID
        $uniqueId = $this->generateTxnID("COI");

        //get student instance
        $student = Student::where('sais_id', Auth::user()->sais_id)->first();

        //check if student has already applied for the same class
        $existingCOI = Coi::where('class_id', $request->class_id)
            ->where('student_id', $student->campus_id)
            ->where(function($query) {
                $query->orWhere('status', 'Requested')
                    ->orWhere('status', 'Approved');
            })
            ->first();
        
        //if student still hasn't applied for this class, continue
        if(empty($existingCOI)) {
            //check if course_offering has faculty assigned
            $co = CourseOffering::where('class_nbr', $request->class_id)
                ->first()
                ->toArray();

            //if no faculty assigned
            if($co['email'] == '') {
                //return error
                return response()->json(
                    [
                        'message' => 'No Faculty-in-Charge assigned in this class. Contact the unit offering the course so they can enter the FIC in SAIS',
                        'status' => 'Error'
                    ], 200
                );
            } else { // if there is faculty assigned
                //begins transaction but won't commit it to DB first
                DB::beginTransaction();
                try {
                    //Create COI
                    Coi::create([
                        "coi_id" => $uniqueId,
                        "class_id" => $request->class_id,
                        "student_id" => $student->campus_id,
                        "status" => "Requested",
                        "comment" => "",
                        "created_at" => Carbon::now()
                    ]);
                    
                    //Create COI TXN
                    CoiTxn::create([
                        "coi_id" => $uniqueId,
                        "action" => "Requested",
                        "committed_by" => Auth::user()->sais_id,
                        "note" => $request->justification,
                        "created_at" => Carbon::now()
                    ]);
                    
                    //generate random alpha numeric characters for external links
                    $randomAlphaNum = $this->generateRandomAlphaNum(50, 1);
                    
                    //create external link
                    ExternalLink::create([
                        "token" => $randomAlphaNum,
                        "model_type" => 'App\Models\Coi',
                        "model_id" => $uniqueId
                    ]);
        
                    // add emailing

                    //Commit the changes to db if there is no error
                    DB::commit();
                    
                    //return ok
                    return response()->json(
                        [
                            'message' => 'COI successfully requested',
                            'status' => 'Ok'
                        ], 200
                    );
        
                } catch (\Exception $ex) {
                    //if there is an error, rollback to previous state of db before beginTransaction
                    DB::rollback();
        
                    //return error
                    return response()->json(
                        [
                            'message' => $ex->getMessage()
                        ], 500
                    );
                }
            }
        } else { //if student has already applied for this class
            //return error
            return response()->json(
                [
                    'message' => 'You have already applied COI to this class with status: ' . $existingCOI->status,
                    'status' => 'Error'
                ], 200
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
