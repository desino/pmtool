<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Initiative;

Class ClientService
{
    public static function getAllClients() {
        $client = Client::all();
        return $client;        
    }
}