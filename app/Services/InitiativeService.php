<?php

namespace App\Services;

use App\Models\Initiative;
use App\Models\ReleaseTicket;
use App\Models\Ticket;
use App\Models\TicketAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InitiativeService
{
    public static function getOpportunityInitiative($request, $perPage)
    {
        $filters = $request->post('filters');

        // [Initiative::getStatusOpportunity(), Initiative::getStatusLost()]

        $initiative = Initiative::with(['client', 'initiativeEnvironments'])
            // ->when($status != null, function ($q) use ($status) {
            //     $q->status($status);
            // })
            ->when($filters['is_opportunities'] || $filters['is_lost'], function ($q) use ($filters) {
                $status = [Initiative::getStatusOpportunity()];
                if ($filters['is_opportunities'] && $filters['is_lost']) {
                    $status = [Initiative::getStatusOpportunity(), Initiative::getStatusLost()];
                } else if ($filters['is_lost']) {
                    $status = [Initiative::getStatusLost()];
                }
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
        return Initiative::select('id', 'name', 'client_id', 'status', 'share_point_url', 'technical_owner_id')
            ->with([
                'client' => function ($query) {
                    $query->select('id', 'name');
                },
                'initiativeEnvironments' => function ($query) {
                    $query->select('id', 'name', 'initiative_id', 'url');
                }
            ])
            ->status([Initiative::getStatusOpportunity(), Initiative::getStatusOngoing(), Initiative::getStatusClosed()])
            ->orderBy('id', 'desc')->get();
    }

    public static function getInitiativeWithTestDeploymentTickets()
    {
        $initiative = Initiative::select(
            'initiatives.id',
            'initiatives.name',
            'initiatives.client_id',
            'initiatives.technical_owner_id',
            DB::raw(
                '(SELECT COUNT(tickets.id) 
                FROM tickets 
                WHERE tickets.initiative_id = initiatives.id 
                AND tickets.status = ' . Ticket::getStatusReadyForTest() . ') as tickets_count'
            )
        )
            ->join('tickets', 'tickets.initiative_id', '=', 'initiatives.id')
            ->where('tickets.status', Ticket::getStatusReadyForTest())
            ->where(function ($query) {
                if (!Auth::user()->is_admin) {
                    $query->where('initiatives.technical_owner_id', Auth::id())
                        ->orWhereExists(function ($actionQuery) {
                            $actionQuery->select(DB::raw(1))
                                ->from('ticket_actions')
                                ->whereColumn('ticket_actions.ticket_id', 'tickets.id')
                                ->where('ticket_actions.user_id', Auth::id())
                                ->where('ticket_actions.action', TicketAction::getActionDevelop());
                        });
                }
            })
            ->groupBy(
                'initiatives.id',
                'initiatives.name',
                'initiatives.client_id',
                'initiatives.technical_owner_id'
            )
            ->get();
        return $initiative;

        // $retTestDeploymentInitiative = collect([]);
        // Initiative::select(
        //     'id',
        //     'name',
        //     'client_id',
        //     'technical_owner_id',
        // )
        //     ->with([
        //         'client' => function ($query) {
        //             $query->select('id', 'name');
        //         },
        //         'tickets.actions' => function ($query) {
        //             $query->select('ticket_id', 'user_id', 'action');
        //         }
        //     ])
        //     ->withCount(['tickets' => function ($query) {
        //         $query->readyForTestStatus();
        //     }])
        //     ->whereHas('tickets', function ($query) {
        //         $query->readyForTestStatus();
        //     })
        //     ->get()
        //     ->each(function ($initiative) use ($retTestDeploymentInitiative) {
        //         if ($initiative->technical_owner_id == Auth::id() || Auth::user()->is_admin) {
        //             $initiative->makeHidden('tickets');
        //             $retTestDeploymentInitiative->push($initiative);
        //         } else {
        //             $isAllowToShowTickets = false;
        //             $tickets = $initiative->tickets->where('status', Ticket::getStatusReadyForTest());
        //             foreach ($tickets as $ticket) {
        //                 $developAction = $ticket->actions->where('action', TicketAction::getActionDevelop())->first();
        //                 if ($developAction && $developAction->user_id == Auth::id()) {
        //                     $isAllowToShowTickets = true;
        //                     break;
        //                 }
        //             }
        //             if ($isAllowToShowTickets) {
        //                 $initiative->makeHidden('tickets');
        //                 $retTestDeploymentInitiative->push($initiative);
        //             }
        //         }
        //     });
        // return $retTestDeploymentInitiative;
    }

    public static function getInitiativeWithAcceptanceDeploymentTickets()
    {
        $initiative = Initiative::select(
            'initiatives.id',
            'initiatives.name',
            'initiatives.client_id',
            'initiatives.technical_owner_id',
            DB::raw(
                '(SELECT COUNT(tickets.id) 
                FROM tickets 
                WHERE tickets.initiative_id = initiatives.id 
                AND tickets.status = ' . Ticket::getStatusReadyForACC() . ') as tickets_count'
            )
        )
            ->join('tickets', 'tickets.initiative_id', '=', 'initiatives.id')
            ->where('tickets.status', Ticket::getStatusReadyForACC())
            ->where(function ($query) {
                if (!Auth::user()->is_admin) {
                    $query->where('initiatives.technical_owner_id', Auth::id())
                        ->orWhereExists(function ($actionQuery) {
                            $actionQuery->select(DB::raw(1))
                                ->from('ticket_actions')
                                ->whereColumn('ticket_actions.ticket_id', 'tickets.id')
                                ->where('ticket_actions.user_id', Auth::id())
                                ->where('ticket_actions.action', TicketAction::getActionDevelop());
                        });
                }
            })
            ->groupBy(
                'initiatives.id',
                'initiatives.name',
                'initiatives.client_id',
                'initiatives.technical_owner_id'
            )
            ->get();
        return $initiative;

        // $retAcceptanceDeploymentInitiative = collect([]);
        // Initiative::select(
        //     'id',
        //     'name',
        //     'client_id',
        //     'technical_owner_id',
        // )
        //     ->with([
        //         'client' => function ($query) {
        //             $query->select('id', 'name');
        //         },
        //         'tickets.actions' => function ($query) {
        //             $query->select('ticket_id', 'user_id', 'action');
        //         }
        //     ])
        //     ->withCount(['tickets' => function ($query) {
        //         $query->readyForAcceptanceStatus();
        //     }])
        //     ->whereHas('tickets', function ($query) {
        //         $query->readyForAcceptanceStatus();
        //     })
        //     ->get()
        //     ->each(function ($initiative) use ($retAcceptanceDeploymentInitiative) {
        //         if ($initiative->technical_owner_id == Auth::id() || Auth::user()->is_admin) {
        //             $initiative->makeHidden('tickets');
        //             $retAcceptanceDeploymentInitiative->push($initiative);
        //         } else {
        //             $isAllowToShowTickets = false;
        //             $tickets = $initiative->tickets->where('status', Ticket::getStatusReadyForACC());
        //             foreach ($tickets as $ticket) {
        //                 $developAction = $ticket->actions->where('action', TicketAction::getActionDevelop())->first();
        //                 if ($developAction && $developAction->user_id == Auth::id()) {
        //                     $isAllowToShowTickets = true;
        //                     break;
        //                 }
        //             }
        //             if ($isAllowToShowTickets) {
        //                 $initiative->makeHidden('tickets');
        //                 $retAcceptanceDeploymentInitiative->push($initiative);
        //             }
        //         }
        //     });
        // return $retAcceptanceDeploymentInitiative;
    }

    public static function getInitiativeWithProductionDeploymentTickets()
    {

        // $tickets = ReleaseTicket::select(
        //     'release_tickets.release_id',
        //     'releases.initiative_id',
        //     'initiatives.id',
        //     'initiatives.name',
        //     'initiatives.client_id',
        //     'clients.name as client_name',
        //     DB::raw(
        //         '(SELECT COUNT(tickets.id) 
        //         FROM tickets 
        //         WHERE tickets.initiative_id = releases.initiative_id 
        //         AND tickets.status = ' . Ticket::getStatusReadyForPRD() . ') as tickets_count'
        //     )
        // )
        //     ->join('releases', 'releases.id', '=', 'release_tickets.release_id')
        //     ->join('tickets', 'tickets.id', '=', 'release_tickets.ticket_id')
        //     ->join('initiatives', 'initiatives.id', '=', 'releases.initiative_id')
        //     ->join('clients', 'clients.id', '=', 'initiatives.client_id')
        //     ->where('tickets.status', Ticket::getStatusReadyForPRD())
        //     ->where(function ($query) {
        //         if (Auth::user()->is_admin) {
        //             $query->where('initiatives.technical_owner_id', Auth::id())
        //                 ->orWhereExists(function ($actionQuery) {
        //                     $actionQuery->select(DB::raw(1))
        //                         ->from('ticket_actions')
        //                         ->whereColumn('ticket_actions.ticket_id', 'tickets.id')
        //                         ->where('ticket_actions.user_id', Auth::id())
        //                         ->where('ticket_actions.action', TicketAction::getActionDevelop());
        //                 });
        //         }
        //     })
        //     ->groupBy(
        //         'release_tickets.release_id',
        //         'releases.initiative_id',
        //     )
        //     ->get();
        // return $tickets;

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
            ->get()
            ->each(function ($initiative) use ($retProductionDeploymentInitiative) {
                if ($initiative->technical_owner_id == Auth::id() || Auth::user()->is_admin) {
                    $initiative->makeHidden('tickets');
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
                        $initiative->makeHidden('tickets');
                        $retProductionDeploymentInitiative->push($initiative);
                    }
                }
            });
        return $retProductionDeploymentInitiative;
    }
}
