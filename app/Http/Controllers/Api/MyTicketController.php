<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAction;
use App\Services\InitiativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MyTicketController extends Controller
{

    public function index($initiative_id, Request $request)
    {

        $status = false;
        $initiative = InitiativeService::getInitiative($request, $initiative_id);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 404);
        }

        $filters = $request->filters;

        $tickets = Ticket::select(
            'tickets.id',
            'tickets.initiative_id',
            'tickets.name',
            'tickets.functionality_id',
            'tickets.composed_name',
            'tickets.asana_task_id',
            'tickets.macro_status',
            'tickets.is_visible',
            'tickets.is_priority',
            'tickets.created_at',
            'tickets.created_by',
        )
            ->with([
                'currentAction' => function ($q) {
                    $q->select('id', 'ticket_id', 'action');
                },
                'functionality' => function ($q) {
                    $q->select('id', 'name', 'display_name');
                },
                'latestComment' => function ($q) {
                    $q->select(
                        'ticket_comments.id',
                        'ticket_comments.ticket_id',
                        'ticket_comments.comment',
                        'ticket_comments.created_at',
                        'ticket_comments.updated_at',
                        'ticket_comments.created_by',
                        'ticket_comments.updated_by',
                        'ticket_comments.tagged_users',
                        'created_user.name AS created_user_name',
                        'updated_user.name AS updated_user_name',
                        DB::RAW('IF (ticket_comments.updated_by , ticket_comments.updated_by, ticket_comments.created_by) AS user_id'),
                        DB::RAW('IF (ticket_comments.updated_by , updated_user.name, created_user.name) AS created_updated_user_name'),
                    )
                        ->leftJoin('users AS created_user', 'created_user.id', 'ticket_comments.created_by')
                        ->leftJoin('users AS updated_user', 'updated_user.id', 'ticket_comments.updated_by');
                },
            ])

            ->where('tickets.initiative_id', $initiative_id)
            ->where('tickets.is_visible', 1)
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
            })
            ->when($filters['task_name'] != '', function ($query) use ($filters) {
                $query->whereLike('composed_name', '%' . $filters['task_name'] . '%');
            })
            ->when($filters['task_type'] != '', function ($query) use ($filters) {
                $query->where('type', $filters['task_type']);
            })
            ->when($filters['is_include_done'] == 'false', function ($query) use ($filters) {
                $query->where('tickets.macro_status', '!=', Ticket::MACRO_STATUS_DONE);
            })
            ->groupBy('tickets.id', 'tickets.initiative_id', 'tickets.name', 'tickets.functionality_id', 'tickets.composed_name', 'tickets.asana_task_id', 'tickets.macro_status')
            ->orderBy('tickets.id')
            ->get()->each(function ($ticket) {
                if ($ticket->latestComment) {
                    $ticket->latestComment->comment = Str::limit(strip_tags(strip_tags($ticket->latestComment->comment)), 120, '...');
                }
            });
        // ->paginate(10);
        $meta['task_type'] = Ticket::getAllTypes();
        $initiativeData = array(
            'id' => $initiative->id,
            'name' => $initiative->name,
        );
        $meta['initiative'] = $initiativeData;
        return ApiHelper::response(true, '', $tickets, 200, $meta);
    }
}
