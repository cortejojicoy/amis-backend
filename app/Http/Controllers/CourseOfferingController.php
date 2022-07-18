<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use Illuminate\Http\Request;

class CourseOfferingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $courses = CourseOffering::select('course')->distinct()->get();

        return response()->json(
            [
             'courses' => $courses
            ], 200
         );
    }

    /**
     * Get section of a chosen course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getSections(Request $request)
    {
        $sections = CourseOffering::select('class_nbr','section', 'days', 'times', 'id', 'name', 'descr')->where('course', $request->course)->get();

        return response()->json(
            [
                'sections' => $sections
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
