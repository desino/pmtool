<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AssignProjectRequest;
use App\Http\Requests\Api\CreateReleaseRequest;
use App\Http\Requests\Api\SaveTaskDescriptionRequest;
use App\Http\Requests\Api\TicketDetailEstimatedHoursRequest;
use App\Http\Requests\Api\TicketRequest;
use App\Http\Requests\UpdateReleaseNoteRequest;
use App\Models\Functionality;
use App\Models\Logging;
use App\Models\Project;
use App\Models\Release;
use App\Models\ReleaseTicket;
use App\Models\Section;
use App\Models\Ticket;
use App\Models\TicketAction;
use App\Services\AsanaService;
use App\Services\InitiativeService;
use App\Services\ProjectService;
use App\Services\ReleaseService;
use App\Services\TicketService;
use Exception;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    protected AsanaService $asanaService;

    public function __construct(AsanaService $asanaService)
    {
        $this->asanaService = $asanaService;
    }

    public function getInitialDataForCreateOrEditTicket(int $initiative_id)
    {
        $sectionFunctionality = TicketService::getSectionFunctionality($initiative_id);
        $ticketTypes = TicketService::getTicketTypes();
        $projects = TicketService::getInitiativeProject($initiative_id);
        $users = TicketService::getUsers();
        $initiative = TicketService::getInitiative($initiative_id);
        $actions = TicketService::getTicketActionWithDefaultData(TicketAction::getAllActions(), $initiative);

        $retData = [
            'sectionFunctionality' => $sectionFunctionality,
            'ticketTypes' => $ticketTypes,
            'projects' => $projects,
            'users' => $users,
            'actions' => $actions,
            'initiative' => $initiative,
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }

    public function store(TicketRequest $request)
    {
        $validateData = $request->validated();
        $requestData = $request->all();
        $status = false;
        $retData = [
            'ticket' => "",
        ];

        $initiative = InitiativeService::getInitiative($request);

        $authUser = Auth::user();
        if (!$authUser->is_admin && $initiative->functional_owner_id != $authUser->id && $initiative->technical_owner_id != $authUser->id) {
            return ApiHelper::response(false, __('messages.ticket.dont_have_permission'), null, 400);
        }

        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }

        $getAsanaProject = $this->asanaService->getProject(trim($initiative->asana_project_id));
        if ($getAsanaProject['error_status']) {
            return ApiHelper::response($status, __('messages.asana.project_does_not_exist'), '', 500);
        }

        $filteredActionDevelop = array_filter($validateData['ticket_actions'], function ($action) {
            return $action['is_checked'] == true && $action['action'] === TicketAction::getActionDevelop();
        });
        if (empty($filteredActionDevelop)) {
            return ApiHelper::response($status, __('messages.create_ticket.action_develop_not_exist'), '', 400);
        }

        $ticketTypes = Ticket::getAllTypes();
        $projectId = $initiative->asana_project_id;
        $generateTicketComposedNameData = TicketService::generateTicketComposedName($validateData['initiative_id'], $validateData['name'], $validateData['type']);
        $ticketComposedName = $generateTicketComposedNameData['composed_name'];
        $validateData['composed_name'] = $ticketComposedName;
        $data = [
            'name' => $validateData['composed_name'],
            'resource_type' => 'task',
            'resource_subtype' => 'default_task',
        ];
        $task = $this->asanaService->createTask($projectId, $data);
        if ($task['error_status']) {
            return ApiHelper::response($status, __('messages.asana.create_ticket.store_error'), '', 500);
        }
        $validateData['asana_task_id'] = $task['data']['data']['gid'];

        $status = true;
        $message = __('messages.create_ticket.store_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $ticket = Ticket::create($validateData);
            if (!empty($validateData['ticket_actions'])) {
                TicketService::insertTicketActions($ticket->id, $validateData['ticket_actions'], $validateData['auto_wait_for_client_approval']);
                TicketService::updateTicketStatus($ticket);
                TicketService::createMacroStatusAndUpdateTicket($ticket);
            }
            $retData = [
                'ticket' => $ticket,
                'asanaTaskData' => $task['data']['data'],
            ];
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, $retData, $statusCode);
    }

    public function updateTicket(TicketRequest $request, $initiativeId, $ticketId)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.initiative.dont_have_permission'), null, 400);
        }
        $validateData = $request->validated();
        $status = false;
        $retData = [
            'ticket' => "",
        ];

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.section.client_not_exist'), '', 400);
        }
        $ticket = Ticket::find($ticketId);
        if (!$ticket) {
            return ApiHelper::response($status, __('messages.ticket.not_found'), '', 400);
        }

        $getAsanaProject = $this->asanaService->getProject(trim($initiative->asana_project_id));
        if ($getAsanaProject['error_status']) {
            return ApiHelper::response($status, __('messages.asana.project_does_not_exist'), '', 500);
        }

        $filteredActionDevelop = array_filter($validateData['ticket_actions'], function ($action) {
            return $action['is_checked'] == true && $action['action'] === TicketAction::getActionDevelop();
        });
        if (empty($filteredActionDevelop)) {
            return ApiHelper::response($status, __('messages.create_ticket.action_develop_not_exist'), '', 400);
        }

        // $ticketActionsCollect = collect($validateData['ticket_actions']);
        $ticketActionsCollect = $ticket->actions;
        $filteredDoneActions = $ticketActionsCollect->where('status', TicketAction::getStatusDone())->sortByDesc('action');
        $filteredDoneMaxAction = $filteredDoneActions->first();
        $filterNewMaxAction = collect($validateData['ticket_actions'])->where('is_checked', true)->whereNull('status')->sortBy('action')->first();

        if (isset($filterNewMaxAction['action']) && isset($filteredDoneMaxAction['action']) && $filterNewMaxAction['action'] < $filteredDoneMaxAction['action']) {
            return ApiHelper::response($status, __('messages.create_ticket.not_allowed_because_grater_action_is_done'), '', 400);
        }

        $ticketDoneActionCount = $ticket->actions->where('status', TicketAction::getStatusDone())->count();
        $filteredDoneActionsCount = $filteredDoneActions->count();
        if ($ticketDoneActionCount != $filteredDoneActionsCount) {
            return ApiHelper::response($status, __('messages.create_ticket.not_allowed_because_done_actions_not_match'), '', 400);
        }

        foreach ($validateData['ticket_actions'] as $action) {
            $chkDoneAction = $ticket->actions->where('action', $action['action'])->where('status', TicketAction::getStatusDone())->where('user_id', '!=', $action['user_id'])->first();
            if (!empty($chkDoneAction)) {
                return ApiHelper::response($status, __('messages.create_ticket.you_cant_update_ticket_action_user_because_this_action_done'), '', 400);
            }
        }

        $projectId = $initiative->asana_project_id;
        $generateTicketComposedNameData = TicketService::updateTicketComposedName($ticket, $validateData['name'], $validateData['type']);
        $ticketComposedName = $generateTicketComposedNameData['composed_name'];
        $validateData['composed_name'] = $ticketComposedName;

        if ($validateData['name'] != $ticket->name || $validateData['type'] != $ticket->type) {
            $data = [
                'name' => $validateData['composed_name'],
                'resource_type' => 'task',
                'resource_subtype' => 'default_task',
            ];
            $task = $this->asanaService->updateTask($ticket->asana_task_id, $data);
            if ($task['error_status']) {
                return ApiHelper::response($status, __('messages.asana.update_ticket.update_error'), '', 500);
            }
            $validateData['asana_task_id'] = $task['data']['data']['gid'];
        }

        $status = true;
        $message = __('messages.create_ticket.update_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $ticket->update($validateData);

            if (!empty($validateData['ticket_actions'])) {
                // TicketService::deleteActions($ticket->id);
                TicketService::insertTicketActions($ticket->id, $validateData['ticket_actions'], $validateData['auto_wait_for_client_approval']);
                TicketService::updateTicketStatus($ticket);
                TicketService::createMacroStatusAndUpdateTicket($ticket, true);
            }
            $retData = [
                'ticket' => $ticket,
                // 'asanaTaskData' => $task['data']['data'],
            ];
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, $retData, $statusCode);
    }

    public function index($initiative_id, Request $request)
    {

        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.initiative.dont_have_permission'), null, 404);
        }
        $initiative = InitiativeService::getInitiative($request, $initiative_id);
        if (!$initiative) {
            return ApiHelper::response(false, __('messages.solution_design.section.initiative_not_exist'), '', 404);
        }
        $filters = $request->filters;

        $deploymentId = "";
        if ($filters['deployment_id'] != '') {
            $deploymentId = $filters['deployment_id'];
        }
        $release = Release::where('initiative_id', $initiative_id)->find($deploymentId);
        if (!$release && $deploymentId != "") {
            return ApiHelper::response(false, __('messages.release.not_found'), null, 404);
        }

        $tickets = Ticket::select(
            'id',
            'name',
            'type',
            'project_id',
            'created_at',
            'created_by',
            'asana_task_id',
            'functionality_id',
            'initiative_id',
            'composed_name',
            'status',
            'macro_status',
            'is_priority',
            'is_visible',
            'initial_estimation_development_time',
            'dev_estimation_time',
            'moved_back_to_dev_action_count',
            DB::RAW('IF(dev_estimation_time > 0, dev_estimation_time,initial_estimation_development_time) as estimation_time'),
            DB::RAW('IF(macro_status = ' . Ticket::MACRO_STATUS_DONE . ', true,false) as is_ticket_done'),
        )
            ->with([
                'project' => function ($q) {
                    $q->select(
                        'id',
                        'initiative_id',
                        'name',
                    );
                },
                'functionality' => function ($q) {
                    $q->select(
                        'id',
                        'section_id',
                    );
                    $q->with(['section' => function ($q) {
                        $q->select(
                            'id',
                            'initiative_id',
                        );
                        $q->with(['initiative' => function ($q) {
                            $q->select(
                                'id',
                                'asana_project_id'
                            );
                        }]);
                    }]);
                },
                'initiative' => function ($q) {
                    $q->select('id', 'client_id', 'name', 'asana_project_id')
                        ->with(['client' => function ($q) {
                            $q->select('id', 'name');
                        }]);
                },
                'currentAction' => function ($q) {
                    $q->select('id', 'ticket_id', 'action', 'status', 'user_id')
                        ->with(['user' => function ($q) {
                            $q->select('id', 'name');
                        }]);
                },
                'releaseTickets' => function ($q) {
                    $q->select('id', 'release_id', 'ticket_id');
                },
                'actions' => function ($q) {
                    $q->select('id', 'ticket_id', 'action', 'status', 'user_id');
                },
                'timeBookings' => function ($q) {
                    $q->select('id', 'ticket_id', 'booked_date');
                }
            ])
            ->withCount([
                'actions',
                'doneActions',
            ])
            ->where('initiative_id', $initiative_id)
            ->when($deploymentId != '', function ($query) use ($deploymentId) {
                // $query->whereIn('id', function ($query) use ($deploymentId) {
                //     $query->select('ticket_id')
                //         ->from('release_tickets')
                //         ->where('release_id', $deploymentId);
                // });
                $query->whereHas('releaseTickets', function ($query) use ($deploymentId) {
                    $query->where('release_id', $deploymentId);
                });
            })
            ->when($filters['task_name'] != '', function (Builder $query) use ($filters) {
                $query->whereLike('composed_name', '%' . $filters['task_name'] . '%');
            })->when($filters['task_type'] != '', function (Builder $query) use ($filters) {
                $query->where('type', $filters['task_type']);
            })
            ->when(!empty($filters['functionalities']), function (Builder $query) use ($filters) {
                $query->whereIn('functionality_id', array_column($filters['functionalities'], 'id'));
            })
            ->when(!empty($filters['is_open_task']) && $filters['is_open_task'] == 'true', function (Builder $query) use ($filters) {
                $query->where('status', '!=', Ticket::getStatusDone());
            })
            ->when(!empty($filters['projects']) != '', function (Builder $query) use ($filters) {
                $exceptProjects = array_filter($filters['projects'], fn($v) => $v['id'] != 0);
                $notAllocatedProjects = array_filter($filters['projects'], fn($v) => $v['id'] == 0);

                $query->where(function ($query) use ($notAllocatedProjects, $exceptProjects) {
                    $query->whereIn('project_id', array_column($exceptProjects, 'id'))
                        ->when(count($notAllocatedProjects) > 0, function ($query) {
                            $query->orWhereNull('project_id');
                        });
                });
            })
            ->when(!empty($filters['action_owner']) != '', function (Builder $query) use ($filters) {
                $query->whereHas('actions', function ($query) use ($filters) {
                    $query->where('user_id', $filters['action_owner'])
                        ->where('action', function ($subQuery) {
                            $subQuery->selectRaw('MIN(action)')
                                ->from('ticket_actions as ta_inner')
                                ->whereColumn('ta_inner.ticket_id', 'ticket_actions.ticket_id')
                                ->where('status', '!=', TicketAction::getStatusDone())
                                ->groupBy('action')
                                ->orderBy('action', 'ASC')
                                ->limit(1);
                        });
                });
            })
            ->when(!empty($filters['next_action_owner']) != '', function (Builder $query) use ($filters) {
                $query->whereHas('actions', function ($query) use ($filters) {
                    $query->where('user_id', $filters['next_action_owner'])
                        ->where('action', function ($subQuery) {
                            $subQuery->selectRaw('action')
                                ->from('ticket_actions as ta_inner')
                                ->whereColumn('ta_inner.ticket_id', 'ticket_actions.ticket_id')
                                ->where('status', '!=', TicketAction::getStatusDone())
                                ->groupBy('action')
                                ->orderBy('action', 'ASC')
                                ->skip(1)
                                ->take(1);
                        });
                });
            })
            ->when(!empty($filters['macro_status']) != '', function (Builder $query) use ($filters) {
                $query->whereIn('macro_status', array_column($filters['macro_status'], 'id'));
            })
            ->when($filters['is_include_done'] == 'false', function (Builder $query) use ($filters) {
                $query->where('macro_status', '!=', Ticket::MACRO_STATUS_DONE);
            })
            ->get()->each(function ($ticket) {
                $ticketDoneActions = $ticket->actions->where('status', TicketAction::getStatusDone());
                $ticket->is_show_delete_btn = Auth::user()->is_admin && $ticket->timeBookings->count() == 0 && $ticketDoneActions->count() == 0 && $ticket->moved_back_to_dev_action_count == 0 ?? false;
            });
        $meta['task_type'] = Ticket::getAllTypes();
        $meta['functionalities'] = Functionality::whereHas('section', function ($query) use ($initiative_id) {
            $query->where('initiative_id', $initiative_id);
        })->get(['id', 'display_name']);

        $projects = ProjectService::getInitiativeProjects($initiative_id);

        $meta['projects'] = $projects;
        $meta['users'] = TicketService::getUsers();
        $meta['initiative'] = TicketService::getInitiative($initiative_id);
        $meta['macro_status'] = Ticket::getAllMacroStatus();
        $meta['deployments'] = Release::getAllReleases($initiative_id);
        $meta['prd_macro_status'] = Ticket::MACRO_STATUS_READY_FOR_DEPLOYMENT_TO_PRD;
        $meta['ticket_count'] = $tickets->count();
        $meta['ticket_sum'] = $tickets->sum('estimation_time');

        return ApiHelper::response('false', __('messages.ticket.fetched'), $tickets, 200, $meta);
    }

    public function editTicket(Request $request, $initiative_id, $ticket_id)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.initiative.dont_have_permission'), null, 400);
        }
        $status = false;
        $initiative = InitiativeService::getInitiative($request, $initiative_id);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        $ticket = Ticket::with('functionality', 'actions')->find($ticket_id);
        if (!$ticket) {
            return ApiHelper::response($status, __('messages.ticket.not_found'), [], 404);
        }

        $sectionFunctionality = TicketService::getSectionFunctionality($initiative_id);
        $ticketTypes = TicketService::getTicketTypes();
        $projects = TicketService::getInitiativeProject($initiative_id);
        $users = TicketService::getUsers();
        $initiative = TicketService::getInitiative($initiative_id);
        $actions = TicketService::getTicketActionWithDefaultData(TicketAction::getAllActions(), $initiative, $ticket);
        $retData = [
            'ticket' => $ticket,
            'sectionFunctionality' => $sectionFunctionality,
            'ticketTypes' => $ticketTypes,
            'projects' => $projects,
            'users' => $users,
            'actions' => $actions,
            'initiative' => $initiative,
        ];
        return ApiHelper::response($status, __('messages.ticket.fetched'), $retData, 200);
    }

    /**
     * Display the specified ticket for the given initiative.
     *
     * @param  int  $initiative_id
     * @param  int  $ticket_id
     * @return JsonResponse
     */
    public function show(Request $request, int $initiative_id, int $ticket_id): JsonResponse
    {
        $status = false;

        $initiative = InitiativeService::getInitiative($request, $initiative_id);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $ticket = Ticket::select(
            'id',
            'asana_task_id',
            'functionality_id',
            'initiative_id',
            'name',
            'composed_name',
            'type',
            'initial_estimation_development_time',
            'dev_estimation_time',
            'release_note',
            'description',
            'auto_wait_for_client_approval',
            'status',
            'macro_status',
            DB::RAW('IF(dev_estimation_time > 0, dev_estimation_time,initial_estimation_development_time) as estimation_time')
        )
            ->with([
                'functionality' => function ($q) {
                    $q->select('id', 'section_id', 'name', 'display_name', 'description');
                },
                'initiative' => function ($q) {
                    $q->select('id', 'name', 'client_id', 'functional_owner_id', 'quality_owner_id', 'technical_owner_id', 'asana_project_id')
                        ->with([
                            'client' => function ($q) {
                                $q->select('id', 'name');
                            }
                        ]);
                },
                'initiative.functionalOwner' => function ($q) {
                    $q->select('id', 'name');
                },
                'initiative.qualityOwner' => function ($q) {
                    $q->select('id', 'name');
                },
                'initiative.technicalOwner',
                'currentAction' => function ($q) {
                    $q->select('id', 'ticket_id', 'user_id', 'action', 'status')
                        ->with(['user' => function ($q) {
                            $q->select('id', 'name');
                        }]);
                },
                'nextAction' => function ($q) {
                    $q->select('id', 'ticket_id', 'user_id', 'action', 'status')
                        ->with(['user' => function ($q) {
                            $q->select('id', 'name');
                        }]);
                },
                'previousAction' => function ($q) {
                    $q->select('id', 'ticket_id', 'user_id', 'action', 'status')
                        ->with(['user' => function ($q) {
                            $q->select('id', 'name');
                        }]);
                },
                'testCases' => function ($q) {
                    $q->select('id', 'ticket_id', 'expected_behaviour', 'observations', 'owner_id', 'status');
                },
                'actions' => function ($q) {
                    $q->select('id', 'ticket_id', 'user_id', 'action', 'status');
                },
            ])
            ->withCount([
                'actions' => function ($query) {
                    $query->where('user_id', Auth::id());
                }
            ])
            ->where([
                ['id', '=', $ticket_id],
                ['initiative_id', '=', $initiative_id]
            ])
            ->first();

        $ticket->is_allow_dev_estimation_time = Ticket::isAllowDevEstimationTime($ticket->currentAction);
        $ticket->is_show_pre_action_btn = Ticket::isShowPreActionBtn($ticket->previousAction, $ticket->macro_status, $ticket->currentAction, $ticket->initiative_id, $ticket->initiative);
        $ticket->is_disable_action_user = Ticket::isDisableActionUser($ticket->initiative_id, $ticket->initiative);
        $ticket->is_enable_mark_as_done_btn = Ticket::isEnableMarkAsDoneBtn($ticket->status);
        $ticket->is_show_mark_as_done_btn = Ticket::isShowMarkAsDoneBtn($ticket->initiative_id, $ticket->initiative, $ticket->macro_status, $ticket->currentAction);

        // If the ticket is not found, return a 404 response
        if (!$ticket) {
            return ApiHelper::response(false, __('messages.ticket.not_found'), [], 404);
        }

        $testAction = $ticket->actions->where('action', TicketAction::getActionTest());
        $isAllowCaseAddTestSection = false;
        if ($ticket->macro_status != Ticket::MACRO_STATUS_DONE) {
            if ($testAction->count() > 0 && (
                $initiative->functional_owner_id == Auth::id() ||
                $initiative->technical_owner_id == Auth::id() ||
                ($ticket->macro_status == Ticket::MACRO_STATUS_TEST
                    && (
                        $ticket->currentAction->user_id == Auth::id() ||
                        $initiative->quality_owner_id == Auth::id()
                    )
                )
            )) {
                $isAllowCaseAddTestSection = true;
            } else if ($initiative->functional_owner_id == Auth::id() || $initiative->technical_owner_id == Auth::id()) {
                $isAllowCaseAddTestSection = true;
            }
        }

        $isAllowCaseUpdateTestSection = false;
        if ($ticket->macro_status == Ticket::MACRO_STATUS_TEST && ($ticket->currentAction->user_id == Auth::id() || $initiative->quality_owner_id == Auth::id())) {
            $isAllowCaseUpdateTestSection = true;
        }


        $actionsUserIds = $ticket->actions->pluck('user_id');
        $authUser = Auth::user();
        if (!$authUser->is_admin && !$actionsUserIds->contains($authUser->id) && !$ticket->is_visible) {
            return ApiHelper::response(false, __('messages.initiative.dont_have_permission'), null, 404);
        }

        // Fetch all tickets for the given initiative
        $allTickets = Ticket::select(
            'tickets.id',
            'tickets.name',
            'tickets.composed_name'
        )
            ->where('initiative_id', $initiative_id)
            ->when(!Auth::user()->is_admin, function (Builder $query) {
                $query->where('tickets.is_visible', 1)
                    ->whereHas('actions', function ($query) {
                        $query->where('user_id', Auth::id())
                            ->where('action', function ($subQuery) {
                                $subQuery->selectRaw('MIN(action)')
                                    ->from('ticket_actions as ta_inner')
                                    ->whereColumn('ta_inner.ticket_id', 'ticket_actions.ticket_id')
                                    ->where('status', '!=', TicketAction::getStatusDone())
                                    ->groupBy('action')
                                    ->orderBy('action', 'ASC')
                                    ->limit(1);
                            });
                    });
            })
            ->get();
        $meta_data['all_tickets'] = $allTickets;
        $meta_data['users'] = TicketService::getUsers();
        $meta_data['action_status'] = TicketAction::getAllActionStatus();
        $meta_data['is_allow_case_add_test_section'] = $isAllowCaseAddTestSection;
        $meta_data['is_allow_case_update_test_section'] = $isAllowCaseUpdateTestSection;
        $meta_data['actions'] = $ticket->actions;
        // Return the ticket and related meta data in a success response
        return ApiHelper::response(true, __('messages.ticket.fetched'), $ticket, 200, $meta_data);
    }

    public function updateReleaseNote($initiative_id, $ticket_id, UpdateReleaseNoteRequest $request)
    {
        $ticket = Ticket::with('functionality')->find($ticket_id);
        if (!$ticket) {
            return ApiHelper::response('false', __('messages.ticket.not_found'), [], 404);
        }

        $requestData = $request->validated();

        $status = true;
        $code = 200;
        $message = __('messages.ticket.update_success');

        try {
            DB::transaction(function () use ($ticket, $requestData) {
                $ticket->update($requestData);
            });
        } catch (Exception $e) {
            Log::error('Update Release Note Failed: ' . $e->getMessage());

            $status = false;
            $code = 500;
            $message = __('messages.ticket.update_failed');
        }

        return ApiHelper::response($status, $message, $ticket, $code);
    }

    public function saveTaskDescription($initiativeId, $ticketId, SaveTaskDescriptionRequest $request)
    {
        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $ticket = Ticket::find($ticketId);
        if (!$ticket) {
            return ApiHelper::response($status, __('messages.ticket.ticket_not_exist'), '', 400);
        }
        if (!Auth::user()->is_admin && $initiative->functional_owner_id != Auth::id() && $initiative->technical_owner_id != Auth::id()) {
            return ApiHelper::response($status, __('messages.ticket.dont_have_permission'), '', 400);
        }
        $requestData = $request->validated();

        // $data = [
        //     'html_notes' => "<body>" . str_replace(['<p>', '</p>'], '', $requestData['description']) . "</body>",
        // ];

        // $task = $this->asanaService->updateTask($ticket->asana_task_id, $data);
        // if ($task['error_status']) {
        //     return ApiHelper::response($status, __('messages.asana.update_ticket.update_error'), '', 500);
        // }
        // $validateData['asana_task_id'] = $task['data']['data']['gid'];

        $status = true;
        $code = 200;
        $message = __('messages.ticket.update_description_success');

        try {
            DB::transaction(function () use ($ticket, $requestData) {
                $ticket->update($requestData);
            });
        } catch (Exception $e) {
            Log::error('Update description Note Failed: ' . $e->getMessage());

            $status = false;
            $code = 500;
            $message = __('messages.ticket.update_failed');
        }

        return ApiHelper::response($status, $message, $ticket, $code);
    }

    public function allTicketsForDropdown()
    {
        $tickets = Ticket::query()->get(['id', 'name']);

        if ($tickets->isEmpty()) {
            return ApiHelper::response('false', __('messages.ticket.not_found'), [], 404);
        }

        return ApiHelper::response(true, __('messages.ticket.fetched'), $tickets, 200);
    }

    public function getInitiativeProjectList(Request $request, $initiativeId)
    {
        $project = ProjectService::getInitiativeProjects($initiativeId);
        return ApiHelper::response(true, '', $project, 200);
    }

    public function assignProject(AssignProjectRequest $request, $initiativeId)
    {
        $status = false;
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        if ($request->has('project_id') && !empty($request->input('project_id'))) {
            $project = Project::where('initiative_id', $initiativeId)->find($request->project_id);
            if (!$project) {
                return ApiHelper::response($status, __('messages.project.project_not_exist'), '', 400);
            }
        }

        $status = true;
        $message = __('messages.project.assign_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            if ($request->has('project_id') && !empty($request->input('project_id'))) {
                ProjectService::assignProjectForTasks($request);
            }
            if ($request->has('project_name') && !empty($request->input('project_name'))) {
                ProjectService::createAndAssignProjectForTasks($request);
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

    public function assignOrRemoveProjectForTask(Request $request, $initiativeId)
    {
        $status = false;
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $project = Project::where('initiative_id', $initiativeId)->find($request->input('selectedOption')['id']);
        if (!$project) {
            return ApiHelper::response($status, __('messages.project.project_not_exist'), '', 400);
        }

        $status = true;
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $request->merge(['project_id' => $request->input('selectedOption')['id']]);
            $request->merge(['selectedTasks' => [$request->input('taskId')]]);

            if ($request->has('type') && !empty($request->input('type')) && $request->input('type') == 'assign') {
                ProjectService::assignProjectForTasks($request);
                $message = __('messages.project.assign_success');
            }
            if ($request->has('type') && !empty($request->input('type')) && $request->input('type') == 'remove') {
                ProjectService::removeProjectForTasks($request);
                $message = __('messages.project.remove_success');
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

    public function changeActionUser(Request $request, $initiativeId, $ticketId)
    {
        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (Auth::id() != $initiative->functional_owner_id && Auth::id() != $initiative->technical_owner_id) {
            return ApiHelper::response($status, __('messages.ticket.change_action_user_not_allowed'), '', 400);
        }
        $ticket = Ticket::find($ticketId);
        if (!$ticket) {
            return ApiHelper::response($status, __('messages.ticket.ticket_not_exist'), '', 400);
        }
        $status = true;
        $message = __('messages.ticket.change_action_user_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            TicketAction::where('id', $request->input('action_id'))
                ->where('ticket_id', $ticketId)
                ->where('action', $request->input('action'))
                ->update(['user_id' => $request->input('user_id')]);
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

    public function changeActionStatus(Request $request, $initiativeId, $ticketId)
    {
        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (Auth::id() != $request->input('user_id') && $initiative->functional_owner_id != Auth::id()) {
            return ApiHelper::response($status, __('messages.ticket.change_action_status_not_allowed'), '', 400);
        }
        $ticket = Ticket::find($ticketId);
        if (!$ticket) {
            return ApiHelper::response($status, __('messages.ticket.ticket_not_exist'), '', 400);
        }

        if ($request->input('status') == TicketAction::getStatusWaitingForDependantAction()) {
            return ApiHelper::response($status, __('messages.ticket.change_action_status_not_allowed_du_to_waiting_for_dependant'), '', 400);
        }

        if ($request->input('status') == TicketAction::getStatusDone()) {
            return ApiHelper::response($status, __('messages.ticket.change_action_status_not_allowed_du_to_done'), '', 400);
        }

        if ($request->input('action') == TicketAction::getActionDevelop() && $ticket->auto_wait_for_client_approval) {
            return ApiHelper::response($status, __('messages.ticket.change_action_status_not_allowed_du_to_waiting_for_client_approval'), '', 400);
        }

        if ($ticket->status != Ticket::getStatusOngoing()) {
            return ApiHelper::response($status, __('messages.ticket.change_action_status_not_allowed_du_to_status_not_ongoing'), '', 400);
        }

        $status = true;
        $message = __('messages.ticket.change_current_action_status_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $action = TicketService::updateTicketActions($ticket, $request->input('action_id'), TicketAction::getStatusDone());
            TicketService::updateTicketStatus($ticket);
            TicketService::createMacroStatusAndUpdateTicket($ticket);
            TicketService::storeLogging($ticket, Logging::ACTIVITY_TYPE_MARKED_AS_DONE, $action);
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

    public function changePreviousActionStatus(Request $request, $initiativeId, $ticketId)
    {
        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (Auth::id() != $request->input('user_id') && Auth::id() != $initiative->functional_owner_id) {
            return ApiHelper::response($status, __('messages.ticket.change_action_status_not_allowed'), '', 400);
        }
        $ticket = Ticket::with('actions')->find($ticketId);
        if (!$ticket) {
            return ApiHelper::response($status, __('messages.ticket.ticket_not_exist'), '', 400);
        }

        if (!empty($request->input('status')) && $request->input('status') == TicketAction::getStatusWaitingForDependantAction()) {
            return ApiHelper::response($status, __('messages.ticket.change_action_status_not_allowed_du_to_waiting_for_dependant'), '', 400);
        }

        if ($request->input('status') == TicketAction::getStatusDone()) {
            return ApiHelper::response($status, __('messages.ticket.change_action_status_not_allowed_du_to_done'), '', 400);
        }

        if ($ticket->auto_wait_for_client_approval) {
            return ApiHelper::response($status, __('messages.ticket.change_action_status_not_allowed_du_to_waiting_for_client_approval'), '', 400);
        }

        $isReadyForDeploymentToPrd = false;
        if ($ticket->macro_status == Ticket::MACRO_STATUS_READY_FOR_DEPLOYMENT_TO_PRD && $ticket->macro_status != Ticket::MACRO_STATUS_DONE) {
            $releaseTicket = ReleaseTicket::where('ticket_id', $ticket->id)->first();
            if ($releaseTicket) {
                return ApiHelper::response($status, __('messages.ticket.change_action_status_not_allowed_du_to_status_not_ongoing'), '', 400);
            }
            $isReadyForDeploymentToPrd = true;
        }

        $status = true;
        $message = __('messages.ticket.change_previous_action_status_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $action = TicketService::updateTicketPreviousActions($ticket, $request->input('action_id'), $request->input('previous_action_id'), TicketAction::getStatusWaitingForDependantAction(), $isReadyForDeploymentToPrd);
            TicketService::updateTicketStatus($ticket);
            TicketService::createMacroStatusAndUpdateTicket($ticket, true);
            TicketService::storeLogging($ticket, Logging::ACTIVITY_TYPE_MOVED_BACK_TO, $action);
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

    public function getCreateReleaseData(Request $request, $initiativeId)
    {
        $retData = [
            'unprocessedRelease' => ReleaseService::getUnprocessedRelease($initiativeId),
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }

    public function createRelease(CreateReleaseRequest $request, $initiativeId)
    {
        $requestData = $request->all();
        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request);

        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }
        if (Auth::id() != $initiative->functional_owner_id) {
            return ApiHelper::response($status, __('messages.ticket.change_action_status_not_allowed'), '', 400);
        }

        $release = $initiative->unprocessedRelease;
        if ($release) {
            if ($request->input('release_id') == "") {
                return ApiHelper::response($status, __('messages.release.please_select_valid_release'), '', 400);
            }
            if ($release->id != $request->input('release_id')) {
                return ApiHelper::response($status, __('messages.release.please_select_valid_release'), '', 400);
            }
            if ($request->input('tags') != "" || $request->input('is_major') == true) {
                return ApiHelper::response($status, __('messages.release.your_release_has_already_been_created'), '', 400);
            }
        }


        $ticketIds = $requestData['selectedTasks'];
        if (count($ticketIds) == 0) {
            return ApiHelper::response($status, __('messages.release.please_select_tasks'), '', 400);
        }
        $ticketCount = TicketService::getTicketCountCanNotMatchWithStatus($ticketIds, Ticket::getStatusReadyForPRD());
        if ($ticketCount > 0) {
            return ApiHelper::response($status, __('messages.release.please_select_tasks_only_ready_for_PRD'), '', 400);
        }

        $status = true;
        $message = __('messages.release.create_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            if ($request->input('release_id') == "") {
                $releaseVersion = TicketService::createReleaseVersion($requestData['is_major'], $initiative->id);
                $releaseName = TicketService::createReleaseName($releaseVersion, $requestData);
                $requestData['name'] = $releaseName;
                $requestData['version'] = $releaseVersion;
                $requestData['is_major'] = $requestData['is_major'] || $releaseVersion == 1 ? 1 : 0;
                $release = Release::create($requestData);
            }
            foreach ($ticketIds as $ticketId) {
                $condition = [
                    'release_id' => $release->id,
                    'ticket_id' => $ticketId
                ];
                $updateOrCreateData = [
                    'ticket_id' => $ticketId
                ];
                ReleaseTicket::updateOrCreate($condition, $updateOrCreateData);
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

    public function updateTicketDetailEstimatedHours(TicketDetailEstimatedHoursRequest $request, $initiativeId, $ticketId)
    {
        $requestData = $request->all();

        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request);

        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $ticket = Ticket::with('currentAction')->find($ticketId);
        if (!$ticket) {
            return ApiHelper::response($status, __('messages.ticket.ticket_not_exist'), '', 400);
        }
        if (!empty($ticket->currentAction)) {
            if (!empty($ticket->currentAction->user)) {
                if ($ticket->currentAction->action != TicketAction::getActionClarifyAndEstimate() || Auth::id() != $ticket->currentAction->user->id) {
                    return ApiHelper::response($status, __('messages.ticket.update_ticket_detail_estimated_hours_not_allowed'), '', 400);
                }
            }
        }

        $status = true;
        $message = __('messages.ticket_detail.update_ticket_detail_estimated_hours_success');
        $statusCode = 200;
        $retData = [
            'ticket' => ''
        ];
        DB::beginTransaction();
        try {
            $ticket->update(['dev_estimation_time' => $requestData['dev_estimation_time']]);
            DB::commit();
            $retData = [
                'ticket' => $ticket
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, $retData, $statusCode);
    }

    public function addRemovePriority(Request $request, $initiativeId)
    {
        $requestData = $request->all();

        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $status = true;
        if ($requestData['is_priority'] == true) {
            $message = __('messages.ticket.add_remove_priority_success_alt_add');
        } else {
            $message = __('messages.ticket.add_remove_priority_success_alt_remove');
        }
        $statusCode = 200;
        DB::beginTransaction();
        try {
            Ticket::whereIn('id', $requestData['ticket_ids'])->update(['is_priority' => $requestData['is_priority']]);
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
    public function markAsVisibleInvisible(Request $request, $initiativeId)
    {
        $requestData = $request->all();

        $status = false;
        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $status = true;
        if ($requestData['is_visible'] == true) {
            $message = __('messages.ticket.visible_success_alt_add');
        } else {
            $message = __('messages.ticket.visible_success_alt_remove');
        }
        $statusCode = 200;
        DB::beginTransaction();
        try {
            Ticket::whereIn('id', $requestData['ticket_ids'])->update(['is_visible' => $requestData['is_visible']]);
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

    public function deleteTicket(Request $request, $initiativeId)
    {
        $status = false;

        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.ticket.delete_ticket_not_allowed'), null, 400);
        }

        $request->merge(['initiative_id' => $initiativeId]);
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $ticket = Ticket::with('actions')->withCount('timeBookings')->find($request->post('ticket_id'));
        if (!$ticket) {
            return ApiHelper::response($status, __('messages.ticket.ticket_not_exist'), '', 400);
        }
        if ($ticket && $ticket->macro_status == Ticket::MACRO_STATUS_DONE) {
            return ApiHelper::response($status, __('messages.ticket.delete_ticket_not_allowed_because_ticket_already_done'), '', 400);
        }

        $ticketDoneActions = $ticket->actions->where('status', TicketAction::getStatusDone());
        $ticket->is_show_delete_btn = Auth::user()->is_admin && $ticket->timeBookings->count() == 0 && $ticketDoneActions->count() == 0 && $ticket->moved_back_to_dev_action_count == 0 ?? false;

        if (!$ticket->is_show_delete_btn) {
            return ApiHelper::response($status, __('messages.ticket.delete_ticket_not_allowed_because_time_bookings_exist_or_any_action_already_done_dev_count_not_zero'), '', 400);
        }

        $task = $this->asanaService->deleteTask($ticket->asana_task_id);
        if ($task['error_status']) {
            return ApiHelper::response($status, __('messages.asana.delete_ticket.delete_error'), '', 400);
        }

        $status = true;
        $message = __('messages.ticket.delete_ticket_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $ticket->delete();
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
