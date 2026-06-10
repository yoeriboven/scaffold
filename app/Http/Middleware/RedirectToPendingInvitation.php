<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domains\Teams\Models\Invitation;
use App\Http\Controllers\Onboarding\SelectTimezoneController;
use App\Http\Controllers\Onboarding\StoreTimezoneController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RedirectToPendingInvitation
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldSkipRedirect($request)) {
            return $next($request);
        }

        if ($invitation = $this->activeInvitation()) {
            return to_route('invitation.accept.show', $invitation);
        }

        return $next($request);
    }

    /**
     * Skip the redirect while the user is already on the invitation screen or
     * still completing the timezone onboarding. Otherwise this middleware and
     * EnsureUserHasTimezone bounce the user back and forth between the two
     * routes and never reach the controller that clears the session.
     */
    private function shouldSkipRedirect(Request $request): bool
    {
        return $request->routeIs('invitation.accept.*')
            || $request->usesController([
                SelectTimezoneController::class,
                StoreTimezoneController::class,
            ]);
    }

    public function activeInvitation(): ?Invitation
    {
        if ($invitationId = Session::get('invitation')) {
            return Invitation::query()
                ->where('public_id', $invitationId)
                ->first();
        }

        return null;
    }
}
