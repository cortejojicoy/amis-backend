<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\MaStudent;
use App\Models\MentorStatus;
use Illuminate\Support\Facades\Auth;

class AdminMaTableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tags = Admin::where('sais_id', Auth::user()->sais_id)->first();
        $request->merge(['tags' => $tags]);
        // dd($tags);

        $admin = MaStudent::distinct('name')->filter($request, 'admins');
        // dd($admin);
        if($request->has('items')) {
            $admin = $admin->paginate($request->items);
        } else {
            $admin = $admin->get();
        }
        // dd($admin);
        
        $keys = ['name', 'program', 'student_status', 'mentor', 'role', 'mentor_status'];

        return response()->json([
            'ma' => $admin,
            'keys' => $keys,
            'tags' => $tags
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
