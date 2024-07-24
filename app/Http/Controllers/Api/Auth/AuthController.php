<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Logs in a user.
     *
     * @param Request $request The request object.
     * @return \Illuminate\Http\JsonResponse The JSON response.
     */
    public function login(LoginRequest $request)
    {                
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            return ApiHelper::response(true, __('messages.auth.login_success'), ['token' => $token], 200);
        }
        return ApiHelper::response(false, __('messages.auth.login_credentials_does_not_match'), [], 401);
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
            return ApiHelper::response(true, __('messages.auth.looged_in_user_data_fatching_success'), $request->user(), 200);
        }
    }

    public function sendResetLink(ForgotPasswordRequest $request){
        $response = Password::sendResetLink($request->only('email'));

        if ($response === Password::RESET_LINK_SENT) {
            return ApiHelper::response(true, Lang::get($response), '', 200);
        } else {
            return ApiHelper::response(false, Lang::get($response), '', 400);
        }    
    }

    public function logout(Request $request)
    {        
        $user = Auth::user();

        // Revoke the token
        $user->tokens()->delete();

        return ApiHelper::response(true, __('messages.auth.logout_success'), '', 200);        
    }
}
