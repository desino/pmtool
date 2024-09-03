<?php

namespace App\Services;

use App\Models\Initiative;
use App\Models\Project;
use App\Models\Section;
use App\Models\Ticket;
use App\Models\TicketAction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class TicketService
{
    public static function generateTicketComposedName(int $initiativeId, string $name, int $type)
    {
        $typeCode = Ticket::getTypeOfCode($type);
        $initiative = Initiative::find($initiativeId);
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->format('m');
        $ticketCount = 0;
        $lastTicket = Ticket::where('initiative_id', $initiativeId)
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$lastTicket) {
            $ticketCount = 1;
        } else {
            $lastTicketCreatedMonth = Carbon::parse($lastTicket->created_at)->format('m');
            if ($lastTicketCreatedMonth != $currentMonth) {
                $initiative->ticket_composed_name_count = 0;
                $initiative->save();
                $ticketCount = 1;
            } else {
                $ticketCount = $initiative->ticket_composed_name_count + 1;
            }
        }
        $composedName = $typeCode . $currentYear . $currentMonth . '-' . $ticketCount . ':' . $name;
        $retData = [
            'composed_name' => $composedName,
        ];
        return $retData;
    }

    public static function incrementInitiativeTicketCount(int $initiativeId)
    {
        $initiative = Initiative::find($initiativeId);
        $initiative->ticket_composed_name_count = $initiative->ticket_composed_name_count + 1;
        $initiative->save();
    }

    public static function getSectionFunctionality(int $initiative_id)
    {
        $sectionFunctionalities = Section::select(['id', 'name',])
            ->with(['functionalities' => function ($q) {
                $q->select(
                    'id',
                    'section_id',
                    'name',
                    'id'
                );
            }])
            ->where('initiative_id', $initiative_id)
            ->get();
        return $sectionFunctionalities;
    }

    public static function getTicketTypes()
    {
        $ticketTypes = Ticket::getAllTypes();
        return $ticketTypes;
    }

    public static function getInitiativeProject(int $initiative_id)
    {
        $projects = Project::where('initiative_id', $initiative_id)->get();
        return $projects;
    }

    public static function getUsers()
    {
        $user = User::get();
        return $user;
    }

    public static function getInitiative(int $initiative_id)
    {
        $initiative = Initiative::find($initiative_id);
        return $initiative;
    }

    public static function insertTicketActions(int $ticketId, array $ticketActions, bool $autoWaitForClientApproval)
    {
        $actions = array_column($ticketActions, 'action');
        array_multisort($actions, SORT_ASC, $ticketActions);

        foreach ($ticketActions as $ticketActionKey => $ticketAction) {
            $createdArray = [
                'ticket_id' => $ticketId,
                'action' => $ticketAction['action'],
                'user_id' => $ticketAction['user_id'],
                'status' => Self::getTicketActionStatus($ticketActionKey, $ticketAction, $autoWaitForClientApproval),
            ];
            TicketAction::create($createdArray);
        }
    }

    public static function  getTicketActionStatus($index, $ticketAction, $autoWaitForClientApproval)
    {
        if ($autoWaitForClientApproval && $index == 0 && ($ticketAction['action'] == 1 || $ticketAction['action'] == 2)) {
            return TicketAction::getStatusActionable();
        }

        if (!$autoWaitForClientApproval && $index == 0) {
            return TicketAction::getStatusActionable();
        }

        return TicketAction::getStatusWaitingForDependantAction();
    }

    public static function updateTicketStatus($ticket)
    {
        // $ticketAction = TicketAction::where('ticket_id', $ticket->id)
        //     ->where('status', '!=', 2)
        //     ->orderBy('action')
        //     ->first();

        // switch ($ticketAction->action) {
        //     case 1:
        //         $ticket->status = Ticket::getStatusOngoing();
        //         break;
        //     case 2:
        //         $ticket->status = Ticket::getStatusOngoing();
        //         break;
        //     case 3:
        //         $ticket->status = Ticket::getStatusOngoing();
        //         if ($ticket->auto_wait_for_client_approval && $ticketAction->status === 0) {
        //             $ticket->status = Ticket::getStatusWaitForClient();
        //         }
        //         break;
        //     case 4:
        //         $ticket->status = Ticket::getStatusOngoing();
        //         if ($ticket->auto_wait_for_client_approval && $ticketAction->action === 0) {
        //             $ticket->status = Ticket::getStatusWaitForClient();
        //         }
        //         break;
        //     case 5:
        //         $ticket->status = Ticket::getStatusOngoing();
        //         if ($ticket->auto_wait_for_client_approval && $ticketAction->action === 0) {
        //             $ticket->status = Ticket::getStatusWaitForClient();
        //         }
        //         break;
        // }

        $ticketActions = TicketAction::where('ticket_id', $ticket->id)
            ->orderBy('action')
            ->get();
        $taskStatus = "";
        $isThirdActionStatusDone = false;
        $isFourthActionStatusDone = false;
        $isFifthActionStatusDone = false;
        foreach ($ticketActions as $ticketAction) {
            switch ($ticketAction->action) {
                case 1:
                    if ($ticketAction->status === 1) {
                        $taskStatus = Ticket::getStatusOngoing();
                    }
                    break;
                case 2:
                    if ($ticketAction->status === 1) {
                        $taskStatus = Ticket::getStatusOngoing();
                    }
                    break;
                case 3:
                    if ($ticket->auto_wait_for_client_approval && $ticketAction->status === 0) {
                        $taskStatus = Ticket::getStatusWaitForClient();
                    }
                    if (!$ticket->auto_wait_for_client_approval && $ticketAction->status === 1) {
                        $taskStatus = Ticket::getStatusOngoing();
                    }
                    if ($ticketAction->status === 2) {
                        $isThirdActionStatusDone = true;
                    }
                    break;
                case 4:
                    if ($ticket->auto_wait_for_client_approval && $ticketAction->status === 0) {
                        $taskStatus = Ticket::getStatusWaitForClient();
                    }
                    if (!$ticket->auto_wait_for_client_approval && $ticketAction->status === 1) {
                        $taskStatus = Ticket::getStatusOngoing();
                    }
                    if ($isThirdActionStatusDone && $ticketAction->status === 1) {
                        $taskStatus = Ticket::getStatusReadyForTest();
                    }
                    if ($ticketAction->status === 2) {
                        $isFourthActionStatusDone = true;
                    }
                    break;
                case 5:
                    if ($ticket->auto_wait_for_client_approval && $ticketAction->status === 0) {
                        $taskStatus = Ticket::getStatusWaitForClient();
                    }
                    if (!$ticket->auto_wait_for_client_approval && $ticketAction->status === 1) {
                        $taskStatus = Ticket::getStatusOngoing();
                    }
                    if ($isFourthActionStatusDone && $ticketAction->status === 1) {
                        $taskStatus = Ticket::getStatusReadyForACC();
                    }
                    if ($ticketAction->status === 2) {
                        $isFifthActionStatusDone = true;
                    }
                    if ($isThirdActionStatusDone && $isFourthActionStatusDone && $isFifthActionStatusDone) {
                        $taskStatus = Ticket::getStatusReadyForPRD();
                    }
                    break;
            }
        }
        $ticket->status = $taskStatus;
        $ticket->save();
    }
}
