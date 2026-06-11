<?php

declare(strict_types=1);

use App\Domains\Teams\Models\Invitation;
use App\Http\Controllers\Onboarding\SelectTimezoneController;
use App\Http\Controllers\Teams\AcceptInvitationController;
use App\Models\User;

test('an unregistered user can accept an invitation through the link in their mailbox', function () {
    $invitation = Invitation::factory()->create(['email' => 'invitee@example.com']);

    // The browser timezone determines the preselected option on the
    // timezone onboarding screen.
    $page = visit($invitation->url())->withTimezone('Europe/Amsterdam');

    $page
        ->assertUrlIs(route('login'))
        ->click('Sign up')
        ->assertUrlIs(route('register'))
        ->fill('name', 'Invitee')
        ->fill('email', 'invitee@example.com')
        ->fill('password', 'password')
        ->fill('password_confirmation', 'password')
        ->click('Create account')
        ->assertUrlIs(action(SelectTimezoneController::class))
        ->assertSee('Select your timezone')
        ->assertSee('Europe/Amsterdam')
        ->click('Continue')
        ->assertUrlIs(action([AcceptInvitationController::class, 'show'], $invitation))
        ->assertSee('You have been invited')
        ->assertSee($invitation->team->name)
        ->click('Accept')
        ->assertUrlIs(route('dashboard'));

    $user = User::firstWhere('email', 'invitee@example.com');

    expect($user)->not->toBeNull();

    $this->assertDatabaseHas('team_user', [
        'team_id' => $invitation->team_id,
        'user_id' => $user->id,
        'role' => 'member',
    ]);

    $this->assertModelMissing($invitation);
});

test('a registered user can accept an invitation through the link in their mailbox', function () {
    $invitation = Invitation::factory()->create(['email' => 'invitee@example.com']);
    $user = User::factory()->create(['email' => 'invitee@example.com']);

    $page = visit($invitation->url());

    // A guest is sent to login first; the invitation is remembered server-side.
    $page
        ->assertUrlIs(route('login'))
        ->fill('email', 'invitee@example.com')
        ->fill('password', 'password')
        ->click('Log in')
        ->assertUrlIs(action([AcceptInvitationController::class, 'show'], $invitation))
        ->assertSee('You have been invited')
        ->assertSee($invitation->team->name)
        ->click('Accept')
        ->assertUrlIs(route('dashboard'));

    $this->assertDatabaseHas('team_user', [
        'team_id' => $invitation->team_id,
        'user_id' => $user->id,
        'role' => 'member',
    ]);

    $this->assertModelMissing($invitation);
});

test('a logged in user can accept an invitation through the link in their mailbox', function () {
    $invitation = Invitation::factory()->create(['email' => 'invitee@example.com']);
    $user = User::factory()->create(['email' => 'invitee@example.com']);

    $this->actingAs($user);

    // Already authenticated, so the link lands directly on the accept screen.
    $page = visit($invitation->url());

    $page
        ->assertSee('You have been invited')
        ->assertSee($invitation->team->name)
        ->click('Accept')
        ->assertUrlIs(route('dashboard'));

    $this->assertDatabaseHas('team_user', [
        'team_id' => $invitation->team_id,
        'user_id' => $user->id,
        'role' => 'member',
    ]);

    $this->assertModelMissing($invitation);
});
