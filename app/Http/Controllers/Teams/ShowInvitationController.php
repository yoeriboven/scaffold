<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Domains\Teams\Models\Invitation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class ShowInvitationController extends Controller
{
    public function __invoke(Invitation $invitation)
    {
        Session::remove('invitation');

        if (auth()->user()->teams()->where('id', $invitation->team_id)->exists()) {
            // Already member of team
            return redirect('dashboard');
        }

        return Inertia::render('Invitation', [
            'invitationId' => $invitation->public_id,
            'teamName' => $invitation->team->name,
        ]);
    }
}
