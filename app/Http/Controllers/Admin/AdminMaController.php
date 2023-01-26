<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MentorStatus;
use App\Models\Ma;
use App\Models\Mentor;
use App\Services\MentorAssignmentApproval;
use App\Services\TagProcessor;

class AdminMaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, TagProcessor $tagProcessor)
    {
        // $mentor = Mentor::where('uuid', $request->uuid)->first();
        // $request->merge(['mentor' => $mentor]);

        $ma = Ma::filter($request, $tagProcessor);
        
        if($request->has('items')) {
            $ma = $ma->paginate($request->items);
        } else {
            $ma = $ma->get();
        }
        
        return response()->json([
            'ma' => $ma
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
