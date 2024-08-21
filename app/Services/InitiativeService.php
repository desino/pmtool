<?php

namespace App\Services;

use App\Models\Initiative;

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
        return Initiative::with('client', 'initiativeEnvironments')->find($id);
    }

    public static function getInitiativesForHeaderSelectBox($request)
    {
        return Initiative::with('client')->orderBy('id', 'desc')->get();
    }
}
