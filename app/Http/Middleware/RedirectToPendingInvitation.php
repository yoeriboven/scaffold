<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domains\Teams\Models\Invitation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RedirectToPendingInvitation
{
    public function handle(Request $request, Closure $next)
    {
        // Skip the invitation screen itself and any onboarding "gate" route.
        // Those routes redirect on their own (e.g. EnsureUserHasTimezone), so
        // redirecting into them here would create an infinite redirect loop and
        // never reach the controller that clears the invitation from the session.
        if ($request->routeIs('invitation.accept.*', 'onboarding.*')) {
            return $next($request);
        }

        if ($invitation = $this->activeInvitation()) {
            return to_route('invitation.accept.show', $invitation);
        }

        return $next($request);
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
