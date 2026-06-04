<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Domains\Teams\Enum\TeamPermission;
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
            auth()->user()->hasTeamPermissionTo(TeamPermission::MANAGE_TEAM),
            403
        );

        return Inertia::render('teams/Index', [
            'team' => [
                'members' => currentTeam()
                    ->users()
                    ->withPivot(['role', 'permissions'])
                    ->get()
                    ->map(function (User $member) {
                        return [
                            'name' => $member->name,
                            'email' => $member->email,
                            'role' => TeamRole::from($member->pivot->role)->label(),
                        ];
                    }),
                'invitations' => currentTeam()
                    ->invitations
                    ->map(function (Invitation $invitation) {
                        return [
                            'email' => $invitation->email,
                            'role' => TeamRole::MEMBER->label(),
                        ];
                    }),
            ],
        ]);
    }
}
