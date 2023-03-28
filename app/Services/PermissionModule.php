<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionModule{
    function create($request) { 
        DB::beginTransaction();
        try {
            
            $permission = Permission::create(['name' => $request->name, 'guard_name' => 'web', 'group' => $request->group]);

            DB::commit();

            return response()->json(
                [
                    'message' => 'Permission Successfully Created',
                    'permission' => $permission,
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