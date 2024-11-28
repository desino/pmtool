<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InitiativeOverviewController extends Controller
{
    public function index(Request $request)
    {
        // print('<pre>');
        // print_r(Ticket::get()->toArray());
        // print('</pre>');
        // exit;
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.initiative_overview.dont_have_permission'), null, 404);
        }

        $ticketSubqueryForCount = Ticket::selectRaw('COUNT(id)')
            ->whereColumn('tickets.initiative_id', 'initiatives.id')
            ->where('macro_status', '!=', Ticket::MACRO_STATUS_DONE);

        $ticketSubqueryForHours = Ticket::selectRaw('SUM(IF(dev_estimation_time > 0, dev_estimation_time,initial_estimation_development_time))')
            ->whereColumn('tickets.initiative_id', 'initiatives.id')
            ->where('macro_status', '!=', Ticket::MACRO_STATUS_DONE);

        $ticketSubqueryForActionsMaxUpdated = Ticket::selectRaw('MAX(ticket_actions.updated_at)')
            ->join('ticket_actions', 'ticket_actions.ticket_id', '=', 'tickets.id')
            ->whereColumn('tickets.initiative_id', 'initiatives.id')
            ->where('tickets.macro_status', '!=', Ticket::MACRO_STATUS_DONE)
            ->whereNotNull('ticket_actions.updated_at');

        $initiative = Initiative::select(
            'id',
            'client_id',
            'name',
        )
            ->selectSub(
                $ticketSubqueryForCount,
                'total_ticket_count'
            )
            ->selectSub(
                $ticketSubqueryForCount->clone()
                    ->where('is_visible', 1),
                'visible_ticket_count'
            )
            ->selectSub(
                $ticketSubqueryForCount->clone()
                    ->where('is_visible', 0),
                'invisible_ticket_count'
            )
            ->selectSub(
                $ticketSubqueryForHours,
                'total_ticket_estimation_hours'
            )
            ->selectSub(
                $ticketSubqueryForHours->clone()
                    ->where('is_visible', 1),
                'visible_ticket_estimation_hours'
            )
            ->selectSub(
                $ticketSubqueryForActionsMaxUpdated,
                'visible_ticket_actions_updated_at'
            )
            ->orderBy('visible_ticket_count', 'desc')
            ->orderBy('visible_ticket_actions_updated_at', 'desc')
            ->get();
        $retData = [
            'initiatives' => $initiative,
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }
}
