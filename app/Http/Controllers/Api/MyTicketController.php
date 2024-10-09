<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAction;
use App\Services\InitiativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        )
            ->with([
                'currentAction' => function ($q) {
                    $q->select('id', 'ticket_id', 'action');
                },
                'functionality' => function ($q) {
                    $q->select('id', 'name', 'display_name');
                }
            ])
            ->join('ticket_actions', 'tickets.id', 'ticket_actions.ticket_id')
            ->where('tickets.initiative_id', $initiative_id)
            ->where(function ($q) {
                $q->where('tickets.is_visible', 1)
                    ->orWhere('ticket_actions.id', function ($q) {
                        $q->select('id')
                            ->from('ticket_actions')
                            ->where('user_id', Auth::id())
                            ->where('status', '!=', TicketAction::getStatusDone())
                            ->orderBy('action', 'desc')
                            ->limit(1);
                    });
            })
            ->groupBy('tickets.id')
            ->paginate(10);
        return ApiHelper::response(true, '', $tickets, 200);
    }
}
