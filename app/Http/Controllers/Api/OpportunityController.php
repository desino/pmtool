<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EditOpportunityRequest;
use App\Models\Initiative;
use App\Models\InitiativeEnvironment;
use App\Models\Template;
use App\Models\User;
use App\Services\AsanaService;
use App\Services\ClientService;
use App\Services\InitiativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OpportunityController extends Controller
{
    protected AsanaService $asanaService;
    public function __construct(AsanaService $asanaService)
    {
        $this->asanaService = $asanaService;
    }
    public function index(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.opportunity.dont_have_permission'), null, 404);
        }
        $perPage = $request->input('per_page', 10);
        $Opportunities = InitiativeService::getOpportunityInitiative($request, $perPage);
        $parsedOpportunities = ApiHelper::parsePagination($Opportunities);
        $responseData = [
            'opportunities' => $parsedOpportunities,
            'ballparkTotal' => $Opportunities->sum('ballpark_development_hours'),
        ];
        return ApiHelper::response(true, __('messages.opportunity.get_list_success'), $responseData, 200);
    }

    public function getInitialData(Request $request)
    {
        $clients = ClientService::getAllClients();
        $responseData = [
            'clients' => $clients
        ];
        return ApiHelper::response(true, '', $responseData, 200);
    }

    public function update(EditOpportunityRequest $request)
    {
        $requestData = $request->all();

        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.opportunity.dont_have_permission'), null, 400);
        }

        $initiative = Initiative::find($requestData['id']);

        if (!$initiative) {
            return ApiHelper::response(false, __('messages.opportunity.not_found'), null, 400);
        }
        $status = false;

        if ($initiative->asana_project_id && $requestData['name'] != $initiative->name) {
            $data = [
                'name' => $requestData['name'],
            ];
            $project = $this->asanaService->updateProject($initiative->asana_project_id, $data);

            if ($project['error_status']) {
                return ApiHelper::response($status, __('messages.asana.initiative.store_error'), '', 500);
            }
            // $requestData['asana_project_id'] = $project['data']['data']['gid'];
        }
        $status = true;
        $message = __('messages.opportunity.update_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $initiative->update($requestData);
            $initiative->initiativeEnvironments()->delete();
            $environments = Arr::where($requestData['environments'], function ($value, $key) {
                return $value['name'] != '' || $value['url'] != '';
            });
            if (!empty($environments)) {
                foreach ($environments as $environment) {
                    $environment['initiative_id'] = $initiative->id;
                    $attributes = ['id' => $environment['id']];
                    InitiativeEnvironment::updateOrCreate($attributes, $environment);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }

    public function updateStatusLost(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.opportunity.dont_have_permission'), null, 404);
        }
        $initiative = Initiative::find($request->post('id'));
        if (!$initiative) {
            return ApiHelper::response(false, __('messages.opportunity.not_found'), null, 404);
        }
        $status = true;
        $message = __('messages.opportunity.update_status_lost_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $updateData = [
                'status' => Initiative::getStatusLost()
            ];
            $initiative->update($updateData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }

    public function getClientList(Request $request)
    {
        $clientList = ClientService::getAllClients();
        return ApiHelper::response(true, '', $clientList, 200);
    }

    public function getEditOpportunityData(Request $request)
    {
        $clientList = User::all();
        $retData = [
            'clients' => $clientList,
            'initiative_server_type' => InitiativeEnvironment::getServerTypeAll(),
            'pdf_templates' => Template::get()
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }

    public function getOpportunity(Request $request, $id)
    {
        $clientList = InitiativeService::getInitiative($request, $id);
        return ApiHelper::response(true, '', $clientList, 200);
    }
}
