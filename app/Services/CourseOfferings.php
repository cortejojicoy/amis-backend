<?php
namespace App\Services;

use App\Models\CourseOffering;

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
}