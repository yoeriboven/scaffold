<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domains\Teams\Models\Invitation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EnsureUserHasTeam
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->doesNotRequireTeam($request)) {
            return $next($request);
        }

        if ($invitation = $this->activeInvitation()) {
            return to_route('invitation.accept.show', $invitation);
        }

        if (currentTeam() === null) {
            return to_route('onboarding.team');
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

    public function doesNotRequireTeam(Request $request): bool
    {
        return $request->routeIs([
            'onboarding.team*',
            'invitation.accept.*',
        ]);
    }
}
