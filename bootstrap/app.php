<?php

declare(strict_types=1);

use App\Http\Middleware\AddInvitationToSession;
use App\Http\Middleware\ConfigureNightwatchSampling;
use App\Http\Middleware\EnsureUserHasTimezone;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RedirectToPendingInvitation;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Route;
use Monicahq\Cloudflare\Http\Middleware\TrustProxies as CloudflareTrustProxies;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware(['web', 'auth', 'verified', 'app'])
                ->prefix('app')
                ->group(base_path('routes/app.php'));
        }
    )
    ->withCommands([
        base_path('routes/console/console.php'),
        base_path('routes/console/console-local.php'),
        base_path('routes/console/schedule.php'),
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['sidebar_state']);

        $middleware->group('app', [
            RedirectToPendingInvitation::class,
            EnsureUserHasTimezone::class,
        ]);

        $middleware->web(prepend: [
            ConfigureNightwatchSampling::class,
        ]);

        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->replace(
            TrustProxies::class,
            CloudflareTrustProxies::class,
        );

        /**
         * AddInvitationToSession should always run before auth or on unauthenticated we would
         * be redirected away and never get to add the invitation to the session.
         */
        $middleware->priority([
            AddInvitationToSession::class,
            'auth',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {})->create();
