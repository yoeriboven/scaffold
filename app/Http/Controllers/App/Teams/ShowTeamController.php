<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Teams;

use App\Domains\Teams\Enum\Permissions;
use App\Domains\Teams\Enum\TeamRole;
use App\Domains\Teams\Models\Invitation;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class ShowTeamController
{
    public function __invoke(): Response
    {
        abort_unless(
            auth()->user()->hasPermissionTo(Permissions::MANAGE_TEAM),
            404
        );

        return Inertia::render('Team/Team', [
            'team' => [
                'members' => currentTeam()
                    ->users()
                    ->withPivot(['role', 'permissions'])
                    ->get()
                    ->map(function (User $member) {
                        return [
                            'id' => $member->public_id,
                            'email' => $member->email,
                            'name' => $member->name,
                            'role' => TeamRole::from($member->pivot->role)->label(),
                            'role_value' => $member->pivot->role,
                        ];
                    }),
                'invitations' => currentTeam()
                    ->invitations
                    ->map(function (Invitation $invitation) {
                        return [
                            'id' => $invitation->public_id,
                            'email' => $invitation->email,
                            'role' => TeamRole::MEMBER->label(),
                        ];
                    }),
            ],
        ]);
    }
}
