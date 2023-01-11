<?php
namespace App\Services;

use App\Models\CourseOffering;
use App\Models\Faculty;
use App\Models\StudentTerm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseOfferings{
    function updateCourseOffering($request, $id) { 
        DB::beginTransaction();
        try {
            CourseOffering::where('class_nbr', $id)->update($request->all());

            DB::commit();

            return response()->json(
                [
                    'message' => 'Class Successfully Updated',
                    'status' => 'Ok'
                ], 200
            );

        } catch (\Exception $ex) {
            //if there is an error, rollback to previous state of db before beginTransaction
            DB::rollback();

            //return error
            return response()->json(
                [
                    'message' => $ex->getMessage()
                ], 500
            );
        }
    }

    function createCourseOffering($request) {
        DB::beginTransaction();
        try {

            $user = User::where('sais_id', $request->id)->first();
            $studentTerm = StudentTerm::where('status', 'ACTIVE')->first();
            $course = $request->subject . ' ' . $request->catalog;

            CourseOffering::create([
                'institution' => $request->institution,
                'career' => $request->career,
                'term' => $studentTerm->term_id,
                'course_id' => $request->course_id,
                'acad_org' => $request->acad_org,
                'acad_group' => $request->acad_group,
                'course' => $course,
                'activity' => '',
                'subject' => $request->subject,
                'catalog' => $request->catalog,
                'descr' => $request->descr,
                'component' => $request->component,
                'section' => $request->section,
                'class_nbr' => $request->class_nbr,
                'times' => $request->times ? $request->times : '',
                'days' => $request->days ? $request->days : '',
                'facil_id' => '',
                'tot_enrl' => 0,
                'cap_enrl' => 15,
                'id' => $request->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'consent' => $request->consent,
                'prerog' => true,
                'offer_nbr' => $request->offer_nbr,
                'topic_id' => $request->topic_id
            ]);

            DB::commit();

            return response()->json(
                [
                    'message' => 'Course Offering Successfully Created',
                    'status' => 'Ok'
                ], 200
            );

        } catch (\Exception $ex) {
            //if there is an error, rollback to previous state of db before beginTransaction
            DB::rollback();

            //return error
            return response()->json(
                [
                    'message' => $ex->getMessage()
                ], 500
            );
        }
    }

    function editCourseOffering($request, $id) { 
        DB::beginTransaction();
        try {
            $course_offering = CourseOffering::find($id);

            if(!$course_offering) {
                return response()->json(
                    [
                        'message' => 'Course Offering does not exist.',
                    ], 400
                );
            }

            if($request->has('id')) {
                $user = User::where('sais_id', $request->id)->first();
                $request->merge(['name' => $user->full_name]);
                $request->merge(['email' => $user->email]);
            }

            $course_offering->update($request->all());

            DB::commit();

            return response()->json(
                [
                    'message' => 'Class Successfully Updated',
                    'status' => 'Ok'
                ], 200
            );

        } catch (\Exception $ex) {
            //if there is an error, rollback to previous state of db before beginTransaction
            DB::rollback();

            //return error
            return response()->json(
                [
                    'message' => $ex->getMessage()
                ], 500
            );
        }
    }

    function deleteCourseOffering($id) { 
        DB::beginTransaction();
        try {

            $course_offering = CourseOffering::find($id);

            if(!$course_offering) {
                return response()->json(
                    [
                        'message' => 'Course Offering does not exist.',
                    ], 400
                );
            }

            $course_offering->delete();

            DB::commit();

            return response()->json(
                [
                    'message' => 'Class Successfully Deleted',
                    'status' => 'Ok'
                ], 200
            );

        } catch (\Exception $ex) {
            //if there is an error, rollback to previous state of db before beginTransaction
            DB::rollback();

            //return error
            return response()->json(
                [
                    'message' => $ex->getMessage()
                ], 500
            );
        }
    }
}