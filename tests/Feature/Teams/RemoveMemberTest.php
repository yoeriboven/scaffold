<?php

declare(strict_types=1);

use App\Domains\Teams\Enum\TeamRole;
use App\Models\User;

test('admin can remove team member', function () {
    $admin = User::factory()->asTeamAdmin()->create();
    $team = $admin->currentTeam();

    $member = User::factory()->create();
    $member->joinTeam($team, TeamRole::MEMBER);

    expect($team->users)->toHaveCount(2);

    $response = $this
        ->actingAs($admin)
        ->delete(route('team.member.remove', $member));

    $response->assertRedirect(route('team'));
    expect($team->fresh()->users)->toHaveCount(1);
});

test('non-admin cannot remove team member', function () {
    $admin = User::factory()->asTeamAdmin()->create();
    $team = $admin->currentTeam();

    $member1 = User::factory()->create();
    $member1->joinTeam($team, TeamRole::MEMBER);

    $member2 = User::factory()->create();
    $member2->joinTeam($team, TeamRole::MEMBER);

    $response = $this
        ->actingAs($member1)
        ->delete(route('team.member.remove', $member2));

    $response->assertForbidden();
    expect($team->fresh()->users)->toHaveCount(3);
});

test('admin cannot remove themselves', function () {
    $admin = User::factory()->asTeamAdmin()->create();

    $response = $this
        ->actingAs($admin)
        ->delete(route('team.member.remove', $admin));

    $response->assertStatus(422);
});

test('admin cannot remove member from different team', function () {
    $admin1 = User::factory()->asTeamAdmin()->create();
    $admin2 = User::factory()->asTeamAdmin()->create();

    $response = $this
        ->actingAs($admin1)
        ->delete(route('team.member.remove', $admin2));

    $response->assertNotFound();
});
