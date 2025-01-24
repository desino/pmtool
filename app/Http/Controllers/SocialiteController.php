<?php

namespace App\Http\Controllers;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToProvider($provider)
    {
        $provider = self::convertProviderSlugToServiceName($provider);
        return Socialite::driver($provider)->scopes(config("services." . $provider . ".scopes"))->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $provider = self::convertProviderSlugToServiceName($provider);
        $remoteUser = Socialite::driver($provider)
            ->scopes(config("services.{$provider}.scopes"))
            ->user();

        $domainArray = explode('@', $remoteUser->getEmail());
        $allowedDomainsArray = array_map('trim', explode(',', Config::get('myapp.allowed_domains_for_office365_login')));
        if (!in_array($domainArray[1], $allowedDomainsArray)) {
            //return redirect('/office-365-login-callback?message_class=danger&message='.urlencode(__('messages.auth.office365_login_domains_not_match')).'');
            Session::put('message_class', 'danger');
            Session::put('callback_message', __('messages.auth.office365_login_domains_not_match'));
            return redirect('/login');
        }

        $user = User::where('email', $remoteUser->getEmail())->first();
        if (!$user) {
            Session::put('message_class', 'danger');
            Session::put('callback_message', __('messages.auth.user_not_found_in_office365'));
            return redirect('/login');
        } else if ($user && $user->status == 0) {
            Session::put('message_class', 'danger');
            Session::put('callback_message', __('messages.auth.user_is_not_active_in_office365'));
            return redirect('/login');
        }

        $user = User::firstOrCreate([
            'email' => $remoteUser->getEmail(),
        ], [
            'name' => $remoteUser->getName(),
            'password' => 'admin@123'
        ]);

        $token = $user->createToken('token')->plainTextToken;
        Session::put('message_class', 'success');
        Session::put('callback_message', __('messages.auth.office365_login_success'));
        Session::put('token', $token);
        return redirect('/login');
    }

    public function getProviderCallbackSessionData()
    {
        $retData = array(
            'message_class' => Session::get('message_class') ?? null,
            'message' => Session::get('callback_message') ?? null,
            'token' => Session::get('token') ?? null,
            'user' => Auth::user() ?? null
        );
        Session::flash('message_class');
        Session::flash('callback_message');
        Session::flash('token');
        return ApiHelper::response(true, __('messages.auth.provider_callback_session_text'), $retData, 200);
    }

    public static function convertProviderSlugToServiceName($providerSlug)
    {
        return str_replace('-', '_', $providerSlug);
    }
}
