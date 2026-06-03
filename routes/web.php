<?php

declare(strict_types=1);

use App\Http\Controllers\Teams\AcceptInvitationController;
use App\Http\Middleware\AddInvitationToSession;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('front.home');

Route::middleware([
    AddInvitationToSession::class,
    'auth',
])
    ->get('team/invite/{invitation}', [AcceptInvitationController::class, 'show'])
    ->name('invitation.accept.show');
