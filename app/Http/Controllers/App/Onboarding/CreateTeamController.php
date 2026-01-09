<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Onboarding;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class CreateTeamController extends Controller
{
    public function __invoke()
    {
        abort_if(auth()->user()->teams->isNotEmpty(), to_route('dashboard'));

        return Inertia::render('Onboarding/CreateTeam');
    }
}
