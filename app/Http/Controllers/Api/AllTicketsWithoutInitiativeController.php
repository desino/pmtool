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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AllTicketsWithoutInitiativeController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->get('filters');

        $ticketsQuery = Ticket::select(
            'id',
            'name',
            'type',
            'project_id',
            'created_at',
            'created_by',
            'asana_task_id',
            'functionality_id',
            'initiative_id',
            'composed_name',
            'status',
            'macro_status',
            'is_priority',
            'is_visible',
            'initial_estimation_development_time',
            'dev_estimation_time',
            'moved_back_to_dev_action_count',
            DB::RAW('IF(dev_estimation_time > 0, dev_estimation_time,initial_estimation_development_time) as estimation_time'),
            DB::RAW('IF(macro_status = ' . Ticket::MACRO_STATUS_DONE . ', true,false) as is_ticket_done'),
        )
            ->with([
                'functionality' => function ($q) {
                    $q->select(
                        'id',
                        'display_name',
                    );
                },
                'currentAction' => function ($query) {
                    $query->select('id', 'ticket_id', 'action', 'status', 'user_id');
                },
                'initiative' => function ($query) {
                    $query->select('id', 'name');
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
            ->when($filters['visible'] != '', function (Builder $query) use ($filters) {
                $query->where('is_visible', $filters['visible']);
            })
            ->when($filters['is_priority'] == 'true', function (Builder $query) use ($filters) {
                $query->where('is_priority', 1);
            })
            ->paginate(30);
        $tickets = $ticketsQuery->map(function ($ticket) {
            if ($ticket->latestComment) {
                $ticket->latestComment->comment = isset($ticket->latestComment->comment)
                    ? Str::limit(strip_tags($ticket->latestComment->comment), 120, '...')
                    : null;
            }
            return $ticket;
        });

        $retData = [
            'data' => $tickets,
            'current_page' => $ticketsQuery->currentPage(),
            'last_page' => $ticketsQuery->lastPage(),
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }

    public function getInitialData(Request $request)
    {
        $users = User::select('id', 'name')->get();
        $initiatives = Initiative::select('id', 'name', 'client_id')
            ->with(['client' => function ($query) {
                $query->select('id', 'name');
            }])
            ->get();
        $macroStatus = Ticket::getAllMacroStatus();
        $visibleList = Config::get('myapp.ticket_filters_visible_in_visible');
        $retData = [
            'users' => $users,
            'initiatives' => $initiatives,
            'macroStatus' => $macroStatus,
            'visibleList' => $visibleList,
        ];
        return ApiHelper::response(true, '', $retData, 200);
    }

    public function addRemovePriority(Request $request)
    {
        $requestData = $request->all();

        $status = true;
        if ($requestData['is_priority'] == true) {
            $message = __('messages.all_ticket_without_initiative.add_remove_priority_success_alt_add');
        } else {
            $message = __('messages.all_ticket_without_initiative.add_remove_priority_success_alt_remove');
        }
        $statusCode = 200;
        DB::beginTransaction();
        try {
            Ticket::whereIn('id', $requestData['ticket_ids'])->update(['is_priority' => $requestData['is_priority']]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }

    public function markAsVisibleInvisible(Request $request)
    {
        $requestData = $request->all();

        $status = true;
        if ($requestData['is_visible'] == true) {
            $message = __('messages.all_ticket_without_initiative.visible_success_alt_add');
        } else {
            $message = __('messages.all_ticket_without_initiative.visible_success_alt_remove');
        }
        $statusCode = 200;
        DB::beginTransaction();
        try {
            Ticket::whereIn('id', $requestData['ticket_ids'])->update(['is_visible' => $requestData['is_visible']]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }
}
