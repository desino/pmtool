<?php

namespace App\Services;

use App\Models\Initiative;

Class InitiativeService
{
    public static function getOpportunityInitiative($request, $perPage, $status = null) {
        $initiative = Initiative::with('client')
        ->when($status != null, function ($q) use ($status) {
            $q->status($status);
        })
        ->when($request->post('initiative_name') != null, function ($q) use ($request) {
            $q->name($request->post('initiative_name'));
        })
        ->paginate($perPage);        
        return $initiative;
    }
}