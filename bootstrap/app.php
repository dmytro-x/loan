<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':300,1',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\App\Exceptions\DatabaseException $e, $request) {
            return response()->json([
                'error' => 'Database error'
            ], 500);
        });
    })->withProviders([
        \App\Modules\Client\Providers\ClientServiceProvider::class,
        \App\Modules\Credit\Providers\CreditServiceProvider::class,
        \App\Modules\Notification\Providers\NotificationServiceProvider::class,
    ])->create();
