<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function store(ClientRequest $request){
        $validatData = $request->validated();
        
        $status = false;
        try {
            $client = Client::create($validatData);
            $validatData['client_id'] = $client->id;
            $client->initiatives()->create($validatData);
            $status = true;
            $meesage = __('messages.client.store_success');
            $statusCode = 200;
        } catch (\Exception $e) {
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
        }
        return ApiHelper::response($status, $meesage, '', $statusCode);
    }
}
