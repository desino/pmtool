<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Models\Ticket;
use App\Models\TicketAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeMyActionsController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::select(
            'initiatives.id',
            'initiatives.name',
            'initiatives.client_id',
            'clients.name as client_name',
            DB::RAW('count(tickets.id) as tickets_count'),
            DB::RAW('count(CASE WHEN tickets.is_priority = 1 THEN 1 END) as is_priority_tickets_count')
        )
            ->leftjoin('initiatives', 'tickets.initiative_id', '=', 'initiatives.id')
            ->leftjoin('clients', 'initiatives.client_id', '=', 'clients.id')
            ->where('tickets.is_visible', 1)
            ->where('tickets.macro_status', '!=', Ticket::MACRO_STATUS_DONE)
            ->where(function ($q) {
                $q->whereHas('currentAction', function ($q) {
                    $q->where('user_id', Auth::id());
                })
                    ->orWhereHas('actions', function ($q) {
                        $q->where('user_id', Auth::id());
                    });
            })
            ->groupBy('tickets.initiative_id')
            ->get();

        $retData = [
            'initiatives' => $tickets
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }
}
