<?php
namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class TagModule{
    function update($request, $id) { 
        DB::beginTransaction();
        try {
            
            $tag = Tag::find($id)->update([
                'tag_name' => $request->tag_data['tag_name'],
                'reference_model' => $request->tag_data['reference_model'],
                'reference_field' => $request->tag_data['reference_field'],
                'reference_value' => $request->tag_data['reference_value'],
                'reference_type' => $request->tag_data['reference_type'],
                'reference_operation' => $request->tag_data['reference_operation']
            ]);

            DB::commit();

            return response()->json(
                [
                    'message' => 'Tag Successfully Updated',
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

    function create($request) {
        DB::beginTransaction();
        try {
            
            $tag = Tag::create([
                'tag_name' => $request->tag_name,
                'reference_model' => $request->reference_model,
                'reference_field' => $request->reference_field,
                'reference_value' => $request->reference_value,
                'reference_type' => $request->reference_type,
                'reference_operation' => $request->reference_operation
            ]);

            DB::commit();

            return response()->json(
                [
                    'message' => 'Tag Successfully Created',
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