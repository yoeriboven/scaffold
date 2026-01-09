<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Onboarding;

use App\Domains\Teams\Enum\TeamRole;
use App\Domains\Teams\Models\Team;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreTeamController extends Controller
{
    public function __invoke(Request $request)
    {
        abort_if(auth()->user()->teams->isNotEmpty(), to_route('dashboard'));

        $request->validate([
            'name' => 'required|max:50',
        ]);

        $team = Team::create([
            'name' => $request->name,
        ]);

        auth()->user()->joinTeam($team, TeamRole::ADMIN);

        return to_route('dashboard');
    }
}
