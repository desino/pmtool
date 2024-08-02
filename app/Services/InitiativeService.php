<?php

namespace App\Services;

use App\Models\Initiative;

Class InitiativeService
{
    public static function getOpportunityInitiative($request, $perPage, $status = null) {
        $filters = $request->post('filters');
        
        $initiative = Initiative::with('client')
        ->when($status != null, function ($q) use ($status) {
            $q->status($status);
        })
        ->when($filters['initiative_name'] != '', function ($q) use ($filters) {
            $q->name($filters['initiative_name']);
        })
        ->when($filters['client_id'] != '', function ($q) use ($filters) {
            $q->client($filters['client_id']);
        })
        ->paginate($perPage);        
        return $initiative;
    }

    public static function getInitiative($request) {        
        $id = $request->post('initiative_id');        
        return Initiative::with('client')->find($id);
    }
}