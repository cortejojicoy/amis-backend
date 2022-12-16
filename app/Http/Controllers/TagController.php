<?php

namespace App\Http\Controllers;

use App\Models\CoiTxn;
use App\Models\Tag;
use App\Models\UserPermissionTag;
use App\Services\TagModule;
use App\Services\TagProcessor;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // DO NOT DELETE THIS LINE! THIS SHOWS THE IMPLEMENTATION ON HOW TAGS SHOULD WORK
    // public function index(Request $request, TagProcessor $tagProcessor)
    // {
    //     $upts = CoiTxn::filter($request, "admins", $tagProcessor)->get();
        
    //     return response()->json(
    //         [
    //          'upts' => $upts,
    //         ], 200
    //      );
    // }
    public function index(Request $request)
    {
        $tags = Tag::filter($request);

        if($request->has('items')) {
            $tags = $tags->paginate($request->items);
        } else {
            $tags = $tags->get();
        }
        
        return response()->json(
            [
             'tags' => $tags,
            ], 200
         );
    }
    
    // public function index()
    // {
    //     $adminType = MaAdmin::where('sais_id', Auth::user()->sais_id)->get();

    //     $tags = Admin::where('sais_id', Auth::user()->sais_id)->get();
    //     return response()->json([
    //         "tags" => $tags,
    //         "adminType" => $adminType
    //     ],200);
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, TagModule $tagModule)
    {
        return $tagModule->create($request);
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
    public function update(Request $request, $id, TagModule $tagModule)
    {
        return $tagModule->update($request, $id);
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
