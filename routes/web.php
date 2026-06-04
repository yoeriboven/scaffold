<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('front.home');

Route::get('error/{code}', function (int $code) {
    abort_unless(in_array($code, [401, 403, 404, 419, 429, 500, 503], true), 404);

    return Inertia::render('misc/Error', ['code' => $code])
        ->toResponse(request())
        ->setStatusCode($code);
})->name('error');
