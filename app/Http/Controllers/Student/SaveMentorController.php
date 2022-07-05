<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SaveMentor;
use App\Services\BulkUpdateSaveMentor;
use App\Http\Requests\BulkUpdateSaveMentorRequest;

class SaveMentorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $save_mentors =  SaveMentor::where('saisid','12345687')->get();
        if($request->has('mentor_name')){
            $save_mentors = $save_mentors->where('mentor_name',$request->mentor_name);
        }
        return response()->json(
            [
             'save_mentors' => $save_mentors,
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
        return SaveMentor::create($request->all);
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
        Article::find($id)->delete();

        return 204;
    }

    /**
     * Add/Delete bulk resources from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bulkUpdate(BulkUpdateSaveMentorRequest $request, BulkUpdateSaveMentor $bulkUpdateSaveMentor)
    {
        return $bulkUpdateSaveMentor->insertDeleteSaveMentor($request);
    }
}
