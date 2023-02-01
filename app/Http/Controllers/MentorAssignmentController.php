<?php

namespace App\Http\Controllers;

use App\Models\MentorAssignment;
use App\Models\Mentor;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TagProcessor;


class MentorAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, TagProcessor $tagProcessor)
    {
        $faculty = Faculty::where('uuid', Auth::user()->uuid)->first();
        // $mentor = Mentor::where('faculty_id', $faculty->faculty_id)->first();
        $request->merge([
            'mentor' => $faculty,
            'access_permission' => 'tags'
        ]);

        $ma = MentorAssignment::filter($request, $tagProcessor);
        
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
