<?php

namespace App\Services;

use App\Models\Initiative;

Class InitiativeService
{
    public static function getOpportunityInitiative($request, $perPage, $status = null) {
        $initiative = Initiative::when($status != null, function ($q) use ($status) {
            $q->status($status);
        })
        ->paginate($perPage);
        return $initiative;
    }
}