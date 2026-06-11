<?php

declare(strict_types=1);

use App\Support\Flash\FlashLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

describe('browser requests', function () {
    test('a missing page renders the custom error page', function () {
        $this
            ->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertInertia(fn (Assert $page) => $page
                ->component('misc/Error')
                ->where('code', 404));
    });

    test('a 403 renders the custom error page', function () {
        Route::middleware('web')->get('/aborting-route', fn () => abort(403));

        $this
            ->get('/aborting-route')
            ->assertStatus(403)
            ->assertInertia(fn (Assert $page) => $page
                ->component('misc/Error')
                ->where('code', 403));
    });

    test('a 404 renders the custom error page', function () {
        Route::middleware('web')->get('/aborting-route', fn () => abort(404));

        $this
            ->get('/aborting-route')
            ->assertStatus(404)
            ->assertInertia(fn (Assert $page) => $page
                ->component('misc/Error')
                ->where('code', 404));
    });

    test('in production 500 aborted request renders the custom error page', function () {
        $this->app['env'] = 'production';

        Route::middleware('web')->get('/failing-route', fn () => abort(500));

        $this
            ->get('/failing-route')
            ->assertStatus(500)
            ->assertInertia(fn (Assert $page) => $page
                ->component('misc/Error')
                ->where('code', 500));
    });

    test('in production 503 aborted request renders the custom error page', function () {
        $this->app['env'] = 'production';

        Route::middleware('web')->get('/failing-route', fn () => abort(503));

        $this
            ->get('/failing-route')
            ->assertStatus(503)
            ->assertInertia(fn (Assert $page) => $page
                ->component('misc/Error')
                ->where('code', 503));
    });

    test('server errors keep the default response outside production', function () {
        Route::middleware('web')->get('/failing-route', fn () => abort(500));

        $response = $this->get('/failing-route');

        $response->assertInternalServerError();
        expect($response->getContent())->not->toContain('misc/Error');
    });

    test('throttled requests keep the default 429 response', function () {
        Route::middleware(['web', 'throttle:1,1'])->get('/throttled-route', fn () => 'ok');

        $this->get('/throttled-route')->assertOk();
        $this->get('/throttled-route')->assertTooManyRequests();
    });
});

describe('inertia requests', function () {
    test('a missing page renders the custom error page', function () {
        $this
            ->asInertia()
            ->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertJson([
                'component' => 'misc/Error',
                'props' => ['code' => 404],
            ]);
    });

    test('a failing request outside production redirects back with the exception message as a toast', function () {
        Route::middleware('web')->post('/failing-route', fn () => abort(400));

        $this
            ->asInertia()
            ->from('/previous-page')
            ->post('/failing-route')
            ->assertRedirect('/previous-page')
            ->assertToast('Something exploded', FlashLevel::ERROR);
    });

    test('validation errors are left untouched', function () {
        Route::middleware('web')->post('/validating-route', function (Request $request) {
            $request->validate(['email' => ['required']]);
        });

        $this
            ->asInertia()
            ->from('/previous-page')
            ->post('/validating-route')
            ->assertRedirect('/previous-page')
            ->assertInvalid('email');
    });

    test('throttled requests redirect back with a throttle validation error', function () {
        Route::middleware(['web', 'throttle:1,1'])->post('/throttled-route', fn () => 'ok');

        $this->asInertia()->post('/throttled-route')->assertOk();

        $this
            ->asInertia()
            ->from('/previous-page')
            ->post('/throttled-route')
            ->assertRedirect('/previous-page')
            ->assertInvalid(['throttle' => 'Too many requests. Please try again later.']);
    });
});
