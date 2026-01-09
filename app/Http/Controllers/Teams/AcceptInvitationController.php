<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Domains\Teams\Enum\TeamRole;
use App\Domains\Teams\Models\Invitation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AcceptInvitationController extends Controller
{
    public function __invoke(Invitation $invitation)
    {
        if (auth()->user()->teams()->where('id', $invitation->team_id)->exists()) {
            // Already member of team
            return redirect('dashboard');
        }

        DB::transaction(function () use ($invitation) {
            auth()->user()->joinTeam($invitation->team, TeamRole::MEMBER);

            $invitation->delete();
        });

        return to_route('dashboard');
    }
}
