<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InitiativeRequest;
use App\Models\Client;
use App\Models\Initiative;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InitiativeController extends Controller
{
    public function getClients(Request $request){
        $clients = ClientService::getAllClients();
        return ApiHelper::response(true, '', $clients, 200);
    }

    public function store(InitiativeRequest $request){
        $validatData = $request->validated();
        $status = false;
        $retData = [
            'initiative' => "",
        ];
        DB::beginTransaction();
        try {

            $initiative = Initiative::create($validatData);
            $status = true;
            $meesage = __('messages.initiative.store_success');
            $statusCode = 200;
            $initiative->client;
            $retData = [
                'initiative' => $initiative,
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
        }
        return ApiHelper::response($status, $meesage, $retData, $statusCode);
    }
}