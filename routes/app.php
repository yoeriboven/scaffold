<?php

declare(strict_types=1);

use App\Http\Controllers\Onboarding\SelectTimezoneController;
use App\Http\Controllers\Onboarding\StoreTimezoneController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use Illuminate\Support\Facades\Route;

Route::inertia('', 'Dashboard')->name('dashboard');

/* Onboarding */
Route::get('onboarding/timezone', SelectTimezoneController::class);
Route::post('onboarding/timezone', StoreTimezoneController::class);

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
