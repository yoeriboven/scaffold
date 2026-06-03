<?php

use App\Http\Middleware\ConfigureNightwatchSampling;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Middleware\TrustProxies;
use Monicahq\Cloudflare\Http\Middleware\TrustProxies as CloudflareTrustProxies;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        health: '/up',
    )
    ->withCommands([
        base_path('routes/console/console.php'),
        base_path('routes/console/console-local.php'),
        base_path('routes/console/schedule.php'),
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(prepend: [
            ConfigureNightwatchSampling::class,
        ]);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->replace(
            TrustProxies::class,
            CloudflareTrustProxies::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {})->create();
