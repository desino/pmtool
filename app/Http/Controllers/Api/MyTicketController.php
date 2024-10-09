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

class MyTicketController extends Controller
{

    public function index($initiative_id, Request $request)
    {

        $status = false;
        $initiative = InitiativeService::getInitiative($request, $initiative_id);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
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
        )
            ->with([
                'currentAction' => function ($q) {
                    $q->select('id', 'ticket_id', 'action');
                },
                'functionality' => function ($q) {
                    $q->select('id', 'name', 'display_name');
                }
            ])
            ->LEFTJOIN(DB::raw(
                "(SELECT `ticket_id`, MIN(ACTION) AS first_action, `user_id` FROM ticket_actions WHERE `status` != " . TicketAction::getStatusDone() . " GROUP BY `ticket_id` HAVING `user_id` = " . Auth::id() . ") as ta"
            ), 'ta.ticket_id', '=', 'tickets.id')
            ->where('tickets.initiative_id', $initiative_id)
            ->where(function ($q) {
                $q->where('tickets.is_visible', 1)
                    ->orWhereNotNull('ta.first_action');
            })
            ->when($filters['task_name'] != '', function ($query) use ($filters) {
                $query->whereLike('composed_name', '%' . $filters['task_name'] . '%');
            })
            ->when($filters['task_type'] != '', function ($query) use ($filters) {
                $query->where('type', $filters['task_type']);
            })
            ->groupBy('tickets.id', 'tickets.initiative_id', 'tickets.name', 'tickets.functionality_id', 'tickets.composed_name', 'tickets.asana_task_id', 'tickets.macro_status')
            ->orderBy('tickets.id')
            ->paginate(10);
        // ->get(10);
        $meta['task_type'] = Ticket::getAllTypes();
        $meta['macro_status'] = Ticket::getAllMacroStatus();
        return ApiHelper::response(true, '', $tickets, 200, $meta);
    }
}
