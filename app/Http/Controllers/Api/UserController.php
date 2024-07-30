<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function user(Request $request)
    {
        if (Auth::check()) {
            return ApiHelper::response(true, 'Logged In User', $request->user(), 200);
        }
    }
}
