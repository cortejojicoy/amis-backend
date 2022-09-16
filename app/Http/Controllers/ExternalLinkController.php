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
        $exLink = ExternalLink::where('token', $request->token)
            ->where('action', null)
            ->first();

        if($exLink) {
            if($exLink->model_type == 'App\Models\Coi') {
                return $useExternalLinks->updateCoi($action, $exLink);
            } else if ($exLink->model_type == 'App\Models\Prerog') {
                $external_link_token = $this->generateRandomAlphaNum(50, 1);

                return $useExternalLinks->updatePrerog($action, $exLink, $external_link_token);
            }
        } else {
            $exLink = ExternalLink::where('token', $request->token)
                ->first();
            
            if($exLink->model_type == 'App\Models\Coi') {
                $module = 'COI';
            } else if ($exLink->model_type == 'App\Models\Prerog') {
                $module = 'Prerog';
            }

            if($exLink->action == 'Cancelled') {
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
