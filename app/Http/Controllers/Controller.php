<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function generateTxnID($prefix) {
        $randomAlphaNum = $this->generateRandomAlphaNum(4, 0);

        $uniqueId = $prefix . '-' . date("y") . date("m") . date("d") . $randomAlphaNum;

        return $uniqueId;
    }


    // type = 0 -> for txn ID
    // type = 1 -> for others/general use
    public function generateRandomAlphaNum($length, $type) {
        $strOptions = [
            "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ",
            "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz_"
        ];

        $randomAlphaNum = "";

        for($id = 0; $id < $length; $id++) {
            $randomAlphaNum = $randomAlphaNum . str_shuffle($strOptions[$type])[$id];
        }

        return $randomAlphaNum;
    }
}
