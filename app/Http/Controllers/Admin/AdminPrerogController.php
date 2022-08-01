<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Prerog;
use App\Services\ApplyPrerogativeEnrollment;
use Illuminate\Http\Request;

class AdminPrerogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admin = Admin::where('sais_id', $request->sais_id)->first();
        $request->merge(['admin' => $admin]);

        $prgs = Prerog::filter($request, 'admins');

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
        return $applyPrerogativeEnrollment->updatePrerog($request, $id, 'admins');
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
