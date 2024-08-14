<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EditOpportunityRequest;
use App\Models\Initiative;
use App\Services\ClientService;
use App\Services\InitiativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OpportunityController extends Controller
{
    public function index(Request $request){
        $perPage = $request->input('per_page', 10);
        $oppertunities = InitiativeService::getOpportunityInitiative($request, $perPage, Initiative::getStatusOpportunity());
        $parsedOppertunities = ApiHelper::parsePagination($oppertunities);
        $responseData = [
            'oppertunities' => $parsedOppertunities,
            'ballparkTotal' => $oppertunities->sum('ballpark_development_hours'),
        ];
        return ApiHelper::response(true, __('messages.opportunity.get_list_success'), $responseData, 200);
    }

    public function getInitialData(Request $request){
        $clients = ClientService::getAllClients();
        $responseData = [
            'clients' => $clients
        ];
        return ApiHelper::response(true, '', $responseData, 200);
    }

    public function update(EditOpportunityRequest $request){
        $requestData = $request->all();
        $initiative = Initiative::find($requestData['id']);

        if(!$initiative) {
            return ApiHelper::response(false, __('messages.opportunity.not_found'), null, 404);
        }
        $status = false;
        DB::beginTransaction();
        try {
            $initiative->update($requestData);
            $status = true;
            $meesage = __('messages.opportunity.update_success');
            $statusCode = 200;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $meesage, '', $statusCode);
    }

    public function updateStatusLost(Request $request){
        $initiative = Initiative::find($request->post('id'));
        if(!$initiative) {
            return ApiHelper::response(false, __('messages.opportunity.not_found'), null, 404);
        }
        $status = false;
        DB::beginTransaction();
        try {
            $updateData = [
                'status' => Initiative::getStatusLost()
            ];
            $initiative->update($updateData);
            $status = true;
            $meesage = __('messages.opportunity.update_status_lost_success');
            $statusCode = 200;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $meesage, '', $statusCode);
    }
}