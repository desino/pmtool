<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AssignProjectRequest;
use App\Http\Requests\Api\TitcketRequest;
use App\Http\Requests\UpdateReleaseNoteRequest;
use App\Models\Functionality;
use App\Models\Project;
use App\Models\Section;
use App\Models\Ticket;
use App\Services\AsanaService;
use App\Services\InitiativeService;
use App\Services\ProjectService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    protected AsanaService $asanaService;

    public function __construct(AsanaService $asanaService)
    {
        $this->asanaService = $asanaService;
    }

    public function getSectionFunctionality(int $initiative_id)
    {
        $sectionFunctionalities = Section::select(['id', 'name',])
            ->with(['functionalities' => function ($q) {
                $q->select(
                    'id',
                    'section_id',
                    'name',
                    'id'
                );
            }])
            ->where('initiative_id', $initiative_id)
            ->get();
        return ApiHelper::response(true, '', $sectionFunctionalities, 200);
    }

    public function store(TitcketRequest $request)
    {
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

        $getAsanaProject = $this->asanaService->getProject(trim($initiative->asana_project_id));
        if ($getAsanaProject['error_status']) {
            return ApiHelper::response($status, __('messages.asana.project_does_not_exist'), '', 500);
        }

        $ticketTypes = Ticket::getAllTypes();
        $projectId = $initiative->asana_project_id; // from getProject api
        $data = [
            'name' => $validateData['name'],
            'resource_type' => 'task',
            'resource_subtype' => 'default_task',
            // 'resource_subtype' => $ticketTypes[$validateData['type']],
            // 'notes' => 'This is a task created from the API.',
            // 'due_on' => '2024-08-10',
        ];
        $task = $this->asanaService->createTask($projectId, $data);
        if ($task['error_status']) {
            return ApiHelper::response($status, __('messages.asana.create_ticket.store_error'), '', 500);
        }
        $validateData['asana_task_id'] = $task['data']['data']['gid'];
        DB::beginTransaction();
        try {
            $ticket = Ticket::create($validateData);
            $status = true;
            $message = __('messages.create_ticket.store_success');
            $statusCode = 200;
            $retData = [
                'ticket' => $ticket,
            ];
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $message = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $message, $retData, $statusCode);
    }

    //    TODO : refactor this after functionality completes
    public function index($initiative_id, Request $request)
    {
        $filters = $request->filters;

        $tickets = Ticket::select(
            'id',
            'name',
            'type',
            'project_id',
            'created_at',
        )
            ->with(['project' => function ($q) {
                $q->select(
                    'id',
                    'initiative_id',
                    'name',
                );
            }])
            ->whereHas('functionality.section', function ($query) use ($initiative_id) {
                $query->where('initiative_id', $initiative_id);
            })->when($filters['task_name'] != '', function (Builder $query) use ($filters) {
                $query->whereLike('name', '%' . $filters['task_name'] . '%');
            })->when($filters['task_type'] != '', function (Builder $query) use ($filters) {
                $query->where('type', $filters['task_type']);
            })
            ->when(!empty($filters['functionalities']), function (Builder $query) use ($filters) {
                $query->whereIn('functionality_id', array_column($filters['functionalities'], 'id'));
            })
            ->when(!empty($filters['projects']) != '', function (Builder $query) use ($filters) {
                $query->whereIn('project_id', array_column($filters['projects'], 'id'));
            })
            ->paginate(10);

        $meta['task_type'] = Ticket::getAllTypes();
        $meta['functionalities'] = Functionality::whereHas('section', function ($query) use ($initiative_id) {
            $query->where('initiative_id', $initiative_id);
        })->get(['id', 'display_name']);
        $meta['projects'] = ProjectService::getInitiativeProjects($initiative_id);

        return ApiHelper::response('false', __('messages.ticket.fetched'), $tickets, 200, $meta);
    }

    public function show($initiative_id, $ticket_id)
    {
        $ticket = Ticket::with('functionality')
            ->where('id', $ticket_id)
            ->whereHas('functionality.section', function ($query) use ($initiative_id) {
                $query->where('initiative_id', $initiative_id);
            })
            ->get()->first();

        if (!$ticket) {
            return ApiHelper::response('false', __('messages.ticket.not_found'), [], 404);
        }

        return ApiHelper::response(true, __('messages.ticket.fetched'), $ticket, 200);
    }

    public function updateReleaseNote($ticket_id, UpdateReleaseNoteRequest $request)
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
            \Log::error('Update Release Note Failed: ' . $e->getMessage());

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
        DB::beginTransaction();
        try {
            if ($request->has('project_id') && !empty($request->input('project_id'))) {
                ProjectService::assignProjectForTasks($request);
            }
            if ($request->has('project_name') && !empty($request->input('project_name'))) {
                ProjectService::createAndAssignProjectForTasks($request);
            }
            $status = true;
            $message = __('messages.project.assign_success');
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
            $status = true;
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
