<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Domains\Teams\Enum\TeamRole;
use App\Domains\Teams\Models\Invitation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class AcceptInvitationController extends Controller
{
    public function show(Invitation $invitation)
    {
        Session::remove('invitation');

        return Inertia::render('onboarding/Invitation', [
            'invitationId' => $invitation->public_id,
            'teamName' => $invitation->team->name,
        ]);
    }

    public function store(Invitation $invitation)
    {
        DB::transaction(function () use ($invitation) {
            auth()->user()->joinTeam($invitation->team, TeamRole::MEMBER);

            $invitation->delete();
        });

        return to_route('dashboard');
    }
}
