<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Models\Ticket;
use App\Models\TicketAction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AllTicketsWithoutInitiativeController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->get('filters');

        $tickets = Ticket::select('*')
            ->with('currentAction')
            ->where('macro_status', '!=', Ticket::MACRO_STATUS_DONE)
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
            ->when(!empty($filters['initiative_id']) != '', function (Builder $query) use ($filters) {
                $query->where('initiative_id', $filters['initiative_id']);
            })
            ->when(!empty($filters['macro_status']) != '', function (Builder $query) use ($filters) {
                $query->whereIn('macro_status', array_column($filters['macro_status'], 'id'));
            })
            ->paginate(10);
        return ApiHelper::response(true, '', $tickets, 200);
    }

    public function getInitialData(Request $request)
    {
        $users = User::get();
        $initiatives = Initiative::with('client')->get();
        $macroStatus = Ticket::getAllMacroStatus();
        $visible = Config::get('myapp.ticket_filters_visible_in_visible');
        $retData = [
            'users' => $users,
            'initiatives' => $initiatives,
            'macroStatus' => $macroStatus,
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }
}
