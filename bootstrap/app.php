<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use App\Http\Middleware\RequireInventoryAuth;
use App\Http\Middleware\RequireShopAuth;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'inventory.auth' => RequireInventoryAuth::class,
            'shop.auth' => RequireShopAuth::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'payment/midtrans/notification',
            'payment/doku/notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
