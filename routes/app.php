<?php

declare(strict_types=1);

use App\Http\Controllers\Onboarding\SelectTimezoneController;
use App\Http\Controllers\Onboarding\StoreTimezoneController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use App\Http\Controllers\Teams\AcceptInvitationController;
use App\Http\Controllers\Teams\InviteUserController;
use App\Http\Controllers\Teams\ShowTeamController;
use App\Http\Middleware\AddInvitationToSession;
use Illuminate\Support\Facades\Route;

Route::inertia('', 'Dashboard')->name('dashboard');

/* Onboarding */
// Onboarding steps are "gate" routes: each one may redirect the user until it
// is satisfied. RedirectToPendingInvitation skips every `onboarding.*` route so
// new steps are exempt automatically without touching the middleware.
Route::prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('timezone', SelectTimezoneController::class)->name('timezone.show');
    Route::post('timezone', StoreTimezoneController::class)->name('timezone.store');
});

/* Team */
Route::get('team', ShowTeamController::class);

Route::post('team/invite', InviteUserController::class);

// Link user clicks to join the team
Route::middleware([AddInvitationToSession::class])
    ->get('team/invite/{invitation}', [AcceptInvitationController::class, 'show'])
    ->name('invitation.accept.show');

// URL of the form to accept the invitation
Route::post('team/invite/{invitation}', [AcceptInvitationController::class, 'store'])
    ->name('invitation.accept.store');

/* Settings */
Route::redirect('settings', '/settings/profile');

Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');

Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('settings/security', [SecurityController::class, 'edit'])
    ->name('security.edit');

Route::put('settings/password', [SecurityController::class, 'update'])
    ->middleware('throttle:6,1')
    ->name('user-password.update');
