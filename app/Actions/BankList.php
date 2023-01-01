<?php

namespace App\Actions;

class BankList
{
    public static function get()
    {
        $filePath = public_path('data/indonesia-bank.json');

        if (! file_exists($filePath)) {
            return [];
        }

        $fileContents = file_get_contents($filePath);

        $jsonData = collect(json_decode($fileContents, true))->pluck('name', 'code');

        return $jsonData;
    }
}
