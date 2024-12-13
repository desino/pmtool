<?php

namespace App\Http\Controllers;

use App\Helper\ApiHelper;
use App\Http\Requests\Api\TicketCommentRequest;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Services\InitiativeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketCommentController extends Controller
{
    public function index(Request $request, $initiative_id, $ticket_id)
    {
        $status = false;
        $initiative = InitiativeService::getInitiative($request, $initiative_id);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $ticket = Ticket::find($ticket_id);
        if (!$ticket) {
            return ApiHelper::response($status, __('messages.ticket.not_found'), '', 400);
        }

        $ticketComments = TicketComment::select(
            'id',
            'comment',
            'created_at'
        )
            ->where('ticket_id', $ticket_id)
            ->orderBy('id', 'DESC')
            ->paginate(2);

        return ApiHelper::response($status, __('messages.ticket.comment_list'), $ticketComments, 200);
    }
    public function store(TicketCommentRequest $request, $initiative_id, $ticket_id)
    {

        $status = false;
        $retData = [
            'comment' => "",
        ];
        $initiative = InitiativeService::getInitiative($request);
        if (!$initiative) {
            return ApiHelper::response($status, __('messages.solution_design.section.initiative_not_exist'), '', 400);
        }

        $ticket = Ticket::find($ticket_id);
        if (!$ticket) {
            return ApiHelper::response($status, __('messages.ticket.not_found'), '', 400);
        }

        $taggedUsers = [];
        $userIds = "";
        if (!empty($request->post('tagged_users'))) {
            $taggedUsers = array_column($request->post('tagged_users'), 'id');
            $userIds = implode(',', $taggedUsers);
        }
        $insertData = [
            'initiative_id' => $initiative_id,
            'ticket_id' => $ticket_id,
            'type' => $request->post('type'),
            'comment' => $request->post('comment'),
            'tagged_users' => $userIds,
        ];

        $status = true;
        $message = __('messages.comment.store_success');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $ticketComment = TicketComment::create($insertData);
            $retData['comment'] = $ticketComment;
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, $retData, $statusCode);
    }
}
