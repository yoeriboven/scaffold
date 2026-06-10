<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * If a user is not logged in we want to save the invitation to the session
 * and let them do the logging in / registration. Once logged in we show the
 * accept invitation screen.
 */
class AddInvitationToSession
{
    public function handle(Request $request, Closure $next)
    {
        info('AddInvitationToSession');
        // TODO: Change to controller check sometime. We can't do that currently because our usesController method only checks for invokable controllers.
        if (! $request->routeIs('invitation.accept.show')) {
            info('not invitation.accept.show so continue');
            return $next($request);
        }
info('lets put it in the session: '.$request->route('invitation'));
        Session::put('invitation', $request->route('invitation'));

        return $next($request);
    }
}
