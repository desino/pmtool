<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use App\Services\InitiativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeaderController extends Controller
{
    public function getInitiatives(Request $request){
        if (Auth::check()) {
            $initiative = InitiativeService::getInitiativesForHeaderSelectBox($request);
            return ApiHelper::response(true, '', $initiative, 200);
        }
    }
}