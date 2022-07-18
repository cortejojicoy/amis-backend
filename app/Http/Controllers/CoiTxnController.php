<?php

namespace App\Http\Controllers;

use App\Models\CoiTxn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoiTxnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //get the coi_txn_history of student
        $coi_txns = DB::table('coitxns AS ctxn')
        ->select(DB::raw("c.coi_id as reference_id, co.course, co.section, CONCAT(co.days, ' ', co.times) AS schedule, ctxn.note, ctxn.action, to_char(ctxn.created_at, 'DD MON YYYY hh12:mi AM') as date_created, u.email as committed_by, to_char(c.submitted_to_sais, 'DD MON YYYY hh12:mi AM') as last_action_date"))
        ->leftJoin('cois AS c', 'c.coi_id', '=', 'ctxn.coi_id')
        ->leftJoin('course_offerings AS co', 'c.class_id', 'co.class_nbr')
        ->leftJoin('users AS u', 'u.sais_id', '=', 'ctxn.committed_by')
        ->leftJoin('students AS s', 's.sais_id', 'u.sais_id')
        ->where('u.sais_id', Auth::user()->sais_id)
        ->orderByDesc('ctxn.created_at')
        ->paginate(5);

        //get the keys of the txns
        // $keys = array_keys(get_object_vars($coi_txns->items()[0]));
        $keys = ['reference_id', 'course', 'section', 'schedule', 'note', 'action', 'date_created', 'committed_by', 'last_action_date'];

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
