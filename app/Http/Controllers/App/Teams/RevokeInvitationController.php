<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Teams;

use App\Domains\Teams\Enum\Permissions;
use App\Domains\Teams\Models\Invitation;
use App\Http\Controllers\Controller;

class RevokeInvitationController extends Controller
{
    public function __invoke(Invitation $invitation)
    {
        abort_unless(
            auth()->user()->hasPermissionTo(Permissions::MANAGE_TEAM),
            403
        );

        abort_unless(
            $invitation->team_id === currentTeam()->id,
            403
        );

        $invitation->delete();

        toast()->success(trans('Invitation revoked'));

        return to_route('team');
    }
}
