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
