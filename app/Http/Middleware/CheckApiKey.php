<?php

namespace App\Http\Middleware;

use App\Helper\ApiHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = Config::get('myapp.api_key');
        $requestData = $request->all();
        if (!isset($requestData['api_key']) || trim($requestData['api_key']) !== $apiKey) {
            return response([
                'message' => __('messages.invalid_api_key'),
                'data' => []
            ], 401);
        }
        return $next($request);
    }
}
