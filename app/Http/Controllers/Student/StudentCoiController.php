<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\ApplyConsentOfInstructor;
use Illuminate\Http\Request;


class StudentCoiController extends Controller
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
    public function store(Request $request, ApplyConsentOfInstructor $applyConsentOfInstructor)
    {
        if(config('app.coi_enabled')) {
            $coiID = $this->generateTxnID("COI");
            $externalLinkToken = $this->generateRandomAlphaNum(50, 1);
            
            return $applyConsentOfInstructor->createCoi($request, $coiID, $externalLinkToken);
        } else {
            return response()->json(
                [
                    'message' => 'Action Denied',
                ], 400
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
