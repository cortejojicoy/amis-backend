<?php
namespace App\Services;

class GenericService {
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

    function getTerm($year, $sem) {
        return 1000 + $this->getYear($year) +  $this->getSem($sem);
    }

    function getSem($sem) {
        if($sem == 'First Semester') {
            return 1;
        } else if ($sem == 'Second Semester') {
            return 2;
        } else if ($sem == 'Midyear') {
            return 3;
        } else if ($sem == 'First Trimester') {
            return 4;
        } else if ($sem == 'Second Trimester') {
            return 5;
        } else if ($sem == 'Third Trimester') {
            return 6;
        }
    }

    function getYear($ay) {
        $year = explode('-', $ay)[0];
        $year_code = (int )substr($year, 2);

        return $year_code * 10;
    }
}