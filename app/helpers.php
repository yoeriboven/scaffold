<?php

declare(strict_types=1);

use App\Domains\Teams\Models\Team;

function currentTeam(): ?Team
{
    return once(fn (): ?Team => auth()->user()->currentTeam());
}
