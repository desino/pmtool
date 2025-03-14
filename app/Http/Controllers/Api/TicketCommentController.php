<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Ticket;
use App\Helper\ApiHelper;
use Illuminate\Http\Request;
use App\Models\TicketComment;
use Illuminate\Support\Facades\DB;
use App\Services\InitiativeService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\MicrosoftTeamsService;
use App\Http\Requests\Api\TicketCommentRequest;

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

        $ticketComments = $this->selectDataForListAndCreatedUpdated()
            ->when($request->get('first_comment_id') > 0, function ($query) use ($request) {
                $query->where('ticket_comments.id', '<', $request->get('first_comment_id'));
            })
            ->where('ticket_comments.ticket_id', $ticket_id)
            ->orderBy('ticket_comments.id', 'DESC')
            ->limit(20)
            ->get();

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

        $userIds = "";
        if (!empty($request->post('tagged_users'))) {
            $userIds = implode(',', $request->post('tagged_users'));
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
            $ticketComment = $this->selectDataForListAndCreatedUpdated()->where('ticket_comments.id', $ticketComment->id)->first();
            $retData['comment'] = $ticketComment;
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }

        if ($statusCode == 200 && count($request->post('tagged_users', [])) > 0) {
            $recipientUsers = User::whereIn('id', $request->post('tagged_users'))
                ->whereNotNull('teams_webhook_url')
                ->get();
            $actionByUser   = Auth::user();
            $actionData     = (object) [
                'text'          => $ticketComment->comment,
                'actionElement' => 'comment',
                'actionType'    => 'add',
            ];
            MicrosoftTeamsService::sendNotification(ticket: $ticket, actionByUser: $actionByUser, actionData: $actionData, recipientUsers: $recipientUsers);
        }

        return ApiHelper::response($status, $message, $retData, $statusCode);
    }

    public function update(TicketCommentRequest $request, $initiative_id, $ticket_id)
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

        $ticketComment = TicketComment::find($request->get('id'));
        if (!$ticketComment) {
            return ApiHelper::response($status, __('messages.comment.not_found'), '', 400);
        }

        $userIds = "";
        if (!empty($request->post('tagged_users'))) {
            $userIds = implode(',', $request->post('tagged_users'));
        }

        $updateData = [
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
            $ticketComment->update($updateData);
            $ticketComment = $this->selectDataForListAndCreatedUpdated()->where('ticket_comments.id', $ticketComment->id)->first();
            $retData['comment'] = $ticketComment;
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }

        if ($statusCode == 200 && count($request->post('tagged_users', [])) > 0) {
            $recipientUsers = User::whereIn('id', $request->post('tagged_users'))
                ->whereNotNull('teams_webhook_url')
                ->get();
            $actionByUser   = Auth::user();
            $actionData     = (object) [
                'text'          => $ticketComment->comment,
                'actionElement' => 'comment',
                'actionType'    => 'edit',
            ];
            MicrosoftTeamsService::sendNotification(ticket: $ticket, actionByUser: $actionByUser, actionData: $actionData, recipientUsers: $recipientUsers);
        }
        return ApiHelper::response($status, $message, $retData, $statusCode);
    }

    public function delete(Request $request, $initiative_id, $ticket_id)
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

        $status = true;
        $message = __('messages.comment.deleted_successfully');
        $statusCode = 200;
        DB::beginTransaction();
        try {
            $ticketComment = TicketComment::find($request->get('id'));
            $ticketComment->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $status = false;
            $message = __('messages.something_went_wrong');
            $statusCode = 500;
            logger()->error($e);
        }
        return ApiHelper::response($status, $message, '', $statusCode);
    }

    public function selectDataForListAndCreatedUpdated()
    {
        $ticketComment = TicketComment::select(
            'ticket_comments.id',
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
            DB::RAW('IF (ticket_comments.updated_by , updated_user.email, created_user.email) AS created_updated_user_email'),
        )
            ->leftJoin('users AS created_user', 'created_user.id', 'ticket_comments.created_by')
            ->leftJoin('users AS updated_user', 'updated_user.id', 'ticket_comments.updated_by');
        return clone $ticketComment;
    }
}
