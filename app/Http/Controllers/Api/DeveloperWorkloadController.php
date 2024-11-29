<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeveloperWorkloadController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.developer_workload.dont_have_permission'), null, 404);
        }

        $tickets = Ticket::select(
            '*',
            DB::RAW('IF(dev_estimation_time > 0, dev_estimation_time,initial_estimation_development_time) as estimation_time'),
        )
            ->with([
                'actions',
                'developAction' => function ($q) {
                    $q->with('user');
                }
            ])
            ->where('macro_status', '!=', Ticket::MACRO_STATUS_DONE)
            ->whereHas('actions', function ($query) {
                $query->where('action', TicketAction::getActionDevelop());
                $query->where('status', '!=', TicketAction::getStatusDone());
            })
            ->get();

        $userTickets = $tickets->groupBy('developAction.user_id');

        $retDeveloperWorkloadData = collect([]);
        foreach ($userTickets as $userTicket) {
            $visibleTickets = $userTicket->where('is_visible', 1);
            $visibleTicketsCount = $visibleTickets->count();
            $visibleTicketsHours = $visibleTickets->sum('estimation_time');

            $invisibleTickets = $userTicket->where('is_visible', 0);
            $invisibleTicketsCount = $invisibleTickets->count();
            $invisibleTicketsHours = $invisibleTickets->sum('estimation_time');

            $totalTicketsCount = $userTicket->count();
            $totalTicketsHours = $userTicket->sum('estimation_time');
            $retDeveloperWorkloadData->push([
                'user_id' => $userTicket[0]->developAction->user_id,
                'user_name' => $userTicket[0]->developAction->user->name,

                'visible_tickets_count' => $visibleTicketsCount,
                'visible_tickets_hours' => $visibleTicketsHours,
                'display_visible_tickets_count_hours' => "{$visibleTicketsCount} ({$visibleTicketsHours}hrs)",

                'invisible_tickets_count' => $invisibleTicketsCount,
                'invisible_tickets_hours' => $invisibleTicketsHours,
                'display_invisible_tickets_count_hours' => "{$invisibleTicketsCount} ({$invisibleTicketsHours}hrs)",

                'total_tickets_count' => $totalTicketsCount,
                'total_tickets_hours' => $totalTicketsHours,
                'display_total_tickets_count_hours' => "{$totalTicketsCount} ({$totalTicketsHours}hrs)",
            ]);
        }
        $retData = [
            'developerWorkloads' => $retDeveloperWorkloadData
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }

    public function getDeveloperWorkloadTicketModalData(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->is_admin) {
            return ApiHelper::response(false, __('messages.developer_workload.dont_have_permission'), null, 404);
        }

        $tickets = Ticket::select(
            '*',
            DB::RAW('IF(dev_estimation_time > 0, dev_estimation_time,initial_estimation_development_time) as estimation_time'),
        )
            ->with([
                'currentAction',
                'actions',
                'developAction' => function ($q) {
                    $q->with('user');
                }
            ])
            ->where('macro_status', '!=', Ticket::MACRO_STATUS_DONE)
            ->whereHas('actions', function ($query) use ($request) {
                $query->where('action', TicketAction::getActionDevelop())
                    ->where('user_id', $request->get('user_id'))
                    ->where('status', '!=', TicketAction::getStatusDone());
            })
            ->when($request->get('type_of_tickets') == 'visible' || $request->get('type_of_tickets') == 'invisible', function ($query) use ($request) {
                $isVisible = $request->get('type_of_tickets') == 'visible' ? 1 : 0;
                $query->where('is_visible', $isVisible);
            })
            ->get();
        $passData = [
            'tickets' => $tickets
        ];
        return ApiHelper::response(true, '', $passData, 200);
    }
}
