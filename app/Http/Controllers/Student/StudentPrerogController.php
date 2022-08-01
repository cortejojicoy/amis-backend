<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\ApplyPrerogativeEnrollment;
use Illuminate\Http\Request;

class StudentPrerogController extends Controller
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
    public function store(Request $request, ApplyPrerogativeEnrollment $applyPrerogativeEnrollment)
    {
        $prg_id = $this->generateTxnID("PRG");
        $external_link_token = $this->generateRandomAlphaNum(50, 1);

        return $applyPrerogativeEnrollment->createPrerog($request, $prg_id, $external_link_token);
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
