<?php

declare(strict_types=1);

use App\Domains\Teams\Enum\TeamRole;
use App\Domains\Teams\Models\Invitation;
use App\Models\User;

test('admin can revoke invitation', function () {
    $admin = User::factory()->asTeamAdmin()->create();
    $team = $admin->currentTeam();

    $invitation = $team->invitations()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this
        ->actingAs($admin)
        ->delete(route('team.invitation.revoke', $invitation));

    $response->assertRedirect(route('team'));
    expect(Invitation::find($invitation->id))->toBeNull();
});

test('non-admin cannot revoke invitation', function () {
    $admin = User::factory()->asTeamAdmin()->create();
    $team = $admin->currentTeam();

    $member = User::factory()->create();
    $member->joinTeam($team, TeamRole::MEMBER);

    $invitation = $team->invitations()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this
        ->actingAs($member)
        ->delete(route('team.invitation.revoke', $invitation));

    $response->assertForbidden();
    expect(Invitation::find($invitation->id))->not->toBeNull();
});

test('admin cannot revoke invitation from different team', function () {
    $admin1 = User::factory()->asTeamAdmin()->create();
    $admin2 = User::factory()->asTeamAdmin()->create();

    $invitation = $admin2->currentTeam()->invitations()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this
        ->actingAs($admin1)
        ->delete(route('team.invitation.revoke', $invitation));

    $response->assertForbidden();
    expect(Invitation::find($invitation->id))->not->toBeNull();
});
