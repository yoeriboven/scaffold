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
Route::get('onboarding/timezone', SelectTimezoneController::class);
Route::post('onboarding/timezone', StoreTimezoneController::class);

/* Team */
Route::get('team', ShowTeamController::class)->name('team');

Route::post('team/invite', InviteUserController::class)->name('team.invite');

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
