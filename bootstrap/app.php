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
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Monicahq\Cloudflare\Http\Middleware\TrustProxies as CloudflareTrustProxies;
use Symfony\Component\HttpFoundation\Response;

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
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontTruncateRequestExceptions();

        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            if (! $request->inertia()) {
                return $response;
            }

            if ($exception instanceof ValidationException) {
                return $response;
            }

            if ($exception instanceof ThrottleRequestsException) {
                throw ValidationException::withMessages([
                    'throttle' => 'Too many requests. Please try again later.',
                ]);
            }

            if (app()->isProduction() && in_array($response->getStatusCode(), [500, 503, 404, 403], true)) {
                return Inertia::location(route('error', $response->getStatusCode()));
            }

            $message = $response->getStatusCode() >= 500 && app()->isProduction()
                ? 'Something went wrong with the request. We have been notified.'
                : $exception->getMessage();

            //          dev - prod
            // < 500    message - message
            // 500 - message - smt..

            //            $message = match (true) {
            //                $response->getStatusCode() === 419 => 'The page expired, please try again.',
            //                $response->getStatusCode() >= 400 && $response->getStatusCode() < 500 => $exception->getMessage(),
            //                default => 'Something went wrong with the request. We have been notified.',
            //            };

            toast()->error($message);

            return back();
        });
    })->create();
