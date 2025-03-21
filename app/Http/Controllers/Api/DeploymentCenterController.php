<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Models\Logging;
use App\Models\Release;
use App\Models\ReleaseTicket;
use App\Models\Ticket;
use App\Models\TicketAction;
use App\Services\InitiativeService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $retTickets = collect([]);
        $tickets = Ticket::select('id', 'initiative_id', 'name', 'composed_name')
            ->with([
                'actions' => function ($q) {
                    $q->select('ticket_id', 'user_id', 'action');
                },
                'developAction' => function ($q) {
                    $q->select('ticket_id', 'user_id', 'action');
                    $q->with([
                        'user' => function ($q) {
                            $q->select('id', 'name');
                        },
                    ]);
                },
            ])
            ->where('initiative_id', $initiativeId)
            ->readyForTestStatus()
            ->get();

        $isAllowToShowTickets = false;
        if ($initiative->technical_owner_id == Auth::id() || Auth::user()->is_admin) {
            $isAllowToShowTickets = true;
        } else {
            foreach ($tickets as $ticket) {
                $developAction = $ticket->actions->where('action', TicketAction::getActionDevelop())->first();
                if ($developAction && $developAction->user_id == Auth::id()) {
                    $isAllowToShowTickets = true;
                }
            };
        }
        if ($isAllowToShowTickets) {
            $retTickets = $tickets;
        }
        $initiativeData = array(
            'id' => $initiative->id,
            'name' => $initiative->name,
        );
        $retTickets = $retTickets->transform(function ($ticket) {
            $ticket->makeHidden(['actions',  'initiative']);
            return $ticket;
        });
        $data = [
            'tickets' => $retTickets,
            'initiative' => $initiativeData,
            'isAllowProcess' => $initiative->technical_owner_id == Auth::id() || Auth::user()->is_admin ?? false,
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

        if ($initiative->technical_owner_id != Auth::id() && !Auth::user()->is_admin) {
            return ApiHelper::response($status, __('messages.home.deployment_center.test_deployment.no_permission'), '', 400);
        }

        $tickets = Ticket::whereIn('id', $request->input('ticketIds'))->get();
        $filteredTickets = $tickets->filter(function ($ticket) {
            return $ticket->status != Ticket::getStatusReadyForTest() || $ticket->macro_status != Ticket::MACRO_STATUS_TEST_WAIT_FOR_DEPLOYMENT_TO_TEST;
        });
        if ($filteredTickets->count() > 0) {
            return ApiHelper::response($status, __('messages.home.deployment_center.test_deployment.not_allow_process_ticket'), '', 400);
        }

        DB::beginTransaction();
        $status = true;
        $message = __('message.home.deployment_center.test_deployment.update_status_success');
        $statusCode = 200;
        try {
            foreach ($tickets as $ticket) {
                $ticket->status = Ticket::getStatusOngoing();
                $ticket->save();
                TicketService::createMacroStatusAndUpdateTicket($ticket);
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

    public function getAcceptanceDeploymentTicketsModalData(Request $request, $initiativeId)
    {
        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request, $initiativeId);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $status = true;
        $retTickets = collect([]);
        $tickets = Ticket::select('id', 'initiative_id', 'name', 'composed_name')
            ->with([
                'actions' => function ($q) {
                    $q->select('ticket_id', 'user_id', 'action');
                },
                'developAction' => function ($q) {
                    $q->select('ticket_id', 'user_id', 'action');
                    $q->with([
                        'user' => function ($q) {
                            $q->select('id', 'name');
                        },
                    ]);
                },
            ])
            ->where('initiative_id', $initiativeId)
            ->readyForAcceptanceStatus()
            ->get();

        $isAllowToShowTickets = false;
        if ($initiative->technical_owner_id == Auth::id() || Auth::user()->is_admin) {
            $isAllowToShowTickets = true;
        } else {
            foreach ($tickets as $ticket) {
                $developAction = $ticket->actions->where('action', TicketAction::getActionDevelop())->first();
                if ($developAction && $developAction->user_id == Auth::id()) {
                    $isAllowToShowTickets = true;
                }
            };
        }
        if ($isAllowToShowTickets) {
            $retTickets = $tickets;
        }
        $initiativeData = array(
            'id' => $initiative->id,
            'name' => $initiative->name,
        );
        $retTickets = $retTickets->transform(function ($ticket) {
            $ticket->makeHidden(['actions',  'initiative']);
            return $ticket;
        });
        $data = [
            'tickets' => $retTickets,
            'initiative' => $initiativeData,
            'isAllowProcess' => $initiative->technical_owner_id == Auth::id() || Auth::user()->is_admin ?? false,
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

        if ($initiative->technical_owner_id != Auth::id() && !Auth::user()->is_admin) {
            return ApiHelper::response($status, __('messages.home.deployment_center.acceptance_deployment.no_permission'), '', 400);
        }

        $tickets = Ticket::whereIn('id', $request->input('ticketIds'))->get();
        $filteredTickets = $tickets->filter(function ($ticket) {
            return $ticket->status != Ticket::getStatusReadyForACC() || $ticket->macro_status != Ticket::MACRO_STATUS_VALIDATE_WAITING_FOR_DEPLOYMENT_TO_ACC;
        });
        if ($filteredTickets->count() > 0) {
            return ApiHelper::response($status, __('messages.home.deployment_center.acceptance_deployment.not_allow_process_ticket'), '', 400);
        }

        $status = true;
        $message = __('message.home.deployment_center.test_deployment.update_status_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            foreach ($tickets as $ticket) {
                $ticket->status = Ticket::getStatusOngoing();
                $ticket->save();
                TicketService::createMacroStatusAndUpdateTicket($ticket);
                TicketService::storeLogging($ticket, Logging::ACTIVITY_TYPE_DEPLOYMENT);
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

    public function getProductionDeploymentTicketsModalData(Request $request, $initiativeId)
    {
        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request, $initiativeId);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $status = true;
        $retTickets = collect([]);
        $releaseTickets = ReleaseTicket::with([
            'ticket' => function ($q) {
                $q->select('id', 'initiative_id', 'name', 'composed_name');
                $q->with(
                    [
                        'actions',
                        'developAction' => function ($q) {
                            $q->select('ticket_id', 'user_id', 'action');
                            $q->with([
                                'user' => function ($q) {
                                    $q->select('id', 'name');
                                },
                            ]);
                        },
                    ]
                );
            },
            'release'
        ])
            ->whereHas('release', function ($query) use ($initiativeId) {
                $query->where('initiative_id', $initiativeId);
            })
            ->whereHas('ticket', function ($query) use ($initiativeId) {
                $query->where('status', Ticket::getStatusReadyForPRD());
            })
            ->get();

        if ($initiative->technical_owner_id == Auth::id() || Auth::user()->is_admin) {
            $retTickets = $releaseTickets;
        } else {
            $isAllowToShowTickets = false;
            foreach ($releaseTickets as $releaseTicket) {
                $developAction = $releaseTicket->ticket->actions->where('action', TicketAction::getActionDevelop())->first();
                if ($developAction && $developAction->user_id == Auth::id()) {
                    $isAllowToShowTickets = true;
                    break;
                }
            }
            if ($isAllowToShowTickets) {
                $retTickets = $releaseTickets;
            }
        }
        $initiativeData = array(
            'id' => $initiative->id,
            'name' => $initiative->name,
        );
        $retTickets->transform(function ($releaseTicket) {
            $releaseTicket->ticket->makeHidden(['actions', 'initiative']);
            return $releaseTicket;
        });
        $data = [
            'tickets' => $retTickets,
            'release' => $initiative->unprocessedRelease,
            'initiative' => $initiativeData,
            'isAllowProcess' => $initiative->technical_owner_id == Auth::id() || Auth::user()->is_admin ?? false,
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

        if ($initiative->technical_owner_id != Auth::id() && !Auth::user()->is_admin) {
            return ApiHelper::response($status, __('messages.home.deployment_center.production_deployment.no_permission'), '', 400);
        }

        $tickets = Ticket::whereIn('id', $request->input('ticketIds'))->get();
        $filteredTickets = $tickets->filter(function ($ticket) {
            return $ticket->status != Ticket::getStatusReadyForPRD() || $ticket->macro_status != Ticket::MACRO_STATUS_READY_FOR_DEPLOYMENT_TO_PRD;
        });
        if ($filteredTickets->count() > 0) {
            return ApiHelper::response($status, __('messages.home.deployment_center.production_deployment.not_allow_process_ticket'), '', 400);
        }

        $status = true;
        $message = __('message.home.deployment_center.production_deployment.update_status_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {

            foreach ($tickets as $ticket) {
                $ticket->status = Ticket::getStatusDone();
                $ticket->save();
                TicketService::createMacroStatusAndUpdateTicket($ticket);
                TicketService::storeLogging($ticket, Logging::ACTIVITY_TYPE_MARKED_AS_DONE);
            }
            $release->update(['status' => Release::PROCESSED_RELEASE, 'processed_by' => Auth::id(), 'processed_at' => Carbon::now()]);
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
}
