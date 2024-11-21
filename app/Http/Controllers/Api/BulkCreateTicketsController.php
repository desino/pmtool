<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BulkCreateTicketsRequest;
use App\Models\Functionality;
use App\Models\Initiative;
use App\Models\Section;
use App\Models\Ticket;
use App\Models\TicketAction;
use App\Services\AsanaService;
use App\Services\InitiativeService;
use App\Services\TicketService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkCreateTicketsController extends Controller
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
            return ApiHelper::response(false, __('messages.initiative.dont_have_permission'), null, 404);
        }
        $initiative = Initiative::find($request->initiative_id);
        if (!$initiative) {
            return ApiHelper::response(false, __('messages.solution_design.section.initiative_not_exist'), '', 404);
        }

        $sectionsWithFunctionalities = Section::select(
            'id',
            'initiative_id',
            'display_name',
        )
            ->with([
                'functionalities' => function ($q) {
                    $q->select(
                        'id',
                        'section_id',
                        'name',
                        'display_name',
                        DB::raw('"" AS clarify_estimate_checked'),
                        DB::raw('"" AS initial_estimation_development_time'),
                    )
                        ->withCount('tickets')
                        ->having('tickets_count', 0);
                }
            ])
            ->where('initiative_id', $initiative->id)
            ->whereHas('functionalities', function ($q) {
                $q->withCount('tickets')
                    ->having('tickets_count', 0);
            })
            ->get();
        $retData = [
            'initiative' => $initiative,
            'sectionsWithFunctionalities' => $sectionsWithFunctionalities
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }

    public function storeNewBulkTickets(BulkCreateTicketsRequest $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.initiative.dont_have_permission'), null, 404);
        }
        $status = false;
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $functionalityIds = array_unique(array_merge(...array_map(function ($section) {
            return array_column($section['functionality'], 'functionality_id');
        }, $request->sections)));

        $functionalities = Functionality::whereIn('id', $functionalityIds)->get();
        if ($functionalities->count() != count($functionalityIds)) {
            return ApiHelper::response($status, __('messages.solution_design.section.functionality_not_exist'), '', 400);
        }

        $status = true;
        $message = __('messages.ticket.bulk_tickets_created_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            foreach ($request->sections as $section) {
                foreach ($section['functionality'] as $functionality) {
                    if ($functionality['initial_estimation_development_time'] != '') {
                        $generateTicketComposedNameData = TicketService::generateTicketComposedName($initiative->id, $functionality['functionality_name'], Ticket::TYPE_DEVELOPMENT);
                        $ticketComposedName = $generateTicketComposedNameData['composed_name'];
                        $insertTicket['initiative_id'] = $initiative->id;
                        $insertTicket['functionality_id'] = $functionality['functionality_id'];
                        $insertTicket['name'] = $functionality['functionality_name'];
                        $insertTicket['composed_name'] = $ticketComposedName;
                        $insertTicket['type'] = Ticket::TYPE_DEVELOPMENT;
                        $insertTicket['initial_estimation_development_time'] = $functionality['initial_estimation_development_time'];
                        $insertTicket['auto_wait_for_client_approval'] = true;
                        $data = [
                            'name' => $ticketComposedName,
                            'resource_type' => 'task',
                            'resource_subtype' => 'default_task',
                        ];
                        $projectId = $initiative->asana_project_id;
                        $task = $this->asanaService->createTask($projectId, $data);
                        $insertTicket['asana_task_id'] = $task['data']['data']['gid'];
                        $ticket = Ticket::create($insertTicket);

                        $actions = [
                            [
                                'action' => TicketAction::getActionDevelop(),
                                'user_id' => Auth::id(),
                                'is_checked' => 1
                            ]
                        ];
                        $clarifyEstimateAction = [];
                        if ($functionality['clarify_estimate_checked']) {
                            $clarifyEstimateAction = [
                                'action' => TicketAction::getActionClarifyAndEstimate(),
                                'user_id' => $initiative->technical_owner_id ?? Auth::id(),
                                'is_checked' => 1
                            ];
                            array_push($actions, $clarifyEstimateAction);
                        }
                        TicketService::insertTicketActions($ticket->id, $actions, true);
                        TicketService::updateTicketStatus($ticket);
                        TicketService::createMacroStatusAndUpdateTicket($ticket);
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $status = false;
            $message = $e->getMessage();
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }
}
