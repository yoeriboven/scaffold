<?php

declare(strict_types=1);

namespace App\Http\Controllers\Onboarding;

use DateTimeZone;
use Inertia\Inertia;

class SelectTimezoneController
{
    public function __invoke()
    {
        abort_if(auth()->user()->timezone !== null, to_route('dashboard'));

        return Inertia::render('onboarding/SelectTimezone', [
            'timezones' => DateTimeZone::listIdentifiers(),
        ]);
    }
}
