<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Domains\Teams\Enum\TeamPermission;
use App\Domains\Teams\Mail\InviteUserMail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class InviteUserController extends Controller
{
    public function handle(Request $request)
    {
        abort_unless(
            auth()->user()->hasTeamPermissionTo(TeamPermission::MANAGE_TEAM),
            403
        );

        abort_if(
            RateLimiter::tooManyAttempts($this->rateLimiterKey(currentTeam()->id, $request->email), 1),
            429,
            trans('You can only send an invite to the same team member once per day.')
        );

        $request->validate([
            'email' => 'required|email:strict',
        ]);

        $invitation = currentTeam()->invite($request->email);

        Mail::to($request->email)
            ->queue(new InviteUserMail($invitation));

        RateLimiter::increment(
            $this->rateLimiterKey(currentTeam()->id, $request->email),
            strtotime('1 day', 0)
        );

//        toast()->success(sprintf('%s %s', trans('Invitation sent to'), $request->email));

        return to_route('team');
    }

    protected function rateLimiterKey(int $teamId, ?string $email): string
    {
        return sprintf('send-invitation.%s.%s', $teamId, $email ?? '');
    }
}
