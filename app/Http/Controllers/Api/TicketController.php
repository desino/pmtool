<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TitcketRequest;
use App\Http\Requests\UpdateReleaseNoteRequest;
use App\Models\Functionality;
use App\Models\Section;
use App\Models\Ticket;
use App\Services\AsanaService;
use App\Services\InitiativeService;
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
        $validatData = $request->validated();
        $status = false;
        $retData = [
            'ticket' => "",
        ];

        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.sectino.initiative_not_exist'), '', 400);
        }
        if (!$initiative->client) {
            return ApiHelper::response($status, __('messages.solution_design.sectino.client_not_exist'), '', 400);
        }

        $getAsanaProject = $this->asanaService->getProject(trim($initiative->asana_project_id));
        if ($getAsanaProject['error_status']) {
            return ApiHelper::response($status, __('messages.asana.project_does_not_exist'), '', 500);
        }

        $ticketTypes = Ticket::getAllTypes();
        $projectId = $initiative->asana_project_id; // from getProject api
        $data = [
            'name' => $validatData['name'],
            'resource_type' => 'task',
            'resource_subtype' => 'default_task',
            // 'resource_subtype' => $ticketTypes[$validatData['type']],
            // 'notes' => 'This is a task created from the API.',
            // 'due_on' => '2024-08-10',
        ];
        $task = $this->asanaService->createTask($projectId, $data);
        if ($task['error_status']) {
            return ApiHelper::response($status, __('messages.asana.create_ticket.store_error'), '', 500);
        }
        $validatData['asana_task_id'] = $task['data']['data']['gid'];
        DB::beginTransaction();
        try {
            $ticket = Ticket::create($validatData);
            $status = true;
            $meesage = __('messages.create_ticket.store_success');
            $statusCode = 200;
            $retData = [
                'ticket' => $ticket,
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $meesage = env('APP_ENV') == 'local' ? $e->getMessage() : 'Something went wrong!';
            $statusCode = 500;
            Log::info($e->getMessage());
        }
        return ApiHelper::response($status, $meesage, $retData, $statusCode);
    }

//    TODO : refactor this after functionality completes
    public function index($initiative_id,Request $request)
    {
        $filters=$request->filters;

        $tickets = Ticket::whereHas('functionality.section',function ($query) use ($initiative_id){
                $query->where('initiative_id',$initiative_id);
        })->when($filters['task_name'] != '', function (Builder $query) use ($filters) {
            $query->whereLike('name','%'.$filters['task_name'].'%');
        })->when($filters['task_type'] != '', function (Builder $query) use ($filters) {
            $query->where('type',$filters['task_type']);
        })->when($filters['functionalities'] != null, function (Builder $query) use ($filters) {
            $query->whereIn('functionality_id',array_column($filters['functionalities'],'id'));
        })->get();

        $tickets=$tickets->transform(function ($ticket){
           return [
               'id'=>$ticket->id,
               'name' => $ticket->name,
               'type_label'=>$ticket->type_label,
               'created_at' => $ticket->created_at->format('Y-m-d'),
               ];
        });

        $meta['task_type']=Ticket::getAllTypes();
        $meta['functionalities']=Functionality::whereHas('section',function ($query) use ($initiative_id){
            $query->where('initiative_id',$initiative_id);
        })->get(['id','display_name']);

        return ApiHelper::response('false', __('messages.ticket.fetched'), $tickets, 200,$meta);
    }

    public function show($initiative_id,$ticket_id)
    {
        $ticket = Ticket::with('functionality')->find($ticket_id);

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
            \Log::error('Update Release Note Failed: '.$e->getMessage());

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

}
