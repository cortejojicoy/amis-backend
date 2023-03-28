<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TagProcessor;
use App\Services\MentorAssignmentService;
use App\Models\Ma;
use App\Models\Mentor;
// use App\Models\Faculty;

class MaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, TagProcessor $tagProcessor)
    {   
        $mentor = Mentor::where('uuid', $request->uuid)->first();
        $request->merge(['mentor' => $mentor]);

        // $faculty = Faculty::where('uuid', $request)

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
    public function update(Request $request, $id, MentorAssignmentService $service)
    {
        return $service->updateApproval($request, $id);
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
