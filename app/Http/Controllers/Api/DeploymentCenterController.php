<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Models\Ticket;
use App\Services\InitiativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeploymentCenterController extends Controller
{
    public function index(Request $request)
    {
        $testDeploymentInitiative = Initiative::select(
            'id',
            'name',
            'client_id'
        )
            ->with(['client' => function ($query) {
                $query->select('id', 'name');
            }])
            // ->with(['tickets' => function ($query) {
            //     $query->select('id', 'initiative_id', 'name')
            //         ->readyForTestStatus();
            // }])
            ->withCount(['tickets' => function ($query) {
                $query->readyForTestStatus();
            }])
            ->whereHas('tickets', function ($query) {
                $query->readyForTestStatus();
            })
            ->get();
        $data = [
            'testDeploymentInitiatives' => $testDeploymentInitiative
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
}
