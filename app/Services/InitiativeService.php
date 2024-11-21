<?php

namespace App\Services;

use App\Models\Initiative;
use App\Models\Ticket;
use App\Models\TicketAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InitiativeService
{
    public static function getOpportunityInitiative($request, $perPage, $status = null)
    {
        $filters = $request->post('filters');

        $initiative = Initiative::with(['client', 'initiativeEnvironments'])
            ->when($status != null, function ($q) use ($status) {
                $q->status($status);
            })
            ->when($filters['initiative_name'] != '', function ($q) use ($filters) {
                $q->name($filters['initiative_name']);
            })
            ->when($filters['client_id'] != '', function ($q) use ($filters) {
                $q->client($filters['client_id']);
            })
            ->orderBy('id', 'desc')
            ->paginate(30);
        return $initiative;
    }

    public static function getInitiative($request, $id = null)
    {
        if ($id == null) {
            $id = $request->post('initiative_id', null);
        }
        return Initiative::with('client', 'initiativeEnvironments', 'unprocessedRelease')->find($id);
    }

    public static function getInitiativesForHeaderSelectBox($request)
    {
        return Initiative::with('client')->orderBy('id', 'desc')->get();
    }

    public static function getInitiativeWithTestDeploymentTickets()
    {
        $retTestDeploymentInitiative = collect([]);
        Initiative::select(
            'id',
            'name',
            'client_id',
            'technical_owner_id',
        )
            ->with([
                'client' => function ($query) {
                    $query->select('id', 'name');
                },
                'tickets.actions' => function ($query) {
                    $query->select('ticket_id', 'user_id', 'action');
                }
            ])
            ->withCount(['tickets' => function ($query) {
                $query->readyForTestStatus();
            }])
            ->whereHas('tickets', function ($query) {
                $query->readyForTestStatus();
            })
            ->get()
            ->each(function ($initiative) use ($retTestDeploymentInitiative) {
                if ($initiative->technical_owner_id == Auth::id() || Auth::user()->is_admin) {
                    $retTestDeploymentInitiative->push($initiative);
                } else {
                    $isAllowToShowTickets = false;
                    $tickets = $initiative->tickets->where('status', Ticket::getStatusReadyForTest());
                    foreach ($tickets as $ticket) {
                        $developAction = $ticket->actions->where('action', TicketAction::getActionDevelop())->first();
                        if ($developAction && $developAction->user_id == Auth::id()) {
                            $isAllowToShowTickets = true;
                            break;
                        }
                    }
                    if ($isAllowToShowTickets) {
                        $retTestDeploymentInitiative->push($initiative);
                    }
                }
            });
        return $retTestDeploymentInitiative;
    }

    public static function getInitiativeWithAcceptanceDeploymentTickets()
    {
        $retAcceptanceDeploymentInitiative = collect([]);
        Initiative::select(
            'id',
            'name',
            'client_id',
            'technical_owner_id',
        )
            ->with([
                'client' => function ($query) {
                    $query->select('id', 'name');
                },
                'tickets.actions' => function ($query) {
                    $query->select('ticket_id', 'user_id', 'action');
                }
            ])
            ->withCount(['tickets' => function ($query) {
                $query->readyForAcceptanceStatus();
            }])
            ->whereHas('tickets', function ($query) {
                $query->readyForAcceptanceStatus();
            })
            ->get()
            ->each(function ($initiative) use ($retAcceptanceDeploymentInitiative) {
                if ($initiative->technical_owner_id == Auth::id() || Auth::user()->is_admin) {
                    $retAcceptanceDeploymentInitiative->push($initiative);
                } else {
                    $isAllowToShowTickets = false;
                    $tickets = $initiative->tickets->where('status', Ticket::getStatusReadyForACC());
                    foreach ($tickets as $ticket) {
                        $developAction = $ticket->actions->where('action', TicketAction::getActionDevelop())->first();
                        if ($developAction && $developAction->user_id == Auth::id()) {
                            $isAllowToShowTickets = true;
                            break;
                        }
                    }
                    if ($isAllowToShowTickets) {
                        $retAcceptanceDeploymentInitiative->push($initiative);
                    }
                }
            });
        return $retAcceptanceDeploymentInitiative;
    }

    public static function getInitiativeWithProductionDeploymentTickets()
    {
        $retProductionDeploymentInitiative = collect([]);
        Initiative::select(
            'initiatives.id',
            'initiatives.name',
            'initiatives.client_id',
            'initiatives.technical_owner_id',
            DB::raw('COUNT(tickets.id) as tickets_count')
        )
            ->with([
                'client' => function ($query) {
                    $query->select('id', 'name');
                },
                'tickets.actions' => function ($query) {
                    $query->select('ticket_id', 'user_id', 'action');
                }
            ])
            ->JOIN('releases', 'releases.initiative_id', '=', 'initiatives.id')
            ->JOIN('release_tickets', 'release_tickets.release_id', '=', 'releases.id')
            ->JOIN('tickets', function ($query) {
                $query->on('tickets.id', '=', 'release_tickets.ticket_id')
                    ->where('tickets.status', Ticket::getStatusReadyForPRD());
            })
            ->groupBy('initiatives.id')
            ->groupBy('initiatives.name')
            ->groupBy('initiatives.client_id')
            ->having('tickets_count', '>', 0)
            ->get()->each(function ($initiative) use ($retProductionDeploymentInitiative) {
                if ($initiative->technical_owner_id == Auth::id() || Auth::user()->is_admin) {
                    $retProductionDeploymentInitiative->push($initiative);
                } else {
                    $isAllowToShowTickets = false;
                    $tickets = $initiative->tickets->where('status', Ticket::getStatusReadyForPRD());
                    foreach ($tickets as $ticket) {
                        $developAction = $ticket->actions->where('action', TicketAction::getActionDevelop())->first();
                        if ($developAction && $developAction->user_id == Auth::id()) {
                            $isAllowToShowTickets = true;
                            break;
                        }
                    }
                    if ($isAllowToShowTickets) {
                        $retProductionDeploymentInitiative->push($initiative);
                    }
                }
            });
        return $retProductionDeploymentInitiative;
    }
}
