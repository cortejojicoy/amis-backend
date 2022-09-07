<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CoiTxn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentCoiTxnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //get the coi_txn_history of student
        $coi_txns = CoiTxn::filter($request, 'students');
        
        if($request->has('items')) {
            $coi_txns = $coi_txns->paginate($request->items);
        } else {
            $coi_txns = $coi_txns->get();
        }

        //get the keys of the txns
        $keys = ['reference_id', 'course', 'section', 'schedule', 'note', 'action', 'date_created', 'committed_by', 'last_action', 'last_action_date'];

        return response()->json(
            [
             'txns' => $coi_txns,
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
