<?php

declare(strict_types=1);

use App\Domains\Teams\Enum\TeamRole;
use App\Models\User;

test('admin can view team page', function () {
    $admin = User::factory()->asTeamAdmin()->create();

    $response = $this
        ->actingAs($admin)
        ->get(route('team'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Team'));
});

test('non-admin cannot view team page', function () {
    $member = User::factory()->create();
    $admin = User::factory()->asTeamAdmin()->create();

    // Attach member to same team as MEMBER role
    $member->joinTeam($admin->currentTeam(), TeamRole::MEMBER);

    $response = $this
        ->actingAs($member)
        ->get(route('team'));

    $response->assertNotFound();
});

test('team page shows members and invitations', function () {
    $admin = User::factory()->asTeamAdmin()->create();
    $team = $admin->currentTeam();

    // Add another member
    $member = User::factory()->create();
    $member->joinTeam($team, TeamRole::MEMBER);

    // Create invitation
    $invitation = $team->invitations()->create([
        'email' => 'invited@example.com',
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('team'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('team.members', 2)
        ->has('team.invitations', 1)
    );
});
