<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Models\TicketAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeMyActionsController extends Controller
{
    public function index(Request $request)
    {
        $initiatives = Initiative::select(
            'initiatives.id',
            'initiatives.client_id',
            'initiatives.name',
            DB::RAW('count(tickets.id) as tickets_count')
        )
            ->with([
                'client' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
            ->join('tickets', 'tickets.initiative_id', '=', 'initiatives.id')
            ->LEFTJOIN(DB::raw(
                "(SELECT `ticket_id`, MIN(ACTION) AS first_action, `user_id` FROM ticket_actions WHERE `status` != " . TicketAction::getStatusDone() . " GROUP BY `ticket_id` HAVING `user_id` = " . Auth::id() . ") as ta"
            ), 'ta.ticket_id', '=', 'tickets.id')
            ->where(function ($q) {
                $q->where('tickets.is_visible', 1)
                    ->orWhereNotNull('ta.first_action');
            })
            ->groupBy('initiatives.id')
            ->get();
        $retData = [
            'initiatives' => $initiatives
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }
}
