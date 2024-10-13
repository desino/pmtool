<?php

use App\Helper\ApiHelper;
use App\Http\Middleware\CheckApiKey;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'checkApiKey' => CheckApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (ValidationException $exception) {
            Log::error($exception->getMessage(), ['path' => dirname(__FILE__)]);
            return ApiHelper::response(true, 'Invalid request data', $exception->errors(), 422);
        });
        $exceptions->renderable(function (AuthenticationException $exception) {
            Log::error($exception->getMessage(), ['path' => dirname(__FILE__)]);
            return ApiHelper::response(false, 'Please login first to continue', '', 403);
        });
    })->create();
