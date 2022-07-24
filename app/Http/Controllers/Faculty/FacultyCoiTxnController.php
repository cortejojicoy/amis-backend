<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\CoiTxn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacultyCoiTxnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $coi_txns = CoiTxn::facultyTxns()->filter($request, 'faculties');

        if($request->has('items')) {
            $coi_txns = $coi_txns->paginate($request->items);
        } else {
            $coi_txns = $coi_txns->get();
        }

        $keys = ['reference_id', 'term', 'class', 'section', 'student_no', 'trx_date', 'trx_status', 'last_commit'];

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
