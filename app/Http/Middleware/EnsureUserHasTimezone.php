<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Controllers\Onboarding\SelectTimezoneController;
use App\Http\Controllers\Onboarding\StoreTimezoneController;
use Closure;
use Illuminate\Http\Request;

class EnsureUserHasTimezone
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
        if ($this->routeDoesNotRequireSetTimezone($request)) {
            return $next($request);
        }

        if (auth()->user()->timezone === null) {
            return to_action(SelectTimezoneController::class);
        }

        return $next($request);
    }

    public function routeDoesNotRequireSetTimezone(Request $request): bool
    {
        return $request->usesController([
            // 'invitation.accept.store',
            SelectTimezoneController::class,
            StoreTimezoneController::class,
        ]);
    }
}
