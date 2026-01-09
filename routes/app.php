<?php

declare(strict_types=1);

use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\Onboarding\CreateTeamController;
use App\Http\Controllers\App\Onboarding\StoreTeamController;
use App\Http\Controllers\App\Settings\PasswordController;
use App\Http\Controllers\App\Settings\ProfileController;
use App\Http\Controllers\App\Settings\TwoFactorAuthenticationController;
use App\Http\Controllers\Teams\AcceptInvitationController;
use App\Http\Controllers\Teams\InviteUserController;
use App\Http\Controllers\Teams\RemoveMemberController;
use App\Http\Controllers\Teams\RevokeInvitationController;
use App\Http\Controllers\Teams\ShowInvitationController;
use App\Http\Controllers\Teams\ShowTeamController;
use App\Http\Middleware\AddInvitationToSession;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('dashboard', DashboardController::class)->name('dashboard');

/** Onboarding */
Route::get('onboarding/team', CreateTeamController::class)->name('onboarding.team');
Route::post('onboarding/team', StoreTeamController::class)->name('onboarding.team.store');

/** Teams */
Route::get('team', ShowTeamController::class)->name('team');

Route::post('team/invite', InviteUserController::class)->name('team.invite');

Route::get('team/invite/{invitation}', ShowInvitationController::class)
    ->middleware(AddInvitationToSession::class)
    ->name('invitation.accept.show');

Route::post('team/invite/{invitation}', AcceptInvitationController::class)
    ->name('invitation.accept.store');

Route::delete('team/invitation/{invitation}', RevokeInvitationController::class)
    ->name('team.invitation.revoke');

Route::delete('team/member/{user}', RemoveMemberController::class)
    ->name('team.member.remove');

/** Settings */
Route::redirect('settings', '/settings/profile');

Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('settings/password', [PasswordController::class, 'edit'])->name('user-password.edit');
Route::put('settings/password', [PasswordController::class, 'update'])
    ->middleware('throttle:6,1')
    ->name('user-password.update');

Route::get('settings/appearance', function () {
    return Inertia::render('settings/Appearance');
})->name('appearance.edit');

Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])->name('two-factor.show');
