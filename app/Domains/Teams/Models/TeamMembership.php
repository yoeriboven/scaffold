<?php

declare(strict_types=1);

namespace App\Domains\Teams\Models;

use App\Domains\Teams\Enum\TeamPermission;
use App\Domains\Teams\Enum\TeamRole;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TeamMembership extends Pivot
{
    protected function casts(): array
    {
        return [
            'role' => TeamRole::class,
            'permissions' => AsEnumCollection::class.':'.TeamPermission::class,
        ];
    }
}
