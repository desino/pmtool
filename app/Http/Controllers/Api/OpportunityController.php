<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Services\InitiativeService;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function index(Request $request){
        $perPage = $request->input('per_page', 2);
        // $oppertunities = InitiativeService::getOpportunityInitiative($request, $perPage, Initiative::getStatusOpportunity());
        $oppertunities = InitiativeService::getOpportunityInitiative($request, $perPage);
        return ApiHelper::response(true, '', $oppertunities, 200);        
    }
}
