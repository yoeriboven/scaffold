<?php

declare(strict_types=1);

use App\Domains\Teams\Models\Team;
use App\Support\Flash\FlashNotifier;

function currentTeam(): ?Team
{
    return once(fn (): ?Team => auth()->user()->currentTeam());
}

function toast(): FlashNotifier
{
    return app(FlashNotifier::class);
}