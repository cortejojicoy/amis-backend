<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class MaTxnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // query for transaction
        $ma_txns = DB::table('mentor_assignment_txns AS mtxn')
                    ->select(DB::raw("mtxn.mas_id as trx_id, to_char(mtxn.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, mtxn.action as trx_status, to_char(mtxn.created_at, 'DD MON YYYY hh12:mi AM') as resolution_data, u.email as last_commit, ma.actions as action, mtxn.note, ma.mentor_name as mentor, ma.mentor_role, ma.mentor_id"))
                    ->leftJoin('mentor_assignments AS ma', 'ma.mas_id', '=', 'mtxn.mas_id')
                    ->leftJoin('users as u', 'u.sais_id', '=', 'mtxn.committed_by')
                    ->leftJoin('faculties as f', 'f.sais_id', '=', 'ma.mentor_id')
                    // ->where('mentor_assignment_txn.last_commit', Auth::user()->saisid)
                    ->paginate(5);

        // $keys = ['mas_txn_id', 'mas_id', 'action', 'committed_by', 'note', 'mentor_role', 'mentor_id'];
        
        $keys = ['trx_id', 'trx_date', 'trx_status', 'resolution_data', 'last_commit', 'action', 'note', 'mentor', 'mentor_role'];
        
        return response()->json([
            'txns' => $ma_txns,
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
