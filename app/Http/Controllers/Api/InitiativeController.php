<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InitiativeRequest;
use App\Models\Client;
use App\Models\Initiative;
use Illuminate\Http\Request;

class InitiativeController extends Controller
{
    public function getClients(Request $request){
        $clients = Client::getAllClients();
        return ApiHelper::response(true, '', $clients, 200);
    }

    public function store(InitiativeRequest $request){
        $validatData = $request->validated();        
        $status = false;
        try {
            Initiative::create($validatData);
            $status = true;
            $meesage = __('messages.initiative.store_success');
            $statusCode = 200;
        } catch (\Exception $e) {            
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
        }
        return ApiHelper::response($status, $meesage, '', $statusCode);
    }
}
