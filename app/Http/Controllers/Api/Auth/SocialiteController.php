<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helper\ApiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToProvider($provider){
        $provider = self::convertProviderSlugToServiceName($provider);
        return Socialite::driver($provider)->scopes(config("services.".$provider.".scopes"))->redirect();
    }

    public function handleProviderCallback(Request $request, $provider)
    {
        print '<pre>';
        print_r("sdfsdfsdf");
        print '</pre>';
        exit;
        // $response = Http::asForm()->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
        //     'client_id' => env('GRAPH_CLIENT_ID'),
        //     'client_secret' => env('GRAPH_CLIENT_SECRET'),
        //     'redirect_uri' => env('GRAPH_REDIRECT_URI'),
        //     'code' => $request->code,
        //     'grant_type' => 'authorization_code',
        // ]);
        // print '<pre>';
        // print_r($response->json());
        // print '</pre>';
        // exit;

        // $accessToken = $response->json()['access_token'];
        // $user = Http::withToken($accessToken)->get('https://graph.microsoft.com/v1.0/me')->json();
        // print '<pre>';
        // print_r($user);
        // print '</pre>';
        // exit;

        $provider = self::convertProviderSlugToServiceName($provider);
        $remoteUser = Socialite::driver($provider)
        ->scopes(config("services.{$provider}.scopes"))
        ->user(); 
        
        abort_unless($remoteUser, 404, __('auth.failed_login'));        

        $domainArray = explode('@', $remoteUser->getEmail());

        $allowedDomainsArray = array_map('trim',explode(',', config('app.allowed_domains_for_office365_login')));
        if (!in_array($domainArray[1], $allowedDomainsArray)) {  
            echo "domains not match";exit;          
            return ApiHelper::response(false, __('messages.office365_login_domains_not_match'), '', 400);
            // return redirect()->to('/login');
        }
        echo "Sdfsdf";exit;
        $user = User::firstOrCreate([
            'email' => $remoteUser->getEmail(),
        ], [
            'name' => $remoteUser->getName(),
            'password' => 'admin@123'
        ]);

        Auth::login($user, true);

        return redirect()->intended('/');
    }

    public static function convertProviderSlugToServiceName($providerSlug)
    {
        return str_replace('-', '_', $providerSlug);
    }
}
