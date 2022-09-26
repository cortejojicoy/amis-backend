<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\MentorStatus;

class MaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tags = Admin::where('sais_id', $request->sais_id)->first();
        $request->merge(['admin' => $tags]);

        // $mas = MentorStatus::where('status', 'Endorsed')->where('student_sais_id', $request->id)->get();
        $mas = MentorStatus::filter($request, 'admins')->get();

        // dd($mas);

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
