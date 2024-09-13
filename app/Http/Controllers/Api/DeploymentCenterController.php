<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Models\Release;
use App\Models\ReleaseTicket;
use App\Models\Ticket;
use App\Services\InitiativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeploymentCenterController extends Controller
{
    public function index(Request $request)
    {
        $productionDeploymentInitiative = InitiativeService::getInitiativeWithProductionDeploymentTickets();
        $acceptanceDeploymentInitiative = InitiativeService::getInitiativeWithAcceptanceDeploymentTickets();
        $testDeploymentInitiative = InitiativeService::getInitiativeWithTestDeploymentTickets();
        $data = [
            'productionDeploymentInitiatives' => $productionDeploymentInitiative,
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

        $status = true;
        $message = __('message.home.deployment_center.test_deployment.update_status_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            Ticket::whereIn('id', $request->input('ticketIds'))->update(['status' => Ticket::getStatusOngoing()]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }

    public function getProductionDeploymentTicketsModalData(Request $request, $initiativeId)
    {
        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request, $initiativeId);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $status = true;
        $ticket = ReleaseTicket::with('ticket', 'release')
            ->whereHas('release', function ($query) use ($initiativeId) {
                $query->where('initiative_id', $initiativeId);
            })
            ->whereHas('ticket', function ($query) use ($initiativeId) {
                $query->where('status', Ticket::getStatusReadyForPRD());
            })
            ->get();
        $data = [
            'tickets' => $ticket,
            'release' => $initiative->unprocessedRelease
        ];
        return ApiHelper::response($status, '', $data, 200);
    }

    public function submitProductionDeploymentTicket(Request $request, $initiativeId)
    {
        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request, $initiativeId);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $release = Release::find($request->input('release_id'));
        if (!$release) {
            return ApiHelper::response($status, __('messages.solution_design.section.release_not_exist'), '', 400);
        }

        $status = true;
        $message = __('message.home.deployment_center.production_deployment.update_status_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            Ticket::whereIn('id', $request->input('ticketIds'))->update(['status' => Ticket::getStatusDone()]);
            $release->update(['status' => Release::PROCESSED_RELEASE]);
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
