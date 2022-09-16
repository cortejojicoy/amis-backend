<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Prerog;
use App\Services\ApplyPrerogativeEnrollment;
use Illuminate\Http\Request;

class FacultyPrerogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $prgs = Prerog::filter($request, 'faculties');

        if($request->has('items')) {
            $prgs = $prgs->paginate($request->items);
        } else {
            $prgs = $prgs->get();
        }
        
        return response()->json(
            [
             'prgs' => $prgs,
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
    public function update(Request $request, $id, ApplyPrerogativeEnrollment $applyPrerogativeEnrollment)
    {
        if(config('app.prerog_enabled')) {
            $external_link_token = $this->generateRandomAlphaNum(50, 1);

            return $applyPrerogativeEnrollment->updatePrerog($request, $id, 'faculties', $external_link_token);
        } else {
            return response()->json(
                [
                    'message' => 'Action Denied',
                ], 400
            );
        }
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
