<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaStudent;
use App\Models\Faculty;
use App\Models\Mentor;
use App\Models\Ma;


class FacultyMaTableController extends Controller
{
    /**
     * Display a listing of the resource.   
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $advisee = Mentor::mentorRole()->get();
        // $request->merge(['faculty' => $advisee]);

        // $faculty = Faculty::where('sais_id', $request->sais_id)->first();
        // $mentor = Mentor::where('faculty_id', $faculty->faculty_id)->first();
        // $request->merge(['adviser' => $mentor]);

        $ma = Ma::filter($request, 'faculties');
  
        if($request->has('items')) {
            $ma = $ma->paginate($request->items);
        } else {
            $ma = $ma->get();
        }

        return response()->json(
            [
                'ma' => $ma
            ], 200
        );
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
