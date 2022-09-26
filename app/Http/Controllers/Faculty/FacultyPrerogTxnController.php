<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\PrerogTxn;
use Illuminate\Http\Request;

class FacultyPrerogTxnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $prgTxns = PrerogTxn::filter($request, 'faculties');

        if($request->has('items')) {
            $prgTxns = $prgTxns->paginate($request->items);
        } else {
            $prgTxns = $prgTxns->get();
        }

        $keys = ['reference_id', 'term', 'course', 'section', 'student_no', 'action', 'date_created', 'committed_by', 'last_action_date'];

        return response()->json(
            [
             'txns' => $prgTxns,
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
