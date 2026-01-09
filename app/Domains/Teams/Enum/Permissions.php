<?php

declare(strict_types=1);

namespace App\Domains\Teams\Enum;

enum Permissions: string
{
    case MANAGE_TEAM = 'manage_team';
    case MANAGE_BILLING = 'manage_billing';

    public static function forTeamRole(TeamRole $role): array
    {
        return match ($role) {
            TeamRole::ADMIN => [
                self::MANAGE_TEAM,
                self::MANAGE_BILLING,
            ],
            TeamRole::MEMBER => [
            ],
        };
    }
}
