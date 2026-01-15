<?php

declare(strict_types=1);

use App\Http\Middleware\AddInvitationToSession;
use App\Http\Middleware\EnsureUserHasTeam;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLanguage;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('web')
                ->group(base_path('routes/front.php'));

            Route::middleware(['web', 'auth', 'app'])
                ->prefix('app')
                ->group(base_path('routes/app.php'));

            Route::middleware('web')
                ->group(base_path('routes/misc.php'));

            //  Route::group([], base_path('routes/webhooks.php'));

            //  Route::middleware('api')
            //    ->group(base_path('routes/api.php'));
        }
    )
    ->withCommands([
        base_path('routes/console/console.php'),
        base_path('routes/console/console-local.php'),
        base_path('routes/console/schedule.php'),
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            SetLanguage::class,
            HandleAppearance::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->group('guest', [
            HandleInertiaRequests::class,
        ]);

        $middleware->group('app', [
            HandleInertiaRequests::class,
            EnsureUserHasTeam::class,
        ]);

        /**
         * First add the invitation to the session, then let the auth middleware
         * figure out whether to open the dashboard or go to the login page.
         */
        $middleware->priority([
            AddInvitationToSession::class,
            'auth',
            EnsureUserHasTeam::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontTruncateRequestExceptions();

        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            if (!$request->inertia()) {
                return $response;
            }

            if (in_array($response->getStatusCode(), [500, 503, 404, 403], true)) {
                return $response;
            }

            if ($exception instanceof ValidationException) {
                return $response;
            }

            $message = match ($response->getStatusCode()) {
                419 => 'The page expired, please try again.',
                default => $exception->getMessage(),
            };

            toast()->error($message);

            return back();
        });
    })->create();
