<?php
namespace App\Services;
use App\Models\SaveMentor;
use Illuminate\Support\Facades\DB;

class BulkUpdateSaveMentor {
    function insertDeleteSaveMentor($request){
        DB::beginTransaction();
        try {
            SaveMentor::where('sais_id',$request->sais_id)->delete();
            SaveMentor::insert($request->input());
            DB::commit();
            return response()->json(['message' => 'Successfully updated.'], 200);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['message' => $ex->getMessage()], 500);
        }
    }
}