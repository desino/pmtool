<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Models\Ticket;
use App\Services\InitiativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeploymentCenterController extends Controller
{
    public function index(Request $request)
    {
        $testDeploymentInitiative = InitiativeService::getInitiativeWithTestDeploymentTickets();
        $acceptanceDeploymentInitiative = InitiativeService::getInitiativeWithAcceptanceDeploymentTickets();
        $data = [
            'testDeploymentInitiatives' => $testDeploymentInitiative,
            'acceptanceDeploymentInitiative' => $acceptanceDeploymentInitiative,
        ];

        return ApiHelper::response(true, '', $data, 200);
    }

    public function getTestDeploymentTicketsModalData(Request $request, $initiativeId)
    {
        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request, $initiativeId);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $status = true;
        $ticket = Ticket::select('*')
            ->where('initiative_id', $initiativeId)
            ->readyForTestStatus()
            ->get();
        $data = [
            'tickets' => $ticket
        ];
        return ApiHelper::response($status, '', $data, 200);
    }

    public function submitTestDeploymentTicket(Request $request, $initiativeId)
    {

        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request, $initiativeId);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        DB::beginTransaction();
        try {
            Ticket::whereIn('id', $request->input('ticketIds'))->update(['status' => Ticket::getStatusOngoing()]);
            $status = true;
            $message = __('message.home.deployment_center.test_deployment.update_status_success');
            $statusCode = 200;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }

    public function getAcceptanceDeploymentTicketsModalData(Request $request, $initiativeId)
    {

        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request, $initiativeId);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $status = true;
        $ticket = Ticket::select('*')
            ->where('initiative_id', $initiativeId)
            ->readyForAcceptanceStatus()
            ->get();
        $data = [
            'tickets' => $ticket
        ];
        return ApiHelper::response($status, '', $data, 200);
    }

    public function submitAcceptanceDeploymentTicket(Request $request, $initiativeId)
    {
        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request, $initiativeId);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        DB::beginTransaction();
        try {
            Ticket::whereIn('id', $request->input('ticketIds'))->update(['status' => Ticket::getStatusOngoing()]);
            $status = true;
            $message = __('message.home.deployment_center.test_deployment.update_status_success');
            $statusCode = 200;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }
}
