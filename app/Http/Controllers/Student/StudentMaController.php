<?php

namespace App\Http\Controllers\Student;

use App\Http\Requests\MentorAssignment\SubmitRequest;
use App\Http\Requests\MentorAssignment\SaveRequest;
use App\Services\MentorAssignmentService;
use App\Models\StudentProgramRecord;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaveMentor;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;

class StudentMaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ma = SaveMentor::filter($request)->get();
        // if($request->has('items')) {
        //     $ma = $ma->paginate($request->items);
        // } else {
        //     $ma = $ma->get();
        // }

        return response()->json(
            [
             'ma' => $ma,
            ], 200
         );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubmitRequest $request, MentorAssignmentService $service)
    {
        foreach($request->input() as $keys => $data) {
            $mas_id[$keys] = $this->generateTxnID("MAS");   
            $loop = $service->submitRequestedMentor($data, $keys, $mas_id);
        }
        return $loop;
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

    public function bulkUpdate(Request $request, MentorAssignmentService $bulkUpdateSaveMentor)
    {
        return $bulkUpdateSaveMentor->insertDeleteSaveMentor($request);
    }
}
