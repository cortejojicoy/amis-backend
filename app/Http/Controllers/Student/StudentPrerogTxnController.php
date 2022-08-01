<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PrerogTxn;
use Illuminate\Http\Request;
use stdClass;

class StudentPrerogTxnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $prerog_txns = PrerogTxn::filter($request, 'students');
        
        if($request->has('items')) {
            $prerog_txns = $prerog_txns->paginate($request->items);
        } else {
            $prerog_txns = $prerog_txns->get();
        }

        //get the keys of the txns
        $keys = ['reference_id', 'course', 'section', 'schedule', 'note', 'action', 'date_created', 'committed_by', 'last_action_date'];

        return response()->json(
            [
             'txns' => $prerog_txns,
             'keys' => $keys,
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
