<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function store(ClientRequest $request){
        $validatData = $request->validated();

        $status = false;
        $retData = [
            'initiative' => ""
        ];
        DB::beginTransaction();
        try {
            $client = Client::create($validatData);
            $validatData['client_id'] = $client->id;
            $validatData['name'] = $validatData['initiative_name'];
            $initiative = $client->initiatives()->create($validatData);
            $status = true;
            $meesage = __('messages.client.store_success');
            $statusCode = 200;
            $retData = [
                'initiative' => $initiative->load('client'),
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $meesage, $retData, $statusCode);
    }
}