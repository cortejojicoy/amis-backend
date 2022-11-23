<?php
namespace App\Services;

use App\Models\Tag;
use App\Models\User;
use App\Models\UserPermissionTag;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserService {
    function updateRole($request, $id){
        DB::beginTransaction();
        try {
            $user = User::where('sais_id', $id)->first();
            $role = Role::where('name', $request->role)->first();

            if($request->mode == 'add') {
                if($user->hasRole($request->role)) {
                    return response()->json(
                        [
                            'message' => 'User already has: ' . $role->name . ' role',
                        ], 400
                    );
                } else {
                    $user->assignRole($request->role);
                }
            } else if ($request->mode == 'remove') {
                $user->removeRole($request->role);
            }

            $updatedUser = User::filter($request)->find($id);
    
            DB::commit();

            return response()->json(
                [
                    'message' => 'User Role Successfully Updated',
                    'user' => $updatedUser,
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

    function updatePermission($request, $id) {
        DB::beginTransaction();
        try {
            
            $user = User::where('sais_id', $id)->first();
            $permission = Permission::where('name', $request->permission)->first();

            if($request->mode == 'add') {
                if($user->hasPermissionTo($permission->name)) {
                    return response()->json(
                        [
                            'message' => 'User already has: ' . $permission->name . ' permission',
                        ], 400
                    );
                } else {
                    $user->givePermissionTo($request->permission);

                    $upt = UserPermissionTag::create([
                        "model_id" => $id,
                        "permission_id" => $permission->id,
                        "tags" => json_encode($request->tags)
                    ]);
                }
            } else if($request->mode == 'remove') {
                $user->revokePermissionTo($permission->name);

                $upt = UserPermissionTag::where('model_id', $id)
                    ->where('permission_id', $permission->id)
                    ->first();

                $upt->delete();
            } else if($request->mode == 'edit') {
                $upt = UserPermissionTag::where('model_id', $id)
                    ->where('permission_id', $permission->id)
                    ->update(['tags' => json_encode($request->tags)]);
            }

            $updatedUser =  User::filter($request)->find($id);
        
            DB::commit();

            return response()->json(
                [
                    'message' => 'User Permission Successfully Updated',
                    'user' => $updatedUser,
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