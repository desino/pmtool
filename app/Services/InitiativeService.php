<?php

namespace App\Services;

use App\Models\Initiative;
use App\Models\Ticket;
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
            ->paginate($perPage);
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
        $testDeploymentInitiative = Initiative::select(
            'id',
            'name',
            'client_id'
        )
            ->with(['client' => function ($query) {
                $query->select('id', 'name');
            }])
            ->withCount(['tickets' => function ($query) {
                $query->readyForTestStatus();
            }])
            ->whereHas('tickets', function ($query) {
                $query->readyForTestStatus();
            })
            ->get();
        return $testDeploymentInitiative;
    }

    public static function getInitiativeWithAcceptanceDeploymentTickets()
    {
        $acceptanceDeploymentInitiative = Initiative::select(
            'id',
            'name',
            'client_id'
        )
            ->with(['client' => function ($query) {
                $query->select('id', 'name');
            }])
            ->withCount(['tickets' => function ($query) {
                $query->readyForAcceptanceStatus();
            }])
            ->whereHas('tickets', function ($query) {
                $query->readyForAcceptanceStatus();
            })
            ->get();
        return $acceptanceDeploymentInitiative;
    }

    public static function getInitiativeWithProductionDeploymentTickets()
    {

        $productionDeploymentInitiative = Initiative::select(
            'initiatives.id',
            'initiatives.name',
            'initiatives.client_id',
            DB::raw('COUNT(tickets.id) as tickets_count')
        )
            ->with(['client' => function ($query) {
                $query->select('id', 'name');
            }])
            ->JOIN('releases', 'releases.initiative_id', '=', 'initiatives.id')
            ->JOIN('release_tickets', 'release_tickets.release_id', '=', 'releases.id')
            ->JOIN('tickets', function ($query) {
                $query->on('tickets.id', '=', 'release_tickets.ticket_id')
                    ->where('tickets.status', Ticket::getStatusReadyForPRD());
            })
            ->groupBy('initiatives.id')
            ->groupBy('initiatives.name')
            ->having('tickets_count', '>', 0)
            ->get();
        return $productionDeploymentInitiative;
    }
}
