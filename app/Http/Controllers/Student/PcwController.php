<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Pcw;
use App\Services\GenericService;
use App\Services\PlanOfCourseworkService;
use Illuminate\Http\Request;

class PcwController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pcws = Pcw::filter($request, 'student');

        if($request->has('items')) {
            $pcws = $pcws->paginate($request->items);
        } else {
            $pcws = $pcws->get();
        }
        
        return response()->json(
            [
             'pcws' => $pcws,
            ], 200
         );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, PlanOfCourseworkService $planOfCourseworkService)
    {
        if(config('app.pcw_enabled')) {
            return $planOfCourseworkService->createPCW($request);
        } else {
            return response()->json(
                [
                    'message' => 'Action Denied',
                ], 400
            );
        }
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
