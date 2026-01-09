<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Adds the invitation to session so when the user later tries to use the app
 * EnsureUserHasTeam automatically picks it up and shows the accept invitation screen.
 */
class AddInvitationToSession
{
    public function handle(Request $request, \Closure $next)
    {
        if ($request->routeIs('invitation.accept.show')) {
            Session::put('invitation', $request->route('invitation')->public_id);
        }

        return $next($request);
    }
}
