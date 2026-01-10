<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Teams;

use App\Domains\Teams\Enum\Permissions;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RemoveMemberController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        // do user onboarding with team name + accept invitation
        abort_unless(
            auth()->user()->hasPermissionTo(Permissions::MANAGE_TEAM),
            403
        );

        abort_if(
            $user->id === auth()->id(),
            422,
            trans('You cannot remove yourself from the team')
        );

        abort_unless(
            currentTeam()->users()->where('users.id', $user->id)->exists(),
            404
        );

        currentTeam()->users()->detach($user);

        toast()->success(sprintf('%s %s', $user->name, trans('removed from team')));

        return to_action(ShowTeamController::class);
    }
}
