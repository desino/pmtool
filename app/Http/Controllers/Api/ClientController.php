<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ClientRequest;
use App\Models\Client;
use App\Models\Project;
use App\Services\AsanaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    protected AsanaService $asanaService;
    public function __construct(AsanaService $asanaService)
    {
        $this->asanaService = $asanaService;
    }
    public function store(ClientRequest $request)
    {
        $validateData = $request->validated();

        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.client.dont_have_permission'), null, 400);
        }

        $status = false;
        $retData = [
            'initiative' => ""
        ];

        $data = [
            'name' => $validateData['initiative_name'],
        ];
        $project = $this->asanaService->createProject($data);
        if ($project['error_status']) {
            return ApiHelper::response($status, __('messages.asana.initiative.store_error'), $retData, 500);
        }

        DB::beginTransaction();
        try {
            $client = Client::create($validateData);
            $validateData['client_id'] = $client->id;
            $validateData['name'] = $validateData['initiative_name'];
            $validateData['asana_project_id'] = $project['data']['data']['gid'];
            $initiative = $client->initiatives()->create($validateData);
            $validateData['name'] = Project::getDefaultProjectName();
            $initiative->project()->create($validateData);
            $status = true;
            $message = __('messages.client.store_success');
            $statusCode = 200;
            $retData = [
                'initiative' => $initiative->load('client'),
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, $retData, $statusCode);
    }
}
