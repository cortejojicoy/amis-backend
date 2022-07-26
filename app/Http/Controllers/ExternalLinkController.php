<?php

namespace App\Http\Controllers;

use App\Models\ExternalLink;
use App\Services\UseExternalLinks;
use Illuminate\Http\Request;

class ExternalLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($action, $token, UseExternalLinks $useExternalLinks)
    {
        $ex_link = ExternalLink::where('token', $token)
            ->where('action', null)
            ->first();;

        if($ex_link) {
            if($ex_link->model_type == 'App\Models\Coi') {
                return $useExternalLinks->updateCoi($action, $ex_link);
            }
        } else {
            return view('external-link', [
                "message" => "Link already used or COI has already been acted upon via the system!",
                "subMessage" => "You may now exit this tab. Thank you!"
            ]);
        }
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
