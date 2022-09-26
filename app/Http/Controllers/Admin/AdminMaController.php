<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MentorStatus;
use App\Models\Admin;
use App\Services\MentorAssignmentApproval;

class AdminMaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request);
        $tags = Admin::where('sais_id', $request->mentor_id)->first();
        $request->merge(['admin' => $tags]);
        
        $mas = MentorStatus::filter($request, 'admins')->get();
        
        $keys = ['actions', 'mentor_name', 'roles', 'field_represented', 'actions'];
        return response()->json([
            'mas' => $mas,
            'keys' => $keys
        ],200);
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

    // public function update(Request $request, $id, ApplyPrerogativeEnrollment $applyPrerogativeEnrollment)
    // {
    //     return $applyPrerogativeEnrollment->updatePrerog($request, $id, 'admins');
    // }
    
    public function update(Request $request, $id, MentorAssignmentApproval $mentorAssignmentApproval)
    {
        $mas_id = $this->generateTxnID("MAS");
        return $mentorAssignmentApproval->updateApproval($request, $id, 'admins', $mas_id);
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
