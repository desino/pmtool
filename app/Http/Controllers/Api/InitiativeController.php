<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InitiativeRequest;
use App\Models\Client;
use App\Models\Initiative;
use App\Services\AsanaService;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InitiativeController extends Controller
{
    protected AsanaService $asanaService;
    public function __construct(AsanaService $asanaService)
    {
        $this->asanaService = $asanaService;
    }

    public function getClients(Request $request)
    {
        $clients = ClientService::getAllClients();
        return ApiHelper::response(true, '', $clients, 200);
    }

    public function store(InitiativeRequest $request)
    {
        $validatData = $request->validated();

        $status = false;
        $retData = [
            'initiative' => "",
        ];

        // $getClient = ClientService::getClient($validatData['client_id']);
        // if($getClient){
        //     return ApiHelper::response($status, __('messages.initiative.client_not_exist'), $retData, 400);
        // }        
        $data = [
            'name' => $validatData['name'],
        ];
        $project = $this->asanaService->createProject($data);
        if ($project['error_status']) {
            return ApiHelper::response($status, __('messages.asana.initiative.store_error'), $retData, 500);
        }

        DB::beginTransaction();
        try {
            $validatData['asana_project_id'] = $project['data']['data']['gid'];
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
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $meesage, $retData, $statusCode);
    }
}
