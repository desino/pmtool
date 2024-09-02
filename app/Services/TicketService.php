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

        $retAction = "";
        foreach ($ticketActions as $ticketActionKey => $ticketAction) {
            $createdArray = [
                'ticket_id' => $ticketId,
                'action' => $ticketAction['action'],
                'user_id' => $ticketAction['user_id'],
                'status' => Self::getTicketActionStatus($ticketActionKey, $ticketAction, $autoWaitForClientApproval),
            ];
            // print('<pre>');
            // print_r($createdArray);
            // print('</pre>');
            $ticketAction = TicketAction::create($createdArray);
            // if ($ticketActionKey == 0) {
            //     $retAction = $ticketAction;
            // }
        }
        exit;
        return $retAction;
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
}
