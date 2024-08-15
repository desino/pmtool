<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TitcketRequest;
use App\Http\Requests\UpdateReleaseNoteRequest;
use App\Models\Section;
use App\Models\Ticket;
use App\Services\AsanaService;
use App\Services\InitiativeService;
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
    public function getSectionFunctionality(Request $request)
    {
        $sectionFunctionalities = Section::select(
            'id',
            'name',
        )
            ->with(['functionalities' => function ($q) {
                $q->select(
                    'id',
                    'section_id',
                    'name',
                    'id'
                );
            }])
            ->where('initiative_id', $request->post('initiative_id'))
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

        $ticketTypes = Ticket::types();
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

    public function show($id)
    {
        \Log::info('hwere');
        $ticket = Ticket::with('functionality')->find($id);

        if (!$ticket) {
            return ApiHelper::response('false', __('messages.ticket.not_found'), [], 404);
        }

        return ApiHelper::response(true, __('messages.ticket.fetched'), $ticket, 200);
    }

    public function updateReleaseNote($id, UpdateReleaseNoteRequest $request)
    {

        $ticket = Ticket::with('functionality')->find($id);
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
