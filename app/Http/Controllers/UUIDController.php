<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;
use App\Models\User;
use App\Models\Faculty;


class UUIDController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // update for single user
        // $uuid = Uuid::generate()->string;
        // $user = DB::table('users')
        //         ->where('sais_id', '=', 91929394)
        //         ->update([
        //             'uuid' => $uuid
        //         ]);
        
        
        // $user = Faculty::where('sais_id', '!=', null)->get();
        $user = User::where('sais_id', '!=', null)->get();
        foreach($user as $users) {
            // $uuid = Uuid::generate()->string;
            // mass update; users table
            DB::table('admins')
            ->where('sais_id', '=', $users->sais_id)
            ->update([
                'uuid' => $users->uuid
            ]);

            // mass update; faculties table
            // DB::table('faculties')
            //     // ->where('sais_id', '=', $users->sais_id)
            //     ->where('faculty_id', '<', 2094)
            //     ->where('faculty_id', '>', 2065)
            //     ->update([
            //         'uuid' => $users->uuid
            //     ]);

        //     DB::table('students')
        //         ->where('sais_id', '=', $users->sais_id)
        //         ->update([
        //             'uuid' => $users->uuid
        //         ]);
        }

        return response()->json([
            'uuid' => $user
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
