<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Once;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // All requests in a test share one app instance (multiple $this->get()
        // calls in a feature test, or the browser test server), so request-scoped
        // once() memoization (e.g. currentTeam()) would leak from one request
        // into the next without this.
        Event::listen(RequestHandled::class, fn () => Once::flush());
    }

    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }

    public function asInertia(): self
    {
        return $this->withHeader('X-Inertia', 'true');
    }
}
