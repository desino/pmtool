<?php

namespace App\Services;

use App\Models\Initiative;
use App\Models\Project;
use App\Models\Release;
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

    public static function updateTicketComposedName($ticket, string $name, int $type)
    {
        $existComposedName = $ticket->composed_name;
        $existName = $ticket->name;
        $composedName = $existComposedName;
        if (!str_contains($existComposedName, $name)) {
            $composedName = str_replace($existName, $name, $composedName);
        }
        $existTypeCode = Ticket::getTypeOfCode($ticket->type);
        $newTypeCode = Ticket::getTypeOfCode($type);
        if (!str_contains($existComposedName, $newTypeCode)) {
            $composedName = str_replace($existTypeCode, $newTypeCode, $composedName);
        }
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
        $sectionFunctionalities = Section::select(['id', 'name', 'display_name'])
            ->with(['functionalities' => function ($q) {
                $q->select(
                    'id',
                    'section_id',
                    'name',
                    'display_name',
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
        $projects = Project::where('initiative_id', $initiative_id)->where('status', 1)->get();
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
        $ticketActions = Arr::where($ticketActions, function ($value) {
            return $value['is_checked'] == 1;
        });
        $ticketActions = Arr::where($ticketActions, function ($value) {
            return !isset($value['status']) || $value['status'] != TicketAction::getStatusDone();
        });
        $actions = array_column($ticketActions, 'action');

        array_multisort($actions, SORT_ASC, $ticketActions);

        $insertedOrUpdateIds = [];
        foreach ($ticketActions as $ticketActionKey => $ticketAction) {
            $condition = ['ticket_id' => $ticketId, 'action' => $ticketAction['action']];
            $fieldsToUpdateOrCreate = [
                'ticket_id' => $ticketId,
                'action' => $ticketAction['action'],
                'user_id' => $ticketAction['user_id'],
                // 'status' => $ticketAction['status'] ?? Self::getTicketActionStatus($ticketActionKey, $ticketAction, $autoWaitForClientApproval),
                'status' => Self::getTicketActionStatus($ticketActionKey, $ticketAction, $autoWaitForClientApproval),
            ];
            $ticketAction = TicketAction::updateOrCreate($condition, $fieldsToUpdateOrCreate);
            $insertedOrUpdateIds[] = $ticketAction->id;
        }
        if (!empty($insertedOrUpdateIds)) {
            TicketAction::whereNotIn('id', $insertedOrUpdateIds)
                ->where('ticket_id', $ticketId)
                ->where('status', '!=', TicketAction::getStatusDone())
                // ->where('action', $ticketAction['action'])
                ->delete();
        }
    }

    public static function updateTicketActions($ticket, $actionId, $status)
    {
        $ticketActions = $ticket->actions;
        $nextTicketAction = "";
        foreach ($ticketActions as $key => $ticketAction) {
            if ($ticketAction->id == $actionId) {
                $ticketAction->status = $status;
                $ticketAction->save();
                if (isset($ticketActions[$key + 1])) {
                    $nextTicketAction = $ticketActions[$key + 1];
                }
                break;
            }
        }

        if ($nextTicketAction) {
            // if ($ticket->auto_wait_for_client_approval) {
            //     if (
            //         ($nextTicketAction->action == TicketAction::getActionDetailTicket() || $nextTicketAction->action == TicketAction::getActionClarifyAndEstimate())
            //         && $nextTicketAction->status == TicketAction::getStatusWaitingForDependantAction()
            //     ) {
            //         $nextTicketAction->status = TicketAction::getStatusActionable();
            //         $nextTicketAction->save();
            //     }
            // } else {
            //     if ($nextTicketAction->status == TicketAction::getStatusWaitingForDependantAction()) {
            //         $nextTicketAction->status = TicketAction::getStatusActionable();
            //         $nextTicketAction->save();
            //     }
            // }

            if ($nextTicketAction->status == TicketAction::getStatusWaitingForDependantAction()) {
                $nextTicketAction->status = TicketAction::getStatusActionable();
                $nextTicketAction->save();
            }
        }
    }

    public static function updateTicketPreviousActions($ticket, $actionId, $status)
    {
        $ticketActions = $ticket->actions;
        $previousTicketAction = null;
        foreach ($ticketActions as $key => $ticketAction) {
            if ($ticketAction->id == $actionId) {
                $ticketAction->status = $status;
                $ticketAction->save();
                if ($key > 0) {
                    $previousTicketAction = $ticketActions[$key - 1];
                }
                break;
            }
        }

        if ($previousTicketAction) {
            if ($previousTicketAction->status == TicketAction::getStatusDone()) {
                $previousTicketAction->status = TicketAction::getStatusActionable();
                $previousTicketAction->save();
            }
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
        $ticketActions = TicketAction::where('ticket_id', $ticket->id)
            ->orderBy('action')
            ->get();

        $taskStatus = null;
        foreach ($ticketActions as $ticketAction) {
            switch ($ticketAction->action) {
                case TicketAction::getActionDetailTicket():
                    if ($ticketAction->status === 1 && $taskStatus == null) {
                        $taskStatus = Ticket::getStatusOngoing();
                    }
                    if ($ticketAction->status === 0 && $taskStatus == null) {
                        $taskStatus = Ticket::getStatusWaitForClient();
                    }
                    break;
                case TicketAction::getActionClarifyAndEstimate():
                    if ($ticketAction->status === 1 && $taskStatus == null) {
                        $taskStatus = Ticket::getStatusOngoing();
                    }
                    if ($ticketAction->status === 0 && $taskStatus == null) {
                        $taskStatus = Ticket::getStatusWaitForClient();
                    }
                    break;
                case TicketAction::getActionDevelop():
                    if ($ticket->auto_wait_for_client_approval && $ticketAction->status === 0  && $taskStatus == null) {
                        $taskStatus = Ticket::getStatusWaitForClient();
                    }
                    if (!$ticket->auto_wait_for_client_approval && $ticketAction->status === 1  && $taskStatus == null) {
                        $taskStatus = Ticket::getStatusOngoing();
                    }
                    break;
                case TicketAction::getActionTest():
                    if ($ticket->auto_wait_for_client_approval && $ticketAction->status === 0 && $taskStatus == null) {
                        $taskStatus = Ticket::getStatusWaitForClient();
                    }
                    if (!$ticket->auto_wait_for_client_approval && $ticketAction->status === 1 && $taskStatus == null) {
                        $taskStatus = Ticket::getStatusReadyForTest();
                    }
                    break;
                case TicketAction::getActionValidate():
                    if ($ticket->auto_wait_for_client_approval && $ticketAction->status === 0 && $taskStatus == null) {
                        $taskStatus = Ticket::getStatusWaitForClient();
                    }
                    if (!$ticket->auto_wait_for_client_approval && $ticketAction->status === 1 && $taskStatus == null) {
                        $taskStatus = Ticket::getStatusReadyForACC();
                    }
                    break;
            }
        }

        $ticketActionsDoneCount = $ticketActions->where('status', 2)->count();
        if ($ticketActionsDoneCount == $ticketActions->count()) {
            $taskStatus = Ticket::getStatusReadyForPRD();
        }
        $ticket->status = $taskStatus;
        $ticket->save();
    }

    public static function createMacroStatusAndUpdateTicket($ticket)
    {
        $ticketStatus = $ticket->status;
        $ticketCurrentAction = $ticket->currentAction->action ?? 0;
        $macroStatus = 0;
        if ($ticketCurrentAction == TicketAction::getActionDetailTicket() && $ticketStatus == Ticket::getStatusOngoing()) {
            $macroStatus = Ticket::MACRO_STATUS_DETAIL_TICKET;
        } else if ($ticketCurrentAction == TicketAction::getActionClarifyAndEstimate() && $ticketStatus == Ticket::getStatusOngoing()) {
            $macroStatus = Ticket::MACRO_STATUS_CLARIFY_AND_ESTIMATE;
        } else if ($ticketCurrentAction == TicketAction::getActionDevelop() && $ticketStatus == Ticket::getStatusWaitForClient()) {
            $macroStatus = Ticket::MACRO_STATUS_DEVELOP_WAIT_FOR_CLIENT;
        } else if ($ticketCurrentAction == TicketAction::getActionDevelop() && $ticketStatus == Ticket::getStatusOngoing()) {
            $macroStatus = Ticket::MACRO_STATUS_DEVELOP;
        } else if ($ticketCurrentAction == TicketAction::getActionTest() && $ticketStatus == Ticket::getStatusReadyForTest()) {
            $macroStatus = Ticket::MACRO_STATUS_TEST_WAIT_FOR_DEPLOYMENT_TO_TEST;
        } else if ($ticketCurrentAction == TicketAction::getActionTest() && $ticketStatus == Ticket::getStatusOngoing()) {
            $macroStatus = Ticket::MACRO_STATUS_TEST;
        } else if ($ticketCurrentAction == TicketAction::getActionTest() && $ticketStatus == Ticket::getStatusWaitForClient()) {
            $macroStatus = Ticket::MACRO_STATUS_TEST_WAIT_FOR_CLIENT;
        } else if ($ticketCurrentAction == TicketAction::getActionValidate() && $ticketStatus == Ticket::getStatusReadyForACC()) {
            $macroStatus = Ticket::MACRO_STATUS_VALIDATE_WAITING_FOR_DEPLOYMENT_TO_ACC;
        } else if ($ticketCurrentAction == TicketAction::getActionValidate() && $ticketStatus == Ticket::getStatusOngoing()) {
            $macroStatus = Ticket::MACRO_STATUS_VALIDATE;
        } else if ($ticketCurrentAction == TicketAction::getActionValidate() && $ticketStatus == Ticket::getStatusWaitForClient()) {
            $macroStatus = Ticket::MACRO_STATUS_VALIDATE_WAIT_FOR_CLIENT;
        } else if ($ticketStatus == Ticket::getStatusReadyForPRD()) {
            $macroStatus = Ticket::MACRO_STATUS_READY_FOR_DEPLOYMENT_TO_TEST;
        } else if ($ticketStatus == Ticket::getStatusDone()) {
            $macroStatus = Ticket::MACRO_STATUS_DONE;
        }
        $ticket->macro_status = $macroStatus;
        $ticket->save();
    }

    public static function deleteActions($id)
    {
        TicketAction::where('ticket_id', $id)->delete();
    }

    public static function getTicketCountCanNotMatchWithStatus($ids, $status)
    {
        $ticketsCount = Ticket::whereIn('id', $ids)->where('status', '!=', $status)->get();
        return $ticketsCount->count();
    }

    public static function createReleaseVersion($isMajor)
    {
        $releaseCount = Release::get()->count();
        if ($releaseCount == 0) {
            return 1;
        }
        $release = Release::where('status', Release::PROCESSED_RELEASE)->orderBy('id', 'desc')->first();
        if ($isMajor) {
            $releaseVersion = round($release->version + 1);
        } else {
            $releaseVersion = round($release->version + 0.1, 1);
        }
        return $releaseVersion;
    }

    public static function createReleaseName($releaseVersion)
    {
        $defaultName = __('messages.release.default_name');
        $releaseName = $defaultName . " " . $releaseVersion;
        return $releaseName;
    }

    public static function getTicketActionWithDefaultData($actions, $initiative, $ticket = null)
    {
        $selectedTicketActions = [TicketAction::getActionDevelop(), TicketAction::getActionTest(), TicketAction::getActionValidate()];
        if ($ticket && $ticket->actions) {
            $selectedTicketActions = $ticket->actions->pluck('action')->toArray();
            $selectedTicketActions[] = TicketAction::getActionDevelop();
        }
        $disabledTicketActions = [TicketAction::getActionDevelop()];
        $disabledTicketActionUsers = [];
        if ($ticket && $ticket?->actions) {
            $disabledTicketActions = $ticket->actions->where(function ($q) {
                $q->where('status', TicketAction::getStatusDone());
                $q->orWhere('status', TicketAction::getActionDevelop());
            })->pluck('action')->toArray();
            $disabledTicketActions[] = TicketAction::getActionDevelop();
            $disabledTicketActionUsers = $ticket->actions->where('status', TicketAction::getStatusDone())->pluck('action')->toArray();
        }
        foreach ($actions as &$action) {
            $action['action'] = $action['id'];
            $action['is_checked'] = in_array($action['id'], $selectedTicketActions);
            $action['is_disabled'] = in_array($action['id'], $disabledTicketActions);
            $action['is_user_select_box_disabled'] = in_array($action['id'], $disabledTicketActionUsers);
            $currentAction = [];
            if ($ticket && $ticket?->actions) {
                $currentAction = $ticket->actions->where('action', $action['id'])->first();
            }
            switch ($action['id']) {
                case TicketAction::getActionDetailTicket():
                case TicketAction::getActionValidate():
                    $action['user_id'] = $initiative->functional_owner_id;
                    if (!empty($currentAction) && $currentAction?->action == $action['id']) {
                        $action['user_id'] = $currentAction->user_id;
                        $action['status'] = $currentAction->status;
                    }
                    break;
                case TicketAction::getActionClarifyAndEstimate():
                case TicketAction::getActionDevelop():
                    $action['user_id'] = $initiative->technical_owner_id;
                    if (!empty($currentAction) && $currentAction?->action == $action['id']) {
                        $action['user_id'] = $currentAction->user_id;
                        $action['status'] = $currentAction->status;
                    }
                    break;
                case TicketAction::getActionTest():
                    $action['user_id'] = $initiative->quality_owner_id;
                    if (!empty($currentAction) && $currentAction?->action == $action['id']) {
                        $action['user_id'] = $currentAction->user_id;
                        $action['status'] = $currentAction->status;
                    }
                    break;
                default:
                    $action['user_id'] = "";
            }
        }
        return $actions;
    }
}
