<?php

declare(strict_types=1);

use App\Domains\Teams\Mail\InviteUserMail;
use App\Domains\Teams\Models\Invitation;
use App\Http\Controllers\Teams\InviteUserController;
use App\Http\Controllers\Teams\ShowTeamController;
use App\Models\User;
use App\Support\Flash\FlashLevel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Once;

beforeEach(fn () => Mail::fake());

test('a team admin can invite a user by email', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(action(InviteUserController::class), ['email' => 'invitee@example.com'])
        ->assertRedirectToAction(ShowTeamController::class)
        ->assertToast('Invitation sent to invitee@example.com');

    $this->assertDatabaseHas('invitations', [
        'team_id' => $user->currentTeam()->id,
        'email' => 'invitee@example.com',
    ]);

    Mail::assertQueued(InviteUserMail::class, function (InviteUserMail $mail) {
        return $mail->hasTo('invitee@example.com')
            && $mail->invitation->email === 'invitee@example.com';
    });
});

test('an invitation requires a valid email address', function (string $email) {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(action(InviteUserController::class), ['email' => $email])
        ->assertInvalid('email');
})->with([
    'empty' => '',
    'not an email' => 'not-an-email',
]);

test('an email that already belongs to a team member cannot be invited', function () {
    $user = User::factory()->create();

    User::factory()
        ->memberOf($user->currentTeam())
        ->create(['email' => 'member@example.com']);

    $this
        ->actingAs($user)
        ->post(action(InviteUserController::class), ['email' => 'member@example.com'])
        ->assertInvalid('email');

    $this->assertDatabaseCount('invitations', 0);

    Mail::assertNothingQueued();
});

test('inviting an already invited email resends the existing invitation instead of creating a duplicate', function () {
    $user = User::factory()->create();

    $invitation = Invitation::factory()
        ->for($user->currentTeam())
        ->create(['email' => 'invitee@example.com']);

    $response = $this
        ->actingAs($user)
        ->post(action(InviteUserController::class), ['email' => 'invitee@example.com']);

    $response
        ->assertRedirectToAction(ShowTeamController::class)
        ->assertToast('Resent invitation to invitee@example.com');

    $this->assertDatabaseCount('invitations', 1);

    Mail::assertQueued(InviteUserMail::class, function (InviteUserMail $mail) use ($invitation) {
        return $mail->invitation->is($invitation);
    });
});

describe('rate limiting', function () {
    test('inviting the same email twice within a day is rate limited', function () {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post(action(InviteUserController::class), ['email' => 'invitee@example.com'])
            ->assertRedirectToAction(ShowTeamController::class);

        $this
            ->asInertia()
            ->from(action(ShowTeamController::class))
            ->post(action(InviteUserController::class), ['email' => 'invitee@example.com'])
            ->assertRedirectToAction(ShowTeamController::class)
            ->assertToast('You can only send an invite to the same team member once per day.', FlashLevel::ERROR);

        $this->assertDatabaseCount('invitations', 1);

        Mail::assertQueuedCount(1);
    });

    test('a user can invite multiple people per day', function () {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post(action(InviteUserController::class), ['email' => 'invitee@example.com'])
            ->assertRedirectToAction(ShowTeamController::class);

        $this
            ->actingAs($user)
            ->post(action(InviteUserController::class), ['email' => 'other@example.com'])
            ->assertRedirectToAction(ShowTeamController::class);

        $this->assertDatabaseHas('invitations', [
            ['email' => 'invitee@example.com'],
            ['email' => 'other@example.com'],
        ]);
    });

    test('the invitation rate limit is scoped per team', function () {
        $user = User::factory()->create();
        $anotherUserOnADifferentTeam = User::factory()->create();

        $this
            ->actingAs($user)
            ->post(action(InviteUserController::class), ['email' => 'invitee@example.com'])
            ->assertRedirectToAction(ShowTeamController::class);

        Once::flush();

        $this
            ->actingAs($anotherUserOnADifferentTeam)
            ->post(action(InviteUserController::class), ['email' => 'invitee@example.com'])
            ->assertRedirectToAction(ShowTeamController::class);

        $this->assertDatabaseHas('invitations', [
            ['team_id' => $user->currentTeam()->id, 'email' => 'invitee@example.com'],
            ['team_id' => $anotherUserOnADifferentTeam->currentTeam()->id, 'email' => 'invitee@example.com'],
        ]);
    });
});

describe('authorization', function () {
    test('a team member without the manage team permission cannot invite users', function () {
        $owner = User::factory()->create();
        $member = User::factory()->memberOf($owner->currentTeam())->create();

        $response = $this
            ->actingAs($member)
            ->post(action(InviteUserController::class), ['email' => 'invitee@example.com']);

        $response->assertForbidden();
    });

    test('guests cannot send invitations', function () {
        $response = $this->post(action(InviteUserController::class), ['email' => 'invitee@example.com']);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('invitations', 0);
    });
});
