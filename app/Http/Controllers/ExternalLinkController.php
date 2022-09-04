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
    public function index(Request $request, $action, UseExternalLinks $useExternalLinks)
    {
        $ex_link = ExternalLink::where('token', $request->token)
            ->where('action', null)
            ->first();

        if($ex_link) {
            if($ex_link->model_type == 'App\Models\Coi') {
                return $useExternalLinks->updateCoi($action, $ex_link);
            } else if ($ex_link->model_type == 'App\Models\Prerog') {
                $external_link_token = $this->generateRandomAlphaNum(50, 1);

                return $useExternalLinks->updatePrerog($action, $ex_link, $external_link_token);
            }
        } else {
            $ex_link = ExternalLink::where('token', $request->token)
                ->first();
            
            if($ex_link->model_type == 'App\Models\Coi') {
                $module = 'COI';
            } else if ($ex_link->model_type == 'App\Models\Prerog') {
                $module = 'Prerog';
            }

            if($ex_link->action == 'Cancelled') {
                $message = "Link has been disabled. " . $module . " has been cancelled by the student.";
            } else {
                $message = "Link already used or the " . $module . " has already been acted upon via the system!";
            }

            return view('external-link', [
                "message" => $message,
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
