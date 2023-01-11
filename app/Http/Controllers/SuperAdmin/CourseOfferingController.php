<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseOffering\PostRequest;
use App\Http\Requests\CourseOffering\PutRequest;
use App\Models\CourseOffering;
use App\Services\CourseOfferings;
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
        $course_offerings = CourseOffering::filter($request);

        if($request->has('items')) {
            $course_offerings = $course_offerings->paginate($request->items);
        } else {
            $course_offerings = $course_offerings->get();
        }
        
        return response()->json(
            [
             'course_offerings' => $course_offerings,
            ], 200
         );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request, CourseOfferings $courseOfferings)
    {
        return $courseOfferings->createCourseOffering($request);
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
    public function update(PutRequest $request, $id, CourseOfferings $courseOfferings)
    {
        return $courseOfferings->editCourseOffering($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, CourseOfferings $courseOfferings)
    {
        return $courseOfferings->deleteCourseOffering($id);
    }
}
