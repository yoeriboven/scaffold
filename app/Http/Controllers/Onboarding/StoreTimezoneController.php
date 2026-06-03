<?php

declare(strict_types=1);

namespace App\Http\Controllers\Onboarding;

use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreTimezoneController
{
    public function __invoke(Request $request)
    {
        abort_if(auth()->user()->timezone !== null, to_route('dashboard'));

        $request->validate([
            'timezone' => ['required', Rule::in(DateTimeZone::listIdentifiers())],
        ]);

        auth()->user()->update([
            'timezone' => $request->timezone,
        ]);

        return to_route('dashboard');
    }
}
