<?php
namespace App\Services;

use App\Models\Tag;
use App\Models\User;
use App\Models\UserPermissionTag;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class TagProcessor {
    function process($query, $filter){
        //check permission of the user logged in
        // $user = User::find('sais_id', Auth::user()->sais_id);
        $user = User::where('sais_id', Auth::user()->sais_id)->first();
        
        //if has permission, get tags
        if($user->hasPermissionTo($filter->access_permission)) {
            $upr = UserPermissionTag::where('model_id', $user->sais_id)
                ->where('permission_id', Permission::where('name', $filter->access_permission)->first()->id)
                ->first();
            
            $tags = json_decode($upr->tags);

            foreach ($tags as $index => $set) {
                if($index == 0) {
                    $query->where(function($query) use ($set) {
                        foreach ($set as $single_tag) {
                            $tag = Tag::where('tag_name', $single_tag)->first();
                            $query->where($tag->reference_model . "." . $tag->reference_field, $tag->reference_operation, $tag->reference_value); 
                        }
                    });
                } else {
                    $query->orWhere(function($query) use ($set) {
                        foreach ($set as $single_tag) {
                            $tag = Tag::where('tag_name', $single_tag)->first();
                            $query->where($tag->reference_model . "." . $tag->reference_field, $tag->reference_operation, $tag->reference_value); 
                        }
                    });
                }
            }

            return $query;
        }
    }
}