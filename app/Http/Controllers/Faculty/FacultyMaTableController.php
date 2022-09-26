<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaStudent;
use App\Models\Mentor;


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

        // dd($request);
        $ma = MaStudent::distinct('name')->filter($request, 'faculties');
        // dd($ma);
        if($request->has('items')) {
            $ma = $ma->paginate($request->items);
        } else {
            $ma = $ma->get();
        }

        $keys = ['name', 'program', 'student_status', 'mentor', 'role', 'mentor_status'];
        return response()->json(
            [
                'ma' => $ma,
                'keys' => $keys
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
