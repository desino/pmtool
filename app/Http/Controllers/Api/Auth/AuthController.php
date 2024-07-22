<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Logs in a user.
     *
     * @param Request $request The request object.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            return ApiHelper::response(true, 'Logged In Successfully', ['token' => $token], 200);
        }
        return ApiHelper::response(false, 'Invalid Credentials', [], 401);
    }

    /**
     * Logs in a user.
     *
     * @param Request $request The request object.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public function user(Request $request)
    {
        if (Auth::check()) {
            return ApiHelper::response(true, 'Logged In User', $request->user(), 200);
        }
    }
}
