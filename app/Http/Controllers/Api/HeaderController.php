<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Initiative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeaderController extends Controller
{
    public function getInitiatives(Request $request){
        if (Auth::check()) {
            // $initiative = Initiative::getInitiatives();
            return ApiHelper::response(true, 'Initiatives', '', 200);
        }
    }
}
