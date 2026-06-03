<?php

declare(strict_types=1);

namespace App\Domains\Teams\Enum;

enum TeamPermission: string
{
    case MANAGE_TEAM = 'manage_team';

    public static function forTeamRole(TeamRole $role): array
    {
        return match ($role) {
            TeamRole::ADMIN => [
                self::MANAGE_TEAM,
            ],
            TeamRole::MEMBER => [
            ],
        };
    }
}
