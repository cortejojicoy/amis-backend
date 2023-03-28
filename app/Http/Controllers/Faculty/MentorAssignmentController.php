<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\MentorAssignment;
use App\Models\Mentor;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $faculty = Faculty::where('uuid', Auth::user()->uuid)->first();
        // $mentor = Mentor::where('faculty_id', $faculty->faculty_id)->first();
        $request->merge(['mentor' => $faculty]);

        $ma = MentorAssignment::filter($request);
        
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
     * @param  \App\Models\MentorAssignment  $mentorAssignment
     * @return \Illuminate\Http\Response
     */
    public function show(MentorAssignment $mentorAssignment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MentorAssignment  $mentorAssignment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MentorAssignment $mentorAssignment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MentorAssignment  $mentorAssignment
     * @return \Illuminate\Http\Response
     */
    public function destroy(MentorAssignment $mentorAssignment)
    {
        //
    }
}
