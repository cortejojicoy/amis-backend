<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\PrerogTxn;
use Illuminate\Http\Request;

class PrerogTxnController extends Controller
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

        $prerogTxns = PrerogTxn::filter($request, 'admins');
        
        if($request->has('items')) {
            $prerogTxns = $prerogTxns->paginate($request->items);
        } else {
            $prerogTxns = $prerogTxns->get();
        }

        //get the keys of the txns
        $keys = ['reference_id', 'term', 'course', 'section', 'student_no', 'action', 'date_created', 'committed_by', 'last_action_date'];

        return response()->json(
            [
             'txns' => $prerogTxns,
             'keys' => $keys,
             'admin' => $admin->college
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
