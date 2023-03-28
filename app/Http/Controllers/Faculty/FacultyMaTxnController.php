<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaTxn;
use App\Models\Mentor;
use App\Models\Faculty;
use App\Services\TagProcessor;

class FacultyMaTxnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, TagProcessor $tagProcessor)
    {
        $faculty = Faculty::where('uuid', $request->uuid)->first();
        $request->merge(['mentor' => $faculty]);

        $ma_txns = MaTxn::filter($request, 'faculties', $tagProcessor);

        if($request->has('items')) {
            $ma_txns = $ma_txns->paginate($request->items);
        } else {
            $ma_txns = $ma_txns->get();
        }
                    
        $keys = ['trx_id', 'trx_date', 'trx_status', 'last_commit', 'action', 'note', 'mentor', 'mentor_role'];
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
